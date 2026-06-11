document.addEventListener('DOMContentLoaded', function () {
    initBoardModal();
    initTaskModal();
    initNotificationModal();
    initUserModal();
    initProfilePasswordValidation();
    initResetPasswordValidation();
    initTaskDragAndDrop();
});

function sequentialPasswordError(value) {
    if (value.length < 6) {
        return 'Mật khẩu phải có tối thiểu 6 ký tự.';
    }

    if (!/[A-Z]/.test(value)) {
        return 'Mật khẩu phải có ít nhất 1 chữ cái viết hoa.';
    }

    if (!/[a-zA-Z]/.test(value) || !/[0-9]/.test(value)) {
        return 'Mật khẩu phải có cả chữ và số.';
    }

    if (/\s/.test(value)) {
        return 'Mật khẩu không được chứa khoảng trắng.';
    }

    if (!/[^a-zA-Z0-9\s]/.test(value)) {
        return 'Mật khẩu phải có ít nhất 1 ký tự đặc biệt.';
    }

    return '';
}

function renderSequentialFieldError(input, errorDiv, message) {
    if (message === '') {
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
        input.classList.remove('is-invalid');
        return;
    }

    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    input.classList.add('is-invalid');
}

function initSequentialPasswordForm(options) {
    var form = document.getElementById(options.formId);

    if (!form) {
        return;
    }

    var password = document.getElementById(options.passwordId);
    var passwordError = document.getElementById(options.passwordErrorId);
    var confirmation = document.getElementById(options.confirmationId);
    var confirmationError = document.getElementById(options.confirmationErrorId);

    password.addEventListener('input', function () {
        if (password.value === '') {
            renderSequentialFieldError(password, passwordError, '');
        } else {
            renderSequentialFieldError(password, passwordError, sequentialPasswordError(password.value));
        }

        if (confirmation.value !== '') {
            var message = password.value === confirmation.value ? '' : 'Xác nhận mật khẩu không khớp.';
            renderSequentialFieldError(confirmation, confirmationError, message);
        }
    });

    confirmation.addEventListener('input', function () {
        if (confirmation.value === '') {
            renderSequentialFieldError(confirmation, confirmationError, '');
            return;
        }

        var message = password.value === confirmation.value ? '' : 'Xác nhận mật khẩu không khớp.';
        renderSequentialFieldError(confirmation, confirmationError, message);
    });

    form.addEventListener('submit', function (event) {
        if (password.value === '' && !options.required && confirmation.value === '') {
            renderSequentialFieldError(password, passwordError, '');
            renderSequentialFieldError(confirmation, confirmationError, '');
            return;
        }

        if (password.value === '') {
            event.preventDefault();
            renderSequentialFieldError(password, passwordError, 'Vui lòng nhập mật khẩu mới.');
            password.focus();
            return;
        }

        var message = sequentialPasswordError(password.value);
        if (message !== '') {
            event.preventDefault();
            renderSequentialFieldError(password, passwordError, message);
            password.focus();
            return;
        }
        renderSequentialFieldError(password, passwordError, '');

        if (confirmation.value === '') {
            event.preventDefault();
            renderSequentialFieldError(confirmation, confirmationError, 'Vui lòng xác nhận mật khẩu mới.');
            confirmation.focus();
            return;
        }

        if (password.value !== confirmation.value) {
            event.preventDefault();
            renderSequentialFieldError(confirmation, confirmationError, 'Xác nhận mật khẩu không khớp.');
            confirmation.focus();
            return;
        }

        renderSequentialFieldError(confirmation, confirmationError, '');
    });
}

function initProfilePasswordValidation() {
    initSequentialPasswordForm({
        formId: 'profilePasswordForm',
        passwordId: 'profile_password',
        passwordErrorId: 'profile_password_error',
        confirmationId: 'profile_password_confirm',
        confirmationErrorId: 'profile_password_confirm_error',
        required: false,
    });
}

