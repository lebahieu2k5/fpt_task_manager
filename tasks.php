<?php

global $conn;
$conn = null;

function connect_db()
{
    global $conn;

    if (!$conn) {
        $conn = mysqli_connect('localhost', 'root', '', 'fpt_task_manager');

        if (!$conn) {
            die('Không thể kết nối database: ' . mysqli_connect_error());
        }

        mysqli_set_charset($conn, 'utf8mb4');
    }
}

function disconnect_db()
{
    global $conn;

    if ($conn) {
        mysqli_close($conn);
    }
}

function escape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function db_escape($value)
{
    global $conn;

    connect_db();

    return mysqli_real_escape_string($conn, trim((string) $value));
}

function get_statuses()
{
    return array(
        'todo' => 'Chưa bắt đầu',
        'doing' => 'Đang thực hiện',
        'review' => 'Chờ duyệt',
        'done' => 'Hoàn thành',
    );
}

function get_priorities()
{
    return array('Thấp', 'Trung bình', 'Cao');
}

function valid_status($status)
{
    $statuses = get_statuses();

    return array_key_exists($status, $statuses) ? $status : 'todo';
}

function valid_priority($priority)
{
    $priorities = get_priorities();

    return in_array($priority, $priorities, true) ? $priority : 'Trung bình';
}

function fix_progress($progress, $status)
{
    $progress = (int) $progress;

    if ($progress < 0) {
        $progress = 0;
    }

    if ($progress > 100) {
        $progress = 100;
    }

    if ($status === 'done') {
        $progress = 100;
    }

    return $progress;
}

function sql_date_value($date)
{
    $date = trim((string) $date);

    if ($date === '') {
        return 'NULL';
    }

    return "'" . db_escape($date) . "'";
}

function sql_member_value($memberId)
{
    $memberId = (int) $memberId;

    return $memberId > 0 ? (string) $memberId : 'NULL';
}

function get_all_members()
{
    global $conn;

    connect_db();

    $sql = 'SELECT * FROM members ORDER BY full_name ASC';
    $query = mysqli_query($conn, $sql);
    $result = array();

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
    }

    return $result;
}

function get_all_tasks($filters = array())
{
    global $conn;

    connect_db();

    $where = array();
    $keyword = isset($filters['keyword']) ? trim($filters['keyword']) : '';
    $status = isset($filters['status']) ? trim($filters['status']) : '';
    $memberId = isset($filters['member_id']) ? (int) $filters['member_id'] : 0;

    if ($keyword !== '') {
        $keyword = db_escape($keyword);
        $where[] = "(t.title LIKE '%{$keyword}%'
            OR t.project_name LIKE '%{$keyword}%'
            OR t.description LIKE '%{$keyword}%')";
    }

    if ($status !== '' && array_key_exists($status, get_statuses())) {
        $status = db_escape($status);
        $where[] = "t.status = '{$status}'";
    }

    if ($memberId > 0) {
        $where[] = "t.assignee_id = {$memberId}";
    }

    $sql = "
        SELECT
            t.*,
            m.full_name AS assignee_name,
            m.department AS assignee_department
        FROM tasks t
        LEFT JOIN members m ON m.id = t.assignee_id
    ";

    if (!empty($where)) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= "
        ORDER BY
            FIELD(t.status, 'todo', 'doing', 'review', 'done'),
            t.deadline IS NULL,
            t.deadline ASC,
            t.id DESC
    ";

    $query = mysqli_query($conn, $sql);
    $result = array();

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
    }

    return $result;
}

function get_task($taskId)
{
    global $conn;

    connect_db();

    $taskId = (int) $taskId;
    $sql = "
        SELECT
            t.*,
            m.full_name AS assignee_name,
            m.department AS assignee_department
        FROM tasks t
        LEFT JOIN members m ON m.id = t.assignee_id
        WHERE t.id = {$taskId}
        LIMIT 1
    ";
    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) > 0) {
        return mysqli_fetch_assoc($query);
    }

    return array();
}

function add_task($projectName, $title, $description, $assigneeId, $status, $priority, $deadline, $progress, $note)
{
    global $conn;

    connect_db();

    $status = valid_status($status);
    $priority = valid_priority($priority);
    $progress = fix_progress($progress, $status);

    $projectName = db_escape($projectName);
    $title = db_escape($title);
    $description = db_escape($description);
    $status = db_escape($status);
    $priority = db_escape($priority);
    $note = db_escape($note);
    $assigneeSql = sql_member_value($assigneeId);
    $deadlineSql = sql_date_value($deadline);

    $sql = "
        INSERT INTO tasks
            (project_name, title, description, assignee_id, status, priority, deadline, progress_percent, note)
        VALUES
            ('{$projectName}', '{$title}', '{$description}', {$assigneeSql}, '{$status}', '{$priority}', {$deadlineSql}, {$progress}, '{$note}')
    ";

    return mysqli_query($conn, $sql);
}

