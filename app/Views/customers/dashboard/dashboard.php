<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<?php if (isset($message)): ?>
    <div class="alert alert-info"><?= esc($message) ?></div>
<?php endif; ?>

<h2>Bienvenido, <?= esc($user->username) ?></h2>
<p>Email: <?= esc($user->email) ?></p>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<?= $this->endSection() ?>