function initResetPasswordValidation() {
    initSequentialPasswordForm({
        formId: 'resetPasswordForm',
        passwordId: 'reset_password',
        passwordErrorId: 'reset_password_error',
        confirmationId: 'reset_password_confirm',
        confirmationErrorId: 'reset_password_confirm_error',
        required: true,
    });
}

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

            document.getElementById('task_note').value = button.getAttribute('data-task-note') || '';

            if (!canManage) {
                notice.classList.remove('d-none');
                toggleManagerFields(managerFields, false);
            }
        } else {
            title.textContent = 'Tạo task mới';
            document.getElementById('task_id').value = '';

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
    var usernameInput = document.getElementById('user_username');
    var usernameErrorDiv = document.getElementById('username_error_msg');
    var emailInput = document.getElementById('user_email');
    var emailErrorDiv = document.getElementById('email_error_msg');
    var password = document.getElementById('user_password');
    var passwordErrorDiv = document.getElementById('password_error_msg');

    function renderFieldError(input, errorDiv, message) {
        if (!errorDiv) {
            return;
        }

        if (message === '') {
            errorDiv.style.display = 'none';
            errorDiv.textContent = '';
            input.classList.remove('is-invalid');
            return;
        }

        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        input.classList.add('is-invalid');
    }

    function getUsernameError(value) {
        if (/\d/.test(value)) {
            return 'Username không được chứa chữ số.';
        }

        if (!/^[a-zA-Z]+$/.test(value)) {
            return 'Username chỉ được chứa chữ cái (không chứa khoảng trắng hoặc ký tự đặc biệt).';
        }

        return '';
    }

    function getEmailError(value) {
        if (value !== value.trim()) {
            return 'Email không được chứa khoảng trắng ở đầu hoặc cuối.';
        }

        if (/\s/.test(value)) {
            return 'Email không được chứa khoảng trắng.';
        }

        if (value.indexOf('@') === -1) {
            return 'Email thiếu ký tự "@".';
        }

        var parts = value.split('@');
        if (parts.length > 2) {
            return 'Email chỉ được chứa duy nhất một ký tự "@".';
        }

        var localPart = parts[0];
        var domainPart = parts[1];

        if (localPart === '') {
            return 'Phần tên người dùng trước ký tự "@" không được để trống.';
        }

        if (domainPart === '') {
            return 'Tên miền sau ký tự "@" không được để trống.';
        }

        if (domainPart.indexOf('.') === -1) {
            return 'Tên miền sau ký tự "@" thiếu dấu chấm "." (ví dụ: gmail.com).';
        }

        if (domainPart.startsWith('.') || domainPart.endsWith('.')) {
            return 'Tên miền không được bắt đầu hoặc kết thúc bằng dấu chấm ".".';
        }

        if (domainPart.indexOf('..') !== -1) {
            return 'Tên miền không được chứa các dấu chấm liền nhau ".."';
        }

        var localPartRegex = /^[a-zA-Z0-9._+-]+$/;
        if (!localPartRegex.test(localPart)) {
            return 'Phần trước "@" chứa ký tự không hợp lệ. Chỉ được chứa chữ cái, số, dấu chấm, dấu gạch dưới, dấu cộng hoặc dấu gạch ngang.';
        }

        var domainPartRegex = /^[a-zA-Z0-9.-]+$/;
        if (!domainPartRegex.test(domainPart)) {
            return 'Tên miền sau "@" chứa ký tự không hợp lệ. Chỉ được chứa chữ cái, số, dấu gạch ngang hoặc dấu chấm.';
        }

        var domainSubparts = domainPart.split('.');
        var tld = domainSubparts[domainSubparts.length - 1];
        if (tld.length < 2) {
            return 'Phần mở rộng tên miền sau dấu chấm cuối cùng phải có ít nhất 2 ký tự (ví dụ: .com, .vn).';
        }

        return '';
    }

    function getPasswordErrors(value) {
        if (value.length < 6) {
            return ['Mật khẩu phải có tối thiểu 6 ký tự.'];
        }

        if (!/[A-Z]/.test(value)) {
            return ['Mật khẩu phải có ít nhất 1 chữ cái viết hoa.'];
        }

        if (!/[a-zA-Z]/.test(value) || !/[0-9]/.test(value)) {
            return ['Mật khẩu phải có cả chữ và số.'];
        }

        if (/\s/.test(value)) {
            return ['Mật khẩu không được chứa khoảng trắng.'];
        }

        if (!/[^a-zA-Z0-9\s]/.test(value)) {
            return ['Mật khẩu phải có ít nhất 1 ký tự đặc biệt.'];
        }

        return [];
    }

    function renderPasswordErrors(errors) {
        if (!passwordErrorDiv) {
            return;
        }

        if (errors.length === 0) {
            passwordErrorDiv.style.display = 'none';
            passwordErrorDiv.textContent = '';
            password.classList.remove('is-invalid');
            return;
        }

        passwordErrorDiv.textContent = errors.join('\n');
        passwordErrorDiv.style.display = 'block';
        password.classList.add('is-invalid');
    }

    password.addEventListener('input', function () {
        if (password.value === '') {
            renderPasswordErrors([]);
            return;
        }

        renderPasswordErrors(getPasswordErrors(password.value));
    });

    usernameInput.addEventListener('input', function () {
        if (usernameInput.value === '') {
            renderFieldError(usernameInput, usernameErrorDiv, '');
            return;
        }

        renderFieldError(usernameInput, usernameErrorDiv, getUsernameError(usernameInput.value));
    });

    emailInput.addEventListener('input', function () {
        if (emailInput.value === '') {
            renderFieldError(emailInput, emailErrorDiv, '');
            return;
        }

        renderFieldError(emailInput, emailErrorDiv, getEmailError(emailInput.value));
    });

    modalElement.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var mode = button ? button.getAttribute('data-mode') : 'create';

        form.reset();
        
        renderFieldError(emailInput, emailErrorDiv, '');
        renderFieldError(usernameInput, usernameErrorDiv, '');
        renderPasswordErrors([]);

        if (mode === 'edit' && button) {
            title.textContent = 'Cập nhật tài khoản';
            document.getElementById('user_id').value = button.getAttribute('data-user-id') || '';
            document.getElementById('user_full_name').value = button.getAttribute('data-user-full-name') || '';
            document.getElementById('user_email').value = button.getAttribute('data-user-email') || '';
            document.getElementById('user_username').value = button.getAttribute('data-user-username') || '';
            document.getElementById('user_role').value = button.getAttribute('data-user-role') || 'member';
            document.getElementById('user_department').value = button.getAttribute('data-user-department') || '';
            password.placeholder = 'Để trống nếu không đổi';
        } else {
            title.textContent = 'Tạo tài khoản mới';
            document.getElementById('user_id').value = '';
            document.getElementById('user_role').value = 'member';
            password.placeholder = '';
        }
        password.required = false;
    });

    form.addEventListener('submit', function (event) {
        var usernameValue = usernameInput.value;
        var emailValue = emailInput.value;
        var passwordValue = password.value;

        if (usernameValue !== '') {
            var usernameError = getUsernameError(usernameValue);

            if (usernameError !== '') {
                event.preventDefault();
                renderFieldError(usernameInput, usernameErrorDiv, usernameError);
                usernameInput.focus();
                return;
            }
        }
        renderFieldError(usernameInput, usernameErrorDiv, '');

        if (emailValue !== '') {
            var emailError = getEmailError(emailValue);

            if (emailError !== '') {
                event.preventDefault();
                renderFieldError(emailInput, emailErrorDiv, emailError);
                emailInput.focus();
                return;
            }
        }
        renderFieldError(emailInput, emailErrorDiv, '');

        var isCreatingUser = document.getElementById('user_id').value === '';
        var passwordErrors = [];

        if (passwordValue === '' && isCreatingUser) {
            passwordErrors.push('Tài khoản mới bắt buộc phải có mật khẩu.');
        } else if (passwordValue !== '') {
            passwordErrors = getPasswordErrors(passwordValue);
        }

        if (passwordErrors.length > 0) {
            event.preventDefault();
            renderPasswordErrors(passwordErrors);
            password.focus();
            return;
        }

        renderPasswordErrors([]);
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
