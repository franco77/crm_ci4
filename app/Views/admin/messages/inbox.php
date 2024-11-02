<?= $this->extend("admin/layout/default") ?>
<?= $this->section("content") ?>
<style>
    .ck-editor__editable_inline {
        min-height: 200px;
    }

    .message-thread {
        margin-left: 20px;
        border-left: 2px solid #dee2e6;
        padding-left: 15px;
    }

    .message-item {
        position: relative;
    }

    .message-item::before {
        content: "";
        position: absolute;
        left: -15px;
        top: 50%;
        width: 10px;
        height: 2px;
        background: #dee2e6;
    }

    .reply-indicator {
        font-size: 0.8em;
        color: #6c757d;
        margin-bottom: 5px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0"><?= $title; ?></h5>
                    <button class="btn btn-primary" id="newMessage">+ Nuevo Mensaje</button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="list-group" id="messagesList">
                            <?php
                            $currentThread = null;
                            foreach ($messages as $message):
                                if ($message['parent_id'] === null):
                                    if ($currentThread !== null) echo '</div>';
                                    $currentThread = $message['id'];
                            ?>
                                    <div class="message-parent">
                                        <a href="#" class="list-group-item list-group-item-action"
                                            data-id="<?= $message['id'] ?>">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1"><?= esc($message['subject']) ?></h6>
                                                <small><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></small>
                                            </div>
                                            <small class="text-muted">De:
                                                <?= esc($message['sender_first_name'] . ' ' . $message['sender_last_name']) ?></small>
                                        </a>
                                    <?php else: ?>
                                        <div class="message-thread">
                                            <div class="message-item">
                                                <a href="#" class="list-group-item list-group-item-action"
                                                    data-id="<?= $message['id'] ?>">
                                                    <div class="reply-indicator">↳ Respuesta</div>
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="mb-1"><?= esc($message['subject']) ?></h6>
                                                        <small><?= date('d/m/Y H:i', strtotime($message['created_at'])) ?></small>
                                                    </div>
                                                    <small class="text-muted">De:
                                                        <?= esc($message['sender_first_name'] . ' ' . $message['sender_last_name']) ?></small>
                                                </a>
                                            </div>
                                        </div>
                                <?php
                                endif;
                            endforeach;
                            if ($currentThread !== null) echo '</div>';
                                ?>
                                    </div>
                        </div>
                        <div class="col-md-8">
                            <div id="messageContent" class="border p-3">Seleccione un mensaje para ver el contenido aquí
                            </div>
                            <button class="btn btn-secondary mt-3" id="replyButton"
                                style="display: none;">Responder</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo mensaje -->
    <div class="modal fade" id="newMessageModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="messageForm">
                        <div class="mb-3">
                            <label>Para:</label>
                            <select class="select2 form-control" name="receiver_id" id="receiverSelect" required>
                                <option value="">Seleccionar destinatario</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= esc($user->id) ?>">
                                        <?= esc($user->first_name . ' ' . $user->last_name) ?> (<?= esc($user->email) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="error-receiver_id"></div>
                        </div>
                        <div class="mb-3">
                            <label>Asunto:</label>
                            <input type="text" class="form-control" name="subject" required>
                            <div class="invalid-feedback" id="error-subject"></div>
                        </div>
                        <div class="mb-3">
                            <label>Mensaje:</label>
                            <textarea class="form-control" id="ckeditor-editor" name="message" rows="7"
                                required></textarea>
                            <div class="invalid-feedback" id="error-message"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="sendMessage">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para la respuesta -->
    <div class="modal fade" id="replyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Responder al Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="replyForm">
                        <input type="hidden" name="parent_id" id="parentMessageId">
                        <input type="hidden" name="receiver_id" id="receiverId">
                        <div class="mb-3">
                            <label>Asunto:</label>
                            <input type="text" class="form-control" name="subject" id="replySubject" required>
                        </div>
                        <div class="mb-3">
                            <label>Mensaje:</label>
                            <textarea class="form-control" name="message" rows="7" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="sendReply">Enviar Respuesta</button>
                </div>
            </div>
        </div>
    </div>

    <?= $this->endSection() ?>

    <?= $this->section("js") ?>
    <script>
        $(document).ready(function() {
            const loadMessages = () => {
                $.get('<?= base_url('admin/messages/getMessages') ?>', function(messages) {
                    let html = '';
                    let currentThread = null;

                    messages.forEach(message => {
                        if (!message.parent_id) {
                            if (currentThread !== null) html += '</div>';
                            currentThread = message.id;
                            html += `
                        <div class="message-parent">
                            <a href="#" class="list-group-item list-group-item-action" data-id="${message.id}">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">${message.subject}</h6>
                                    <small>${new Date(message.created_at).toLocaleString()}</small>
                                </div>
                                <small class="text-muted">De: ${message.sender_first_name} ${message.sender_last_name}</small>
                            </a>`;
                        } else {
                            html += `
                        <div class="message-thread">
                            <div class="message-item">
                                <a href="#" class="list-group-item list-group-item-action" data-id="${message.id}">
                                    <div class="reply-indicator">↳ Respuesta</div>
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">${message.subject}</h6>
                                        <small>${new Date(message.created_at).toLocaleString()}</small>
                                    </div>
                                    <small class="text-muted">De: ${message.sender_first_name} ${message.sender_last_name}</small>
                                </a>
                            </div>
                        </div>`;
                        }
                    });

                    if (currentThread !== null) html += '</div>';
                    $('#messagesList').html(html);
                });
            };

            $('#receiverSelect').select2({
                dropdownParent: $('#newMessageModal'),
                placeholder: 'Buscar usuario...',
                allowClear: true,
                ajax: {
                    url: '<?= base_url('admin/messages/searchUsers') ?>',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        term: params.term
                    }),
                    processResults: data => ({
                        results: data.map(user => ({
                            id: user.id,
                            text: `${user.first_name} ${user.last_name} (${user.email})`
                        }))
                    }),
                    cache: true
                },
                minimumInputLength: 2
            });

            $('#newMessage').on('click', () => $('#newMessageModal').modal('show'));

            $('#sendMessage').on('click', function() {
                $.post('<?= base_url('admin/messages/send') ?>', $('#messageForm').serialize(), function(
                    response) {
                    $('#messageForm .invalid-feedback').text('');
                    $('#messageForm .form-control').removeClass('is-invalid');

                    if (response.status === 'success') {
                        $('#newMessageModal').modal('hide');
                        $('#messageForm')[0].reset();
                        $('#receiverSelect').val(null).trigger('change');
                        loadMessages();
                        toastr.success(response.message);
                    } else if (response.errors) {
                        $.each(response.errors, function(field, error) {
                            $(`#error-${field}`).text(error).show();
                            $(`[name="${field}"]`).addClass('is-invalid');
                        });
                    } else {
                        toastr.error(response.message);
                    }
                }, 'json').fail(() => toastr.error('Ocurrió un error al enviar el mensaje.'));
            });

            $(document).on('click', '.list-group-item', function(e) {
                e.preventDefault();
                const messageId = $(this).data('id');

                $.get(`<?= base_url('admin/messages/getMessage') ?>/${messageId}`, function(response) {
                    if (response.status === 'success') {
                        const message = response.data;
                        $('#messageContent').html(`
                    <h5>Asunto: ${message.subject}</h5>
                    <p><strong>De:</strong> ${message.sender_first_name} ${message.sender_last_name} (${message.sender_email})</p>
                    <p><strong>Enviado el:</strong> ${new Date(message.created_at).toLocaleString()}</p>
                    <hr>
                    <p>${message.message}</p>
                `);

                        $('#replyButton').show().data({
                            'parent-id': message.id,
                            'receiver-id': message.sender_id,
                            'subject': `Re: ${message.subject}`
                        });
                    } else {
                        $('#messageContent').html('<p>Error: No se pudo cargar el mensaje.</p>');
                        $('#replyButton').hide();
                    }
                }).fail(() => $('#messageContent').html('<p>Error: No se pudo cargar el mensaje.</p>'));
            });

            $('#replyButton').on('click', function() {
                $('#parentMessageId').val($(this).data('parent-id'));
                $('#receiverId').val($(this).data('receiver-id'));
                $('#replySubject').val($(this).data('subject'));
                $('#replyModal').modal('show');
            });

            $('#sendReply').on('click', function() {
                // Obtenemos los datos del formulario de respuesta
                const replyData = {
                    parent_id: $('#parentMessageId').val(),
                    receiver_id: $('#receiverId').val(),
                    subject: $('#replySubject').val(),
                    message: $('#replyForm textarea[name="message"]').val()
                };

                $.post('<?= base_url('admin/messages/reply') ?>', replyData, function(response) {
                    if (response.status === 'success') {
                        $('#replyModal').modal('hide');
                        $('#replyForm')[0].reset();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }, 'json').fail(() => toastr.error('Ocurrió un error al enviar la respuesta.'));
            });

            ClassicEditor.create(document.querySelector('#ckeditor-editor')).catch(error => console.error(error));
        });
    </script>
    <?= $this->endSection() ?>