<?php
require '../tasks.php';
require '../layout.php';

$filters = array(
    'keyword' => isset($_GET['keyword']) ? $_GET['keyword'] : '',
    'status' => isset($_GET['status']) ? $_GET['status'] : '',
    'member_id' => isset($_GET['member_id']) ? (int) $_GET['member_id'] : 0,
);

$tasks = get_all_tasks($filters);
$members = get_all_members();
$statuses = get_statuses();

render_header('Danh sách task', 'tasks');
?>
<section class="page-title-row">
    <div>
        <p class="eyebrow">CRUD task</p>
        <h1>Danh sách công việc (Legacy)</h1>
    </div>
    <a class="button" href="add.php">Thêm task</a>
</section>

<section class="panel">
    <form method="get" action="list.php" class="filter-form">
        <div>
            <label>Từ khóa</label>
            <input type="text" name="keyword" value="<?php echo escape($filters['keyword']); ?>" placeholder="Tìm task hoặc dự án">
        </div>
        <div>
            <label>Trạng thái</label>
            <select name="status">
                <option value="">Tất cả</option>
                <?php foreach ($statuses as $key => $name) { ?>
                    <option value="<?php echo escape($key); ?>" <?php if ($filters['status'] === $key) echo 'selected'; ?>>
                        <?php echo escape($name); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div>
            <label>Người làm</label>
            <select name="member_id">
                <option value="0">Tất cả</option>
                <?php foreach ($members as $member) { ?>
                    <option value="<?php echo (int) $member['id']; ?>" <?php if ((int) $filters['member_id'] === (int) $member['id']) echo 'selected'; ?>>
                        <?php echo escape($member['full_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="filter-actions">
            <button class="button" type="submit">Lọc</button>
            <a class="button secondary" href="list.php">Bỏ lọc</a>
        </div>
    </form>
</section>

<section class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Task</th>
                    <th>Dự án</th>
                    <th>Người làm</th>
                    <th>Trạng thái</th>
                    <th>Deadline</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task) { ?>
                    <tr>
                        <td><?php echo (int) $task['id']; ?></td>
                        <td>
                            <strong><?php echo escape($task['title']); ?></strong>
                            <div class="muted"><?php echo escape($task['priority']); ?></div>
                        </td>
                        <td><?php echo escape($task['project_name']); ?></td>
                        <td><?php echo escape($task['assignee_name'] ?: 'Chưa giao'); ?></td>
                        <td>
                            <span class="status-pill <?php echo status_class($task['status']); ?>">
                                <?php echo escape(status_name($task['status'])); ?>
                            </span>
                        </td>
                        <td class="<?php echo task_is_overdue($task) ? 'text-danger' : ''; ?>">
                            <?php echo escape(format_date_vn($task['deadline'])); ?>
                        </td>
                        <td class="table-actions">
                            <a href="edit.php?id=<?php echo (int) $task['id']; ?>">Sửa</a>
                            <a class="danger-link" href="../actions/task_legacy_action.php?action=delete&id=<?php echo (int) $task['id']; ?>" onclick="return confirm('Bạn có chắc muốn xóa task này không?');">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>

                <?php if (empty($tasks)) { ?>
                    <tr>
                        <td colspan="7" class="empty-cell">Không tìm thấy task phù hợp.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</section>

<?php
disconnect_db();
render_footer();
