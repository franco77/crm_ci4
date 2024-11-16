<!DOCTYPE html>
<html>

<head>
    <title>Nueva Respuesta en Ticket</title>
</head>

<body>
    <h2>Nueva Respuesta en Ticket #<?= $ticket['id'] ?></h2>
    <p><strong>Ticket:</strong> <?= esc($ticket['title']) ?></p>
    <p><strong>Respuesta:</strong></p>
    <p><?= nl2br(esc($response['response'])) ?></p>
</body>

</html>