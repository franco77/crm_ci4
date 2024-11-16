<?php foreach ($tickets as $ticket): ?>
    <div class="card ticket-card <?= 'priority-' . strtolower($ticket['priority']) ?> mb-3">
        <div class="card-body">
            <h5 class="card-title"><?= esc($ticket['title']) ?></h5>
            <span
                class="badge bg-<?= $ticket['priority'] === 'high' ? 'danger' : ($ticket['priority'] === 'medium' ? 'warning' : 'success') ?>">
                <?= ucfirst($ticket['priority']) ?>
            </span>
            <p class="card-text"><?= esc($ticket['description']) ?></p>
        </div>
    </div>
<?php endforeach; ?>