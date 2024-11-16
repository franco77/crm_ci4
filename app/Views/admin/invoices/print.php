<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    :root {
        --font-family: Arial, sans-serif;
        --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        --background-color: <?= setting('App.corporateColor') ?>;
        --color: #fff;
        --text-color: #333;
        --border-color: #e0e0e0;
        --highlight-color: #0e9bed;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        padding: 20px;
        font-family: var(--font-family);
        color: var(--text-color);
    }

    .invoice-container {
        background-color: #fff;
        padding: 25px;
        width: 98%;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 15px;
        margin-bottom: 25px;
    }

    .logo img {
        max-width: 100px;
    }

    .invoice-title h2 {
        font-size: 26px;
        font-weight: bold;
        color: var(--highlight-color);
    }

    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 25px;
    }

    .company-info,
    .client-info {
        width: 45%;
        font-size: 14px;
    }

    .company-info p,
    .client-info p {
        margin-bottom: 8px;
        color: #555;
    }

    .summary-table th,
    .summary-table td {
        font-size: 14px;
        padding: 10px;
        border: 1px solid var(--border-color);
    }

    .summary-table th {
        background-color: var(--background-color);
        color: var(--color);
        text-align: left;
    }

    .summary-table td {
        text-align: right;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .invoice-table th,
    .invoice-table td {
        padding: 12px;
        border: 1px solid var(--border-color);
        text-align: left;
    }

    .invoice-table th {
        background-color: var(--background-color);
        color: var(--color);
        font-weight: bold;
    }

    .invoice-table tbody tr:nth-child(even) {
        background-color: #f7f7f7;
    }

    .invoice-summary {
        margin-top: 20px;
        text-align: right;
    }

    footer {
        text-align: center;
        margin-top: 40px;
        color: #777;
        padding: 20px 0;
        font-size: 12px;
        border-top: 1px solid #ddd;
    }

    footer p {
        border-left: 5px solid var(--highlight-color);
        padding-left: 15px;
        display: inline-block;
    }
</style>

<body>
    <div class="invoice-container" style="min-width: 600px">
        <header>
            <div class="logo">
                <img src="<?= base_url('uploads/logo/') . setting('App.file'); ?>" width="110" />
            </div>
            <div class="invoice-title">
                <h2>Factura</h2>
            </div>
        </header>

        <section class="invoice-header">
            <div class="company-info" style="margin-bottom: 20px;">
                <p><?= setting('App.siteName'); ?></p>
                <p><?= setting('App.address'); ?></p>
                <p><?= setting('App.phone'); ?></p>
                <p><?= setting('App.email'); ?></p>
                <br>
                <table class="summary-table data-invoices">
                    <tr>
                        <th>Número</th>
                        <td><?= htmlspecialchars($data['uuid']) ?></td> <!-- Número de la factura -->
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($data['date_invoice']))) ?></td>
                        <!-- Fecha de la factura -->
                    </tr>
                </table>
            </div>

            <div class="client-info">
                <p><strong><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></strong></p>
                <!-- Nombre del cliente -->
                <p><?= htmlspecialchars($data['address']) ?></p> <!-- Dirección del cliente -->
                <p><?= htmlspecialchars($data['phone']) ?></p> <!-- Teléfono del cliente -->
                <p>NIF: <?= htmlspecialchars($data['ic']) ?></p> <!-- NIF del cliente -->
            </div>
        </section>

        <section class="invoice-details">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['details'] as $detail): ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['product_name']) ?></td>
                            <td><?= htmlspecialchars($detail['quantity']) ?></td>
                            <td><?= htmlspecialchars(number_format($detail['price'], 2)) ?>€</td>
                            <td><?= htmlspecialchars(number_format($detail['quantity'] * $detail['price'], 2)) ?>€</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="invoice-summary">
            <table class="summary-table">
                <tr>
                    <th>Base Imponible</th>
                    <td><?= htmlspecialchars(number_format($data['invoice_subtotal'], 2)) ?>€</td>
                    <!-- Base imponible -->
                </tr>
                <tr>
                    <th>IVA (<?= setting('App.vat') ?> %)</th>
                    <td><?= htmlspecialchars(number_format($data['tax'], 2)) ?>€</td> <!-- IVA -->
                </tr>
                <tr>
                    <th>Total</th>
                    <td><?= htmlspecialchars(number_format($data['invoice_total'], 2)) ?>€</td>
                    <!-- Total de la factura -->
                </tr>
            </table>
        </section>


    </div>
    <footer>
        <p><?= setting('App.footer_invoice') ?></p>
    </footer>
</body>

</html>