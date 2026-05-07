<?php
require 'tasks.php';
require 'layout.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = get_task($id);

if (!$data) {
    header('location: task-list.php');
    exit;
}

$members = get_all_members();
$statuses = get_statuses();
$priorities = get_priorities();
$errors = array();

if (!empty($_POST['edit_task'])) {
    $data['id'] = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $data['project_name'] = isset($_POST['project_name']) ? $_POST['project_name'] : '';
    $data['title'] = isset($_POST['title']) ? $_POST['title'] : '';
    $data['description'] = isset($_POST['description']) ? $_POST['description'] : '';
    $data['assignee_id'] = isset($_POST['assignee_id']) ? $_POST['assignee_id'] : '';
    $data['status'] = isset($_POST['status']) ? $_POST['status'] : 'todo';
    $data['priority'] = isset($_POST['priority']) ? $_POST['priority'] : 'Trung bình';
    $data['deadline'] = isset($_POST['deadline']) ? $_POST['deadline'] : '';
    $data['progress_percent'] = isset($_POST['progress_percent']) ? $_POST['progress_percent'] : 0;
    $data['note'] = isset($_POST['note']) ? $_POST['note'] : '';

    if (trim($data['project_name']) === '') {
        $errors['project_name'] = 'Chưa nhập tên dự án';
    }

    if (trim($data['title']) === '') {
        $errors['title'] = 'Chưa nhập tên task';
    }

    if (!$errors) {
        edit_task(
            $data['id'],
            $data['project_name'],
            $data['title'],
            $data['description'],
            $data['assignee_id'],
            $data['status'],
            $data['priority'],
            $data['deadline'],
            $data['progress_percent'],
            $data['note']
        );

        header('location: task-list.php');
        exit;
    }
}

render_header('Sửa task', 'tasks');
?>
<section class="page-title-row">
    <div>
        <p class="eyebrow">Cập nhật</p>
        <h1>Sửa task</h1>
    </div>
    <a class="button secondary" href="task-list.php">Trở về</a>
</section>

<section class="panel">
    <form method="post" action="task-edit.php?id=<?php echo (int) $data['id']; ?>">
        <table class="form-table">
            <tr>
                <td>Dự án</td>
                <td>
                    <input type="text" name="project_name" value="<?php echo escape($data['project_name']); ?>">
                    <?php if (!empty($errors['project_name'])) echo '<span class="error">' . escape($errors['project_name']) . '</span>'; ?>
                </td>
            </tr>
            <tr>
                <td>Tên task</td>
                <td>
                    <input type="text" name="title" value="<?php echo escape($data['title']); ?>">
                    <?php if (!empty($errors['title'])) echo '<span class="error">' . escape($errors['title']) . '</span>'; ?>
                </td>
            </tr>
            <tr>
                <td>Mô tả</td>
                <td><textarea name="description" rows="4"><?php echo escape($data['description']); ?></textarea></td>
            </tr>
            <tr>
                <td>Người thực hiện</td>
                <td>
                    <select name="assignee_id">
                        <option value="">Chưa giao</option>
                        <?php foreach ($members as $member) { ?>
                            <option value="<?php echo (int) $member['id']; ?>" <?php if ((int) $data['assignee_id'] === (int) $member['id']) echo 'selected'; ?>>
                                <?php echo escape($member['full_name']); ?> - <?php echo escape($member['department']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Trạng thái</td>
                <td>
                    <select name="status">
                        <?php foreach ($statuses as $key => $name) { ?>
                            <option value="<?php echo escape($key); ?>" <?php if ($data['status'] === $key) echo 'selected'; ?>>
                                <?php echo escape($name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Độ ưu tiên</td>
                <td>
                    <select name="priority">
                        <?php foreach ($priorities as $priority) { ?>
                            <option value="<?php echo escape($priority); ?>" <?php if ($data['priority'] === $priority) echo 'selected'; ?>>
                                <?php echo escape($priority); ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Deadline</td>
                <td><input type="date" name="deadline" value="<?php echo escape($data['deadline']); ?>"></td>
            </tr>
            <tr>
                <td>Tiến độ (%)</td>
                <td><input type="number" name="progress_percent" min="0" max="100" value="<?php echo (int) $data['progress_percent']; ?>"></td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td><textarea name="note" rows="3"><?php echo escape($data['note']); ?></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="hidden" name="id" value="<?php echo (int) $data['id']; ?>">
                    <input class="button" type="submit" name="edit_task" value="Lưu thay đổi">
                </td>
            </tr>
        </table>
    </form>
</section>

<?php
disconnect_db();
render_footer();
