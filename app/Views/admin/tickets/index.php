<?= $this->extend("admin/layout/default") ?>

<?= $this->section("content") ?>
<link rel="stylesheet" href="<?= base_url('admin/assets/css/ticket.css') ?>">


<div>
    <!-- Dashboard Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary ticket-stats">
                <div class="card-body">
                    <h6 class="card-title">Total Tickets</h6>
                    <h2 class="mb-0"><?= $stats['total'] ?></h2>
                    <?php
                    $yesterdayTotal = isset($stats['yesterday_total']) ? $stats['yesterday_total'] : 0;
                    $percentChange = $yesterdayTotal > 0 ? (($stats['total'] - $yesterdayTotal) / $yesterdayTotal) * 100 : 0;
                    ?>
                    <small><?= number_format($percentChange, 1) ?>% más que ayer</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning ticket-stats">
                <div class="card-body">
                    <h6 class="card-title">Pendientes</h6>
                    <h2 class="mb-0"><?= $stats['pending'] ?></h2>
                    <small><?= $stats['new_today'] ?? 0 ?> tickets nuevos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success ticket-stats">
                <div class="card-body">
                    <h6 class="card-title">Resueltos</h6>
                    <h2 class="mb-0"><?= $stats['resolved'] ?></h2>
                    <small>Hoy: <?= $stats['resolved_today'] ?? 0 ?> tickets</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info ticket-stats">
                <div class="card-body">
                    <h6 class="card-title">Tiempo Promedio</h6>
                    <h2 class="mb-0"><?= number_format($stats['average_time'], 1) ?>h</h2>
                    <small>Meta: 2h</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="search-container mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="searchTickets" placeholder="Buscar tickets...">
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="statusFilter">
                    <option value="">Estado</option>
                    <option value="open">Abierto</option>
                    <option value="in_progress">En Proceso</option>
                    <option value="resolved">Resuelto</option>
                    <option value="closed">Cerrado</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="priorityFilter">
                    <option value="">Prioridad</option>
                    <option value="high">Alta</option>
                    <option value="medium">Media</option>
                    <option value="low">Baja</option>
                </select>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary me-2" id="applyFilters">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createTicketModal">
                    <i class="fas fa-plus me-1"></i>Nuevo Ticket
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-md-8">
            <div id="ticketsList">
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
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="showReplyModal(<?= $ticket['id'] ?>)">
                                        <i class="fas fa-reply me-1"></i>Responder
                                    </button>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    onclick="showTicketDetails(<?= $ticket['id'] ?>)">
                                                    <i class="fas fa-eye me-2"></i>Ver Detalles
                                                </a>
                                            </li>
                                            <?php if (!isAdmin()) : ?>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="showAssignModal(<?= $ticket['id'] ?>)">
                                                        <i class="fas fa-user-plus me-2"></i>Asignar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="showStatusModal(<?= $ticket['id'] ?>)">
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
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="timeline-item">
                                <small class="text-muted"><?= timeAgo($activity['created_at']) ?></small>
                                <p class="mb-0"><?= esc($activity['description']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal fade" id="createTicketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="newTicketForm" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridad</label>
                        <select name="priority" class="form-select" required>
                            <option value="">Seleccionar prioridad</option>
                            <option value="low">Baja</option>
                            <option value="medium">Media</option>
                            <option value="high">Alta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Archivos Adjuntos</label>
                        <input type="file" name="attachments[]" class="form-control" multiple>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitTicket()">Crear Ticket</button>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Responder Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="replyForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="ticket_id" id="replyTicketId">
                    <div class="mb-3">
                        <label class="form-label">Mensaje</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <?php if (session()->get('is_admin')): ?>
                        <div class="mb-3">
                            <label class="form-label">Actualizar Estado</label>
                            <select name="status" class="form-select">
                                <option value="">Mantener estado actual</option>
                                <option value="open">Abierto</option>
                                <option value="in_progress">En Proceso</option>
                                <option value="resolved">Resuelto</option>
                                <option value="closed">Cerrado</option>
                            </select>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitReply()">Enviar Respuesta</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("js") ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<script>
    // Helpers

    // Form Submissions
    async function submitTicket() {
        const form = document.getElementById('newTicketForm');
        const formData = new FormData(form);

        try {
            const response = await fetch('<?= base_url('admin/tickets/create') ?>', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert('Error al crear el ticket: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    }

    async function submitReply() {
        const form = document.getElementById('replyForm');
        const ticketId = document.getElementById('replyTicketId').value;
        const formData = new FormData(form);

        try {
            const response = await fetch(`<?= base_url('admin/tickets/reply') ?>/${ticketId}`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert('Error al enviar la respuesta: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    }

    // Modal Handlers
    function showReplyModal(ticketId) {
        document.getElementById('replyTicketId').value = ticketId;
        new bootstrap.Modal(document.getElementById('replyModal')).show();
    }

    // Search and Filters
    async function applyFilters() {
        // Agregar al inicio de la función applyFilters:
        document.getElementById('ticketsList').innerHTML =
            '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        const status = document.getElementById('statusFilter').value;
        const priority = document.getElementById('priorityFilter').value;
        const search = document.getElementById('searchTickets').value;

        try {
            const response = await fetch(
                `<?= base_url('admin/tickets/index') ?>?status=${encodeURIComponent(status)}&priority=${encodeURIComponent(priority)}&search=${encodeURIComponent(search)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.text();
            document.getElementById('ticketsList').innerHTML = data;
        } catch (error) {
            console.error('Error:', error);
            alert('Error al aplicar los filtros');
        }
    }

    // Event listeners
    document.getElementById('applyFilters').addEventListener('click', applyFilters);

    // Opcional: Aplicar filtros al escribir en el campo de búsqueda (con debounce)
    let searchTimeout;
    document.getElementById('searchTickets').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 300);
    });

    //

    // Funciones para el manejo de estados y asignaciones
    async function showStatusModal(ticketId) {
        const modal = `
        <div class="modal fade" id="statusModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Estado del Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="statusForm">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label class="form-label">Nuevo Estado</label>
                                <select name="status" class="form-select" required>
                                    <option value="open">Abierto</option>
                                    <option value="in_progress">En Proceso</option>
                                    <option value="resolved">Resuelto</option>
                                    <option value="closed">Cerrado</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="updateStatus(${ticketId})">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', modal);
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }

    async function updateStatus(ticketId) {
        const form = document.getElementById('statusForm');
        const formData = new FormData(form);

        try {
            const response = await fetch(`<?= base_url('admin/tickets/updateStatus') ?>/${ticketId}`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert('Error al actualizar el estado: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    }

    async function showAssignModal(ticketId) {
        try {
            const response = await fetch('<?= base_url('admin/users/list') ?>');
            const users = await response.json();

            const modal = `
            <div class="modal fade" id="assignModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Asignar Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="assignForm">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label">Asignar a</label>
                                    <select name="user_id" class="form-select" required>
                                        <option value="">Seleccionar usuario</option>
                                        ${users.map(user => `
                                            <option value="${user.id}">${user.username}</option>
                                        `).join('')}
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="assignTicket(${ticketId})">Asignar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

            document.body.insertAdjacentHTML('beforeend', modal);
            new bootstrap.Modal(document.getElementById('assignModal')).show();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar la lista de usuarios');
        }
    }

    async function assignTicket(ticketId) {
        const form = document.getElementById('assignForm');
        const formData = new FormData(form);

        try {
            const response = await fetch(`<?= base_url('admin/tickets/assign') ?>/${ticketId}`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                location.reload();
            } else {
                alert('Error al asignar el ticket: ' + data.error);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        }
    }

    async function showTicketDetails(ticketId) {
        try {
            const response = await fetch(`<?= base_url('admin/tickets/details/') ?>${ticketId}`);
            if (!response.ok) {
                throw new Error('Error al cargar los detalles del ticket');
            }
            const ticket = await response.json();

            const modal = `
            <div class="modal fade" id="detailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            
            <div class="modal-header border-bottom-0 bg-primary bg-gradient text-white">
                <h5 class="modal-title">
                    <i class="fas fa-ticket-alt me-2"></i>
                    Ticket #${ticket.id}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            
            <div class="modal-body p-4">
                
                <div class="ticket-details mb-4">
                    <h5 class="fw-bold mb-3">${escapeHtml(ticket.title)}</h5>
                    <p class="text-muted mb-4">${escapeHtml(ticket.description)}</p>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Estado y Prioridad</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-circle-notch me-2 text-primary"></i>
                                            <span class="fw-semibold me-2">Estado:</span>
                                            <span class="badge bg-${getStatusBadgeClass(ticket.status)} rounded-pill">${ticket.status}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-flag me-2 text-primary"></i>
                                            <span class="fw-semibold me-2">Prioridad:</span>
                                            <span class="badge bg-${getPriorityBadgeClass(ticket.priority)} rounded-pill">${ticket.priority}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-3 text-muted">Asignación</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <span class="fw-semibold me-2">Creado por:</span>
                                            <span>${escapeHtml(ticket.created_by)}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-check me-2 text-primary"></i>
                                            <span class="fw-semibold me-2">Asignado a:</span>
                                            <span>${escapeHtml(ticket.assigned_to || 'No asignado')}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="attachments mb-4">
                    <h6 class="d-flex align-items-center mb-3">
                        <i class="fas fa-paperclip me-2 text-primary"></i>
                        Archivos Adjuntos
                    </h6>
                    <div class="attachment-list">
                        ${ticket.attachments.length > 0 ? 
                            ticket.attachments.map(file => `
                                <div class="attachment-item p-3 mb-2 bg-light rounded-3 hover-shadow transition">
                                    <a href="${file.file_path}" class="text-decoration-none d-flex align-items-center">
                                        <i class="fas fa-file me-2 text-primary"></i>
                                        <span class="text-dark">${escapeHtml(file.file_name)}</span>
                                    </a>
                                </div>
                            `).join('') : 
                            '<p class="text-muted fst-italic">No hay archivos adjuntos</p>'
                        }
                    </div>
                </div>

                
                <div class="replies">
                    <h6 class="d-flex align-items-center mb-3">
                        <i class="fas fa-comments me-2 text-primary"></i>
                        Historial de Respuestas
                    </h6>
                    <div class="reply-list">
                        ${ticket.replies.map(reply => `
                            <div class="reply-item mb-3 p-4 bg-light rounded-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2 bg-primary text-white">
                                            ${reply.username.charAt(0).toUpperCase()}
                                        </div>
                                        <div>
                                            <strong class="d-block">${escapeHtml(reply.username)}</strong>
                                            <small class="text-muted">${formatDate(reply.created_at)}</small>
                                        </div>
                                    </div>
                                </div>
                                <p class="mb-0 reply-message">${escapeHtml(reply.message)}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>

            
            <div class="modal-footer border-top-0 bg-light">
                <button type="button" class="btn btn-primary btn-lg" onclick="showReplyModal(${ticket.id})">
                    <i class="fas fa-reply me-2"></i>
                    Responder
                </button>
                <button type="button" class="btn btn-light btn-lg" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
        `;

            // Remover modal anterior si existe
            const existingModal = document.getElementById('detailsModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Agregar el nuevo modal al DOM
            document.body.insertAdjacentHTML('beforeend', modal);

            // Mostrar el modal
            const modalInstance = new bootstrap.Modal(document.getElementById('detailsModal'));
            modalInstance.show();
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar los detalles del ticket');
        }
    }

    // Función auxiliar para escapar HTML
    function escapeHtml(unsafe) {
        return unsafe ?
            unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;") :
            '';
    }

    // Funciones auxiliares
    function getStatusBadgeClass(status) {
        const classes = {
            'open': 'primary',
            'in_progress': 'warning',
            'resolved': 'success',
            'closed': 'secondary'
        };
        return classes[status] || 'secondary';
    }

    function getPriorityBadgeClass(priority) {
        const classes = {
            'high': 'danger',
            'medium': 'warning',
            'low': 'success'
        };
        return classes[priority] || 'secondary';
    }

    function formatDate(dateString) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }
</script>
<?= $this->endSection() ?>