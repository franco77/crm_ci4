<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table      = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['invoice_id', 'amount_paid', 'payment_date', 'payment_reference', 'paid_by', 'amount_usd', 'created_at'];
    protected $searchFields = ['invoice_id', 'amount_paid', 'payment_date', 'payment_reference', 'paid_by'];


    public function filter($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
    {
        $builder = $this->table($this->table);

        // Validar que $limit y $start sean enteros
        $limit = is_numeric($limit) ? (int)$limit : 10;  // Por defecto, limit = 10
        $start = is_numeric($start) ? (int)$start : 0;   // Por defecto, start = 0

        // Asegurarse de que $orderField y $orderDir no estén vacíos y sean válidos
        $orderField = in_array($orderField, $this->allowedFields) ? $orderField : 'amount_paid';
        $orderDir = in_array(strtolower($orderDir), ['asc', 'desc']) ? $orderDir : 'asc';

        // Aplicar filtro de búsqueda
        if ($search) {
            $builder->groupStart();
            foreach ($this->searchFields as $i => $column) {
                if ($i === 0) {
                    $builder->like($column, $search);
                } else {
                    $builder->orLike($column, $search);
                }
            }
            $builder->groupEnd();
        }



        // Muestra datos menores o iguales a las primeras 6 columnas.

        $builder->select('id, invoice_id, amount_paid, payment_date, payment_reference, paid_by')
            ->orderBy($orderField, $orderDir)
            ->limit($limit, $start);

        $query = $builder->get()->getResultArray();

        foreach ($query as $index => $value) {
            $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $query[$index][$this->primaryKey] . '">';
            $query[$index]['column_action'] = '<button class="btn btn-sm btn-xs btn-success form-action" item-id="' . $query[$index][$this->primaryKey] . '" purpose="detail"><i class="bi bi-eye"></i></button>';
        }
        return $query;
    }

    public function countTotal()
    {
        return $this->table($this->table)
            ->countAll();
    }

    public function countFilter($search)
    {
        $builder = $this->table($this->table);

        $i = 0;
        foreach ($this->searchFields as $column) {
            if ($search) {
                if ($i == 0) {
                    $builder->groupStart()
                        ->like($column, $search);
                } else {
                    $builder->orLike($column, $search);
                }

                if (count($this->searchFields) - 1 == $i) $builder->groupEnd();
            }
            $i++;
        }

        return $builder->countAllResults();
    }



    public function getInvoiceTotals($search = null, $limit = null, $start = null, $orderField = null, $orderDir = null)
    {
        // Validar y asignar parámetros
        $limit = is_numeric($limit) && $limit > 0 ? (int) $limit : 10;
        $start = is_numeric($start) && $start >= 0 ? (int) $start : 0;
        $orderField = in_array($orderField, ['invoice_id', 'total_amount', 'last_payment_date']) ? $orderField : 'invoice_id';
        $orderDir = in_array(strtolower($orderDir), ['asc', 'desc']) ? strtolower($orderDir) : 'desc';

        $builder = $this->table($this->table);

        // Seleccionar los campos necesarios y aplicar funciones agregadas
        $builder->select('
        invoice_id,
        SUM(amount_paid) AS total_amount,
        COUNT(*) AS payment_count,
        MAX(payment_date) AS last_payment_date,
        GROUP_CONCAT(DISTINCT paid_by) AS paid_by_users
    ');

        // Condicional de búsqueda
        if (!empty($search)) {
            $builder->groupStart()
                ->like('invoice_id', $search)
                ->orLike('paid_by', $search)
                ->groupEnd();
        }

        // Agrupar, ordenar y limitar resultados
        $builder->groupBy('invoice_id')
            ->orderBy($orderField, $orderDir)
            ->limit($limit, $start);

        // Obtener resultados
        $query = $builder->get()->getResultArray();

        // Generar columnas adicionales con HTML escapado para evitar XSS
        foreach ($query as $index => $value) {
            $invoiceId = htmlspecialchars($value['invoice_id'], ENT_QUOTES, 'UTF-8');
            $query[$index]['column_bulk'] = '<input type="checkbox" class="bulk-item" value="' . $invoiceId . '">';
            $query[$index]['column_action'] = '<button class="btn btn-sm btn-success view-payments" data-invoice="' . $invoiceId . '" data-bs-toggle="tooltip" title="Ver pagos"> <i class="bi bi-eye"></i></button>';
        }

        return $query;
    }


    // Nuevo método para contar el total de facturas únicas
    public function countTotalInvoices()
    {
        return $this->table($this->table)
            ->select('invoice_id')  // Selecciona solo la columna que necesitas para contar
            ->distinct()            // Evita duplicados en la columna seleccionada
            ->countAllResults();    // Cuenta los resultados únicos
    }


    // Nuevo método para contar facturas filtradas
    public function countFilteredInvoices($search)
    {
        return $this->table($this->table)
            ->select('invoice_id')  // Selecciona solo la columna que necesitas para contar
            ->distinct()            // Evita duplicados en la columna seleccionada
            ->countAllResults();    // Cuenta los resultados únicos

        if ($search) {
            $builder->groupStart()
                ->like('invoice_id', $search)
                ->orLike('paid_by', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }



    public function makePayment($invoiceId, $walletId, $amount)
    {
        $db = \Config\Database::connect();
        $WalletsModel = new WalletsModel();
        $InvoiceModel = new InvoicesModel();

        $db->transStart();

        try {
            // 1. Verificar y obtener la factura
            $invoice = $InvoiceModel->find($invoiceId);
            if (!$invoice) {
                throw new \Exception('Factura no encontrada');
            }

            if ($amount > $invoice['amount_due']) {
                throw new \Exception('El monto del pago excede el saldo pendiente de la factura');
            }

            // 2. Verificar el wallet
            $currentWallet = $WalletsModel->find($walletId);
            if (!$currentWallet || $currentWallet['remaining_amount'] < $amount) {
                throw new \Exception('Fondos insuficientes en el wallet');
            }

            // 3. Actualizar el remaining_amount del wallet
            $newRemaining = $currentWallet['remaining_amount'] - $amount;
            $walletUpdated = $WalletsModel->update($walletId, [
                'remaining_amount' => $newRemaining
            ]);

            if (!$walletUpdated) {
                throw new \Exception('Error al actualizar el wallet');
            }

            // 4. Actualizar los montos de la factura
            $invoiceResult = $InvoiceModel->updatePaymentAmounts($invoiceId, $amount);

            // 5. Registrar el pago
            $payment = [
                'invoice_id' => $invoiceId,
                'amount_paid' => $amount,
                'payment_date' => date('Y-m-d'),
                'payment_reference' => 'WAL-' . $walletId . '-' . time(),
                'paid_by' => 'wallet',
                'amount_usd' => $amount,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if (!$this->insert($payment)) {
                throw new \Exception('Error al registrar el pago');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción');
            }

            return [
                'success' => true,
                'message' => 'Pago registrado exitosamente',
                'payment_id' => $this->insertID(),
                'wallet_balance' => $newRemaining,
                'invoice' => [
                    'id' => $invoiceId,
                    'amount_paid' => $invoiceResult['new_amount_paid'],
                    'amount_due' => $invoiceResult['new_amount_due']
                ]
            ];
        } catch (\Exception $e) {
            $db->transRollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}