<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<h1><?= esc($title) ?></h1>

<form action="<?= base_url('/customers/dashboard/editProfile') ?>" method="post">
    <label for="username">Nombre de Usuario:</label>
    <input type="text" name="username" value="<?= esc($user->username) ?>" id="username">

    <label for="email">Correo Electrónico:</label>
    <input type="email" name="email" value="<?= esc($user->email) ?>" id="email">

    <!-- Otros campos que quieras editar -->

    <button type="submit">Guardar Cambios</button>
</form>
<?= $this->endSection() ?>
<?= $this->section('js') ?>

<?= $this->endSection() ?>