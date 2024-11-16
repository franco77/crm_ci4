<!DOCTYPE html>
<html>

<head>
    <title>Nuevo Ticket</title>
</head>

<body>
    <h2>Nuevo Ticket Creado</h2>
    <p><strong>Título:</strong> <?= esc($title) ?></p>
    <p><strong>Prioridad:</strong> <?= ucfirst($priority) ?></p>
    <p><strong>Descripción:</strong></p>
    <p><?= nl2br(esc($description)) ?></p>
</body>

</html>