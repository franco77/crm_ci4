<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>

<div class="row">
    <div class="col-xl-8">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title">
                            Order No - <span class="text-primary">#<?= $invoice['uuid'] ?? 'N/A' ?></span>
                        </div>
                        <div>
                            <span class="badge bg-primary-transparent">
                                Invoice Date:
                                <?= isset($invoice['date_invoice']) ? date('d M Y', strtotime($invoice['date_invoice'])) : 'N/A' ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table text-nowrap table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">Product Code</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoiceDetails as $detail): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <span class="avatar avatar-xl bg-light">
                                                        <img src="<?= base_url('uploads/products/' . ($detail['productImage'] ?? 'default_product.jpg')) ?>"
                                                            alt="<?= $detail['product_name'] ?>">
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="mb-1 fs-14 fw-semibold">
                                                        <a
                                                            href="javascript:void(0);"><?= $detail['fullProductName'] ?? 'Unknown Product' ?></a>
                                                    </div>
                                                    <div class="mb-1">
                                                        <span class="me-1">Vendor:</span><span
                                                            class="text-muted"><?= $detail['productVendor'] ?? 'N/A' ?></span>
                                                    </div>
                                                    <div class="mb-1">
                                                        <span class="me-1">Line:</span><span
                                                            class="text-muted"><?= $detail['productLine'] ?? 'N/A' ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><a href="javascript:void(0);"
                                                class="text-primary"><?= $detail['productCode'] ?? 'N/A' ?></a></td>
                                        <td>
                                            <span
                                                class="fs-15 fw-semibold">$<?= number_format($detail['price'] ?? 0, 2) ?></span>
                                        </td>
                                        <td><?= $detail['quantity'] ?? 0 ?></td>
                                        <td>$<?= number_format(($detail['price'] ?? 0) * ($detail['quantity'] ?? 0), 2) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td colspan="2">
                                            <div class="fw-semibold">Sub Total :</div>
                                        </td>
                                        <td>
                                            $<?= number_format($invoice['invoice_subtotal'] ?? 0, 2) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td colspan="2">
                                            <div class="fw-semibold">Tax :</div>
                                        </td>
                                        <td>
                                            $<?= number_format($invoice['tax'] ?? 0, 2) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                        <td colspan="2">
                                            <div class="fw-semibold">Total Price :</div>
                                        </td>
                                        <td>
                                            <span
                                                class="fs-16 fw-semibold">$<?= number_format($invoice['invoice_total'] ?? 0, 2) ?></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer border-top-0">
                        <div class="btn-list float-end">
                            <button class="btn btn-primary btn-wave btn-sm" onclick="javascript:window.print();"><i
                                    class="ri-printer-line me-1 align-middle d-inline-block"></i>Print</button>
                            <button class="btn btn-secondary btn-wave btn-sm"><i
                                    class="ri-share-forward-line me-1 align-middle d-inline-block"></i>Share
                                Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title">
                            User Details
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center border-bottom border-block-end-dashed p-3 flex-wrap">
                            <div class="me-2">
                                <span class="avatar avatar-lg avatar-rounded">
                                    <img src="<?= base_url('uploads/avatars/') . $client['avatar'] ?>" alt="">
                                </span>
                            </div>
                            <div class="flex-fill">
                                <p class="mb-0">
                                    <?= ($client['first_name'] ?? '') . ' ' . ($client['last_name'] ?? '') ?></p>
                                <p class="mb-0 text-muted fs-12"><?= $client['email'] ?? 'N/A' ?></p>
                            </div>
                            <div>
                                <span class="badge bg-primary-transparent">Client</span>
                            </div>
                        </div>
                        <div class="p-3 border-bottom border-block-end-dashed">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="fs-14 fw-semibold">Client Details :</span>
                            </div>
                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Phone :</span>
                                <?= $client['phone'] ?? 'N/A' ?></p>
                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Company :</span>
                                <?= $client['company'] ?? 'N/A' ?></p>

                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Direccion :</span>
                                <?= $client['address'] ?? 'N/A' ?></p>

                        </div>
                    </div>
                    <div class="card-footer">
                        <span class="text-muted"><?= $invoice['notes'] ?? 'No notes available' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<?= $this->endSection() ?>