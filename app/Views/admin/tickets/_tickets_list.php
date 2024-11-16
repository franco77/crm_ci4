<!-- app/Views/admin/tickets/partials/_tickets_list.php -->
<?php foreach ($tickets as $ticket): ?>
    <div class="card ticket-card priority-<?= $ticket['priority'] ?> mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="card-title mb-0">
                    #<?= $ticket['id'] ?> - <?= esc($ticket['title']) ?>
                </h5>
                <span class="badge bg-<?= getPriorityBadgeClass($ticket['priority']) ?>">
                    <?= ucfirst($ticket['priority']) ?>
                </span>
            </div>
            <p class="card-text text-muted"><?= character_limiter(esc($ticket['description']), 100) ?></p>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="status-indicator status-<?= $ticket['status'] ?>"></span>
                    <small class="text-muted"><?= ucfirst($ticket['status']) ?></small>
                    <small class="text-muted ms-2">
                        <i class="far fa-clock me-1"></i><?= timeAgo($ticket['created_at']) ?>
                    </small>
                    <?php if (!empty($ticket['assigned_to_name'])): ?>
                        <small class="text-muted ms-2">
                            <i class="far fa-user me-1"></i><?= esc($ticket['assigned_to_name']) ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="showReplyModal(<?= $ticket['id'] ?>)">
                        <i class="fas fa-reply me-1"></i>Responder
                    </button>
                    <div class="dropdown d-inline">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" onclick="showTicketDetails(<?= $ticket['id'] ?>)">
                                    <i class="fas fa-eye me-2"></i>Ver Detalles
                                </a>
                            </li>
                            <?php if (session()->get('is_admin')): ?>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="showAssignModal(<?= $ticket['id'] ?>)">
                                        <i class="fas fa-user-plus me-2"></i>Asignar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="showStatusModal(<?= $ticket['id'] ?>)">
                                        <i class="fas fa-edit me-2"></i>Cambiar Estado
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php if (empty($tickets)): ?>
    <div class="alert alert-info">
        No se encontraron tickets con los filtros seleccionados.
    </div>
<?php endif; ?>