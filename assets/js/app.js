document.addEventListener('DOMContentLoaded', function () {
    initBoardModal();
    initTaskModal();
    initNotificationModal();
    initUserModal();
    initTaskDragAndDrop();
});

function initBoardModal() {
    var modalElement = document.getElementById('boardModal');

    if (!modalElement) {
        return;
    }

    var form = document.getElementById('boardForm');
    var title = modalElement.querySelector('.modal-title');
    var memberSelect = document.getElementById('board_member_ids');

    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mode = button ? button.getAttribute('data-mode') : 'create';

        form.reset();
        Array.prototype.forEach.call(memberSelect.options, function (option) {
            option.selected = false;
        });

        if (mode === 'edit' && button) {
            title.textContent = 'Cập nhật board';
            document.getElementById('board_id').value = button.getAttribute('data-board-id') || '';
            document.getElementById('board_name').value = button.getAttribute('data-board-name') || '';
            document.getElementById('board_description').value = button.getAttribute('data-board-description') || '';
            document.getElementById('board_start_date').value = button.getAttribute('data-board-start') || '';
            document.getElementById('board_end_date').value = button.getAttribute('data-board-end') || '';

            var members = (button.getAttribute('data-board-members') || '').split(',');
            Array.prototype.forEach.call(memberSelect.options, function (option) {
                option.selected = members.indexOf(option.value) !== -1;
            });
        } else {
            title.textContent = 'Tạo board mới';
            document.getElementById('board_id').value = '';
        }
    });
}

function initTaskModal() {
    var modalElement = document.getElementById('taskModal');

    if (!modalElement) {
        return;
    }

    var form = document.getElementById('taskForm');
    var title = modalElement.querySelector('.modal-title');
    var notice = document.getElementById('memberEditNotice');
    var managerFields = modalElement.querySelectorAll('.manager-only-field');

    Array.prototype.forEach.call(managerFields, function (field) {
        field.setAttribute('data-original-required', field.required ? '1' : '0');
    });

    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mode = button ? button.getAttribute('data-mode') : 'create';
        var canManage = button ? button.getAttribute('data-can-manage') === '1' : true;

        form.reset();
        toggleManagerFields(managerFields, true);
        notice.classList.add('d-none');

        document.getElementById('task_board_id').value =
            (button && button.getAttribute('data-task-board-id')) ||
            (button && button.getAttribute('data-board-id')) ||
            document.getElementById('task_board_id').value;

        if (mode === 'edit' && button) {
            title.textContent = canManage ? 'Cập nhật task' : 'Thành viên cập nhật task';
            document.getElementById('task_id').value = button.getAttribute('data-task-id') || '';
            document.getElementById('task_title').value = button.getAttribute('data-task-title') || '';
            document.getElementById('task_description').value = button.getAttribute('data-task-description') || '';
            document.getElementById('task_assignee_id').value = button.getAttribute('data-task-assignee') || '';
            document.getElementById('task_status_id').value = button.getAttribute('data-task-status') || '';
            document.getElementById('task_priority').value = button.getAttribute('data-task-priority') || 'Trung bình';
            document.getElementById('task_deadline').value = button.getAttribute('data-task-deadline') || '';
            document.getElementById('task_progress_percent').value = button.getAttribute('data-task-progress') || 0;
            document.getElementById('task_note').value = button.getAttribute('data-task-note') || '';

            if (!canManage) {
                notice.classList.remove('d-none');
                toggleManagerFields(managerFields, false);
            }
        } else {
            title.textContent = 'Tạo task mới';
            document.getElementById('task_id').value = '';
            document.getElementById('task_progress_percent').value = 0;
        }
    });
}

function initNotificationModal() {
    var modalElement = document.getElementById('notificationModal');

    if (!modalElement) {
        return;
    }

    var form = document.getElementById('notificationForm');
    var title = modalElement.querySelector('.modal-title');

    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mode = button ? button.getAttribute('data-mode') : 'create';

        form.reset();

        if (mode === 'edit' && button) {
            title.textContent = 'Cập nhật thông báo';
            document.getElementById('notification_id').value = button.getAttribute('data-notification-id') || '';
            document.getElementById('notification_title').value = button.getAttribute('data-notification-title') || '';
            document.getElementById('notification_content').value = button.getAttribute('data-notification-content') || '';
            document.getElementById('notification_priority').value = button.getAttribute('data-notification-priority') || 'Trung bình';
        } else {
            title.textContent = 'Tạo thông báo mới';
            document.getElementById('notification_id').value = '';
        }
    });
}

function initUserModal() {
    var modalElement = document.getElementById('userModal');

    if (!modalElement) {
        return;
    }

    var form = document.getElementById('userForm');
    var title = modalElement.querySelector('.modal-title');
    var password = document.getElementById('user_password');

    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mode = button ? button.getAttribute('data-mode') : 'create';

        form.reset();

        if (mode === 'edit' && button) {
            title.textContent = 'Cập nhật tài khoản';
            document.getElementById('user_id').value = button.getAttribute('data-user-id') || '';
            document.getElementById('user_full_name').value = button.getAttribute('data-user-full-name') || '';
            document.getElementById('user_email').value = button.getAttribute('data-user-email') || '';
            document.getElementById('user_username').value = button.getAttribute('data-user-username') || '';
            document.getElementById('user_role').value = button.getAttribute('data-user-role') || 'member';
            document.getElementById('user_department').value = button.getAttribute('data-user-department') || '';
            password.required = false;
            password.placeholder = 'Để trống nếu không đổi';
        } else {
            title.textContent = 'Tạo tài khoản mới';
            document.getElementById('user_id').value = '';
            document.getElementById('user_role').value = 'member';
            password.required = true;
            password.placeholder = '';
        }
    });
}

function toggleManagerFields(fields, enabled) {
    Array.prototype.forEach.call(fields, function (field) {
        field.disabled = !enabled;
        field.required = enabled && field.getAttribute('data-original-required') === '1';
    });
}

function initTaskDragAndDrop() {
    var draggableCards = document.querySelectorAll('.task-card[draggable="true"]');
    var dropzones = document.querySelectorAll('.task-list');
    var activeCard = null;

    draggableCards.forEach(function (card) {
        if (card.getAttribute('data-editable') !== '1') {
            return;
        }

        card.addEventListener('dragstart', function () {
            activeCard = card;
            card.classList.add('dragging');
        });

        card.addEventListener('dragend', function () {
            card.classList.remove('dragging');
            activeCard = null;
        });
    });

    dropzones.forEach(function (zone) {
        zone.addEventListener('dragover', function (event) {
            if (!activeCard) {
                return;
            }

            event.preventDefault();
            zone.classList.add('drag-over');
        });

        zone.addEventListener('dragleave', function () {
            zone.classList.remove('drag-over');
        });

        zone.addEventListener('drop', function (event) {
            if (!activeCard) {
                return;
            }

            event.preventDefault();
            zone.classList.remove('drag-over');

            var formData = new FormData();
            formData.append('task_id', activeCard.getAttribute('data-task-id'));
            formData.append('status_id', zone.getAttribute('data-status-id'));

            fetch('actions/task_move.php', {
                method: 'POST',
                body: formData,
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {
                    if (!result.success) {
                        alert(result.message || 'Không thể cập nhật trạng thái task.');
                        return;
                    }

                    window.location.reload();
                })
                .catch(function () {
                    alert('Có lỗi xảy ra khi kéo thả task.');
                });
        });
    });
}
