<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'invoice_id',
        'amount_paid',
        'payment_date',
        'payment_reference',
        'paid_by',
        'amount_usd',
        'created_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

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