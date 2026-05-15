<?php
require '../tasks.php';
require '../layout.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = get_task($id);

if (!$data) {
    header('location: list.php');
    exit;
}

$members = get_all_members();
$statuses = get_statuses();
$priorities = get_priorities();
$errors = array();

render_header('Sửa task', 'tasks');
?>
<section class="page-title-row">
    <div>
        <p class="eyebrow">Cập nhật</p>
        <h1>Sửa task (Legacy)</h1>
    </div>
    <a class="button secondary" href="list.php">Trở về</a>
</section>

<section class="panel">
    <form method="post" action="../actions/task_legacy_action.php">
        <table class="form-table">
            <tr>
                <td>Dự án</td>
                <td>
                    <input type="text" name="project_name" value="<?php echo escape($data['project_name']); ?>">
                </td>
            </tr>
            <tr>
                <td>Tên task</td>
                <td>
                    <input type="text" name="title" value="<?php echo escape($data['title']); ?>">
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
