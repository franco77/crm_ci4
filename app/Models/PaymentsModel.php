<?php
// app/Models/PaymentModel.php
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
    protected $updatedField = ''; // No usamos updated_at en esta tabla

    public function makePayment($invoiceId, $walletId, $amount)
    {
        $db = \Config\Database::connect();
        $WalletsModel = new WalletsModel();

        $db->transStart();

        try {
            // Primero obtenemos el wallet actual para verificar fondos
            $currentWallet = $WalletsModel->find($walletId);
            if (!$currentWallet || $currentWallet['remaining_amount'] < $amount) {
                throw new \Exception('Fondos insuficientes en el wallet');
            }

            // Actualizamos el remaining_amount del wallet
            $newRemaining = $currentWallet['remaining_amount'] - $amount;
            $walletUpdated = $WalletsModel->update($walletId, [
                'remaining_amount' => $newRemaining
            ]);

            if (!$walletUpdated) {
                throw new \Exception('Error al actualizar el wallet');
            }

            // Preparamos los datos del pago
            $payment = [
                'invoice_id' => $invoiceId,
                'amount_paid' => $amount,
                'payment_date' => date('Y-m-d'),
                'payment_reference' => 'WAL-' . $walletId . '-' . time(),
                'paid_by' => 'wallet',
                'amount_usd' => $amount,
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Insertamos el pago
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
                'new_balance' => $newRemaining
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