<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vouchers Payrolls</title>
    <style>
    :root {
        --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    #invoice {
        padding: 10px;
        line-height: 1.3;
        font-family: var(--font-family-monospace);
    }

    .invoice {
        position: relative;
        background-color: #FFF;
        min-height: 680px;
        padding: 15px;
    }

    .invoice header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #3989c6;
    }

    .invoice .company-details {
        text-align: right;
    }

    .invoice .company-details .name {
        margin-top: 0;
        margin-bottom: 0;
    }

    .invoice .contacts {
        margin-bottom: 20px;
    }

    .invoice .invoice-to {
        text-align: left;
    }

    .invoice .invoice-details {
        text-align: right;
    }

    .invoice .invoice-details .invoice-id {
        margin-top: -10px;
        color: #3989c6;
    }

    .invoice main {
        padding-bottom: 50px;
    }

    .invoice main .thanks {
        margin-top: -100px;
        font-size: 1.2em;
        margin-bottom: 40px;
    }

    .invoice main .notices {
        padding-left: 6px;
        border-left: 6px solid #3989c6;
    }

    .invoice main .notices .notice {
        font-size: 1em;
    }

    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

    .invoice table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .invoice table th,
    .invoice table td {
        padding: 4px;
        background: #eee;
        border-bottom: 1px solid #fff;
    }

    .invoice table th {
        white-space: nowrap;
        font-weight: 400;
        font-size: 16px;
    }

    .invoice table td h3 {
        margin: 0;
        font-weight: 400;
        color: #3989c6;
        font-size: 1.2em;
    }

    .invoice table .qty,
    .invoice table .total,
    .invoice table .unit {
        text-align: right;
        font-size: 1em;
    }

    .invoice table .no {
        color: #fff;
        font-size: 1em;
        background: #3989c6;
    }

    .invoice table .total {
        background: #3989c6;
        color: #fff;
    }

    .invoice table tfoot td {
        background: 0 0;
        border-bottom: none;
        white-space: nowrap;
        text-align: right;
        padding: 5px 10px;
        font-size: 1em;
        border-top: 1px solid #aaa;
    }

    .invoice footer {
        width: 100%;
        text-align: center;
        color: #777;
        border-top: 1px solid #aaa;
        padding: 8px 0;
    }

    @media print {
        .invoice {
            font-size: 11px !important;
            overflow: hidden !important;
        }

        .invoice footer {
            position: absolute;
            bottom: 10px;
            page-break-after: always;
        }

        .invoice>div:last-child {
            page-break-before: always;
        }
    }
    </style>
</head>

<body>
    <div id="invoice">
        <div class="invoice overflow-auto">
            <div style="min-width: 600px">
                <header style="margin-top: -70px;">
                    <div class="row">
                        <div class="col">
                            <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" width="110" />
                        </div>
                        <div class="col company-details" style="margin-top: -70px;">
                            <h3 class="name"><?= setting('App.siteName'); ?></h3>
                            <div><?= setting('App.address'); ?></div>
                            <div><?= setting('App.phone'); ?></div>
                            <div><?= setting('App.email'); ?></div>
                        </div>
                    </div>
                </header>
                <main>
                    <div class="row contacts">
                        <div class="col invoice-to" style="margin-bottom: 35px;">
                            <div><?= lang('app.payrolls.information_employee') ?>:</div>
                            <div class="address"><?= lang('app.payrolls.name') ?>:
                                <?= $data['first_name'] . ' ' . $data['last_name'] ?></div>
                            <div class="address"><?= lang('app.payrolls.email') ?>: <?= $data['email'] ?></div>
                            <div class="email"><?= lang('app.payrolls.phone') ?>: <?= $data['phone_number'] ?></div>
                        </div>
                        <div class="col invoice-details" style="margin-bottom: 8px;">
                            <h4 class="invoice-id" style="margin-bottom: -1px;">VOUCHER # PAY-00<?= $data['id'] ?>
                            </h4>
                            <?php $dateRegis = date_create($data['created_at']) ?>
                            <div class="date"><?= lang('app.payrolls.pay_date') ?>:
                                <?= date_format($dateRegis, "Y-m-d") ?></div>
                            <div class="date"><?= lang('app.payrolls.pay_time') ?>:
                                <?= date_format($dateRegis, "H:i:s") ?></div>
                        </div>
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="text-right"><?= lang('app.payrolls.concept') ?></th>
                                    <th class="text-right"><?= lang('app.payrolls.amount') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="background: #6fa8dc; color:#fff; text-align:center;">1</td>
                                    <td class="unit" style="background: #266baa; color:#fff;">
                                        <?= lang('app.payrolls.gross_salary') ?></td>
                                    <td class="qty"><?= number_format($data['gross_salary'], 2) ?></td>
                                </tr>
                                <?php foreach ($deductions as $index => $deduction): ?>
                                <tr>
                                    <td style="background: #999999; color:#fff; text-align:center;"><?= $index + 2 ?>
                                    </td>
                                    <td class="unit" style="background: #5b5b5b; color:#fff;">
                                        <?= $deduction['description'] ?>
                                    </td>
                                    <?php $amountFix = ($deduction['amount'] / 100) * $data['gross_salary']; ?>
                                    <td class="qty"> - <?= number_format($amountFix, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td style="background: #999999; color:#fff; text-align:center;">
                                        <?= count($deductions) + 2 ?></td>
                                    <td class="unit" style="background: #5b5b5b; color:#fff;">
                                        <?= lang('app.payrolls.loan_deductions') ?></td>
                                    <td class="qty">- <?= number_format($data['loan_deductions'], 2) ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"></td>
                                    <td colspan="1"><?= lang('app.payrolls.total_deductions') ?></td>
                                    <td><?= number_format($data['deductfix'] + $data['loan_deductions'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="1"></td>
                                    <td colspan="1"><?= lang('app.payrolls.bonus') ?></td>
                                    <td><?= number_format($data['bonus'], 2) ?></td>
                                </tr>
                                <tr>
                                    <td colspan="1"></td>
                                    <td colspan="1" style="color: #266baa;">
                                        <b><?= lang('app.payrolls.net_salary') ?></b>
                                    </td>
                                    <td style="color: #266baa;"><b><?= number_format($data['net_salary'], 2) ?></b></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>

</html>