function edit_task($taskId, $projectName, $title, $description, $assigneeId, $status, $priority, $deadline, $progress, $note)
{
    global $conn;

    connect_db();

    $taskId = (int) $taskId;
    $status = valid_status($status);
    $priority = valid_priority($priority);
    $progress = fix_progress($progress, $status);

    $projectName = db_escape($projectName);
    $title = db_escape($title);
    $description = db_escape($description);
    $status = db_escape($status);
    $priority = db_escape($priority);
    $note = db_escape($note);
    $assigneeSql = sql_member_value($assigneeId);
    $deadlineSql = sql_date_value($deadline);

    $sql = "
        UPDATE tasks SET
            project_name = '{$projectName}',
            title = '{$title}',
            description = '{$description}',
            assignee_id = {$assigneeSql},
            status = '{$status}',
            priority = '{$priority}',
            deadline = {$deadlineSql},
            progress_percent = {$progress},
            note = '{$note}'
        WHERE id = {$taskId}
    ";

    return mysqli_query($conn, $sql);
}

function delete_task($taskId)
{
    global $conn;

    connect_db();

    $taskId = (int) $taskId;
    $sql = "DELETE FROM tasks WHERE id = {$taskId}";

    return mysqli_query($conn, $sql);
}

function get_tasks_grouped_by_status()
{
    $statuses = get_statuses();
    $tasks = get_all_tasks();
    $groups = array();

    foreach ($statuses as $key => $name) {
        $groups[$key] = array(
            'name' => $name,
            'tasks' => array(),
        );
    }

    foreach ($tasks as $task) {
        $groups[$task['status']]['tasks'][] = $task;
    }

    return $groups;
}

function get_task_stats()
{
    global $conn;

    connect_db();

    $sql = "
        SELECT
            COUNT(*) AS total_task,
            SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS done_task,
            SUM(CASE WHEN status <> 'done' THEN 1 ELSE 0 END) AS working_task,
            SUM(CASE WHEN deadline < CURDATE() AND status <> 'done' THEN 1 ELSE 0 END) AS overdue_task,
            ROUND(AVG(progress_percent)) AS avg_progress
        FROM tasks
    ";
    $query = mysqli_query($conn, $sql);
    $row = $query ? mysqli_fetch_assoc($query) : array();

    return array(
        'total_task' => isset($row['total_task']) ? (int) $row['total_task'] : 0,
        'done_task' => isset($row['done_task']) ? (int) $row['done_task'] : 0,
        'working_task' => isset($row['working_task']) ? (int) $row['working_task'] : 0,
        'overdue_task' => isset($row['overdue_task']) ? (int) $row['overdue_task'] : 0,
        'avg_progress' => isset($row['avg_progress']) ? (int) $row['avg_progress'] : 0,
    );
}

function get_project_summaries()
{
    global $conn;

    connect_db();

    $sql = "
        SELECT
            project_name,
            COUNT(*) AS total_task,
            SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) AS done_task,
            ROUND(AVG(progress_percent)) AS avg_progress,
            MIN(deadline) AS first_deadline,
            MAX(deadline) AS last_deadline
        FROM tasks
        GROUP BY project_name
        ORDER BY last_deadline IS NULL, last_deadline ASC, project_name ASC
    ";
    $query = mysqli_query($conn, $sql);
    $result = array();

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
    }

    return $result;
}

function get_upcoming_tasks($limit)
{
    global $conn;

    connect_db();

    $limit = (int) $limit;
    $sql = "
        SELECT
            t.*,
            m.full_name AS assignee_name
        FROM tasks t
        LEFT JOIN members m ON m.id = t.assignee_id
        WHERE t.deadline IS NOT NULL AND t.status <> 'done'
        ORDER BY t.deadline ASC, t.priority DESC, t.id DESC
        LIMIT {$limit}
    ";
    $query = mysqli_query($conn, $sql);
    $result = array();

    if ($query) {
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }
    }

    return $result;
}

function status_name($status)
{
    $statuses = get_statuses();

    return isset($statuses[$status]) ? $statuses[$status] : $status;
}

function format_date_vn($date)
{
    if (empty($date)) {
        return 'Chưa có';
    }

    $time = strtotime($date);

    return $time ? date('d/m/Y', $time) : $date;
}

function task_is_overdue($task)
{
    return !empty($task['deadline'])
        && $task['status'] !== 'done'
        && strtotime($task['deadline']) < strtotime(date('Y-m-d'));
}

function priority_class($priority)
{
    switch ($priority) {
        case 'Cao':
            return 'badge-red';
        case 'Trung bình':
            return 'badge-yellow';
        default:
            return 'badge-green';
    }
}

function status_class($status)
{
    switch ($status) {
        case 'doing':
            return 'status-blue';
        case 'review':
            return 'status-yellow';
        case 'done':
            return 'status-green';
        default:
            return 'status-gray';
    }
}

function progress_class($progress)
{
    $progress = (int) $progress;

    if ($progress >= 100) {
        return 'progress-green';
    }

    if ($progress >= 60) {
        return 'progress-blue';
    }

    if ($progress >= 30) {
        return 'progress-yellow';
    }

    return 'progress-gray';
}
