<?php

function find_user_by_username($username)
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $statement->execute(array('username' => $username));

    return $statement->fetch();
}

function find_user_by_id($id)
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $statement->execute(array('id' => (int) $id));

    return $statement->fetch();
}

function fetch_all_users()
{
    global $pdo;

    $statement = $pdo->query(
        "SELECT id, full_name, email, username, role, department, created_at
        FROM users
        ORDER BY
            CASE role
                WHEN 'admin' THEN 0
                WHEN 'manager' THEN 1
                ELSE 2
            END,
            full_name ASC"
    );

    return $statement->fetchAll();
}

function fetch_assignable_users()
{
    global $pdo;

    $statement = $pdo->query(
        "SELECT id, full_name, username, role, department
        FROM users
        WHERE role IN ('manager', 'member')
        ORDER BY
            CASE role
                WHEN 'manager' THEN 0
                ELSE 1
            END,
            full_name ASC"
    );

    return $statement->fetchAll();
}

function username_exists($username, $ignoreUserId = 0)
{
    global $pdo;

    $statement = $pdo->prepare(
        'SELECT COUNT(*)
        FROM users
        WHERE username = :username AND id <> :ignore_user_id'
    );
    $statement->execute(
        array(
            'username' => $username,
            'ignore_user_id' => (int) $ignoreUserId,
        )
    );

    return (int) $statement->fetchColumn() > 0;
}

function update_user_profile($userId, $fullName, $email, $department, $password)
{
    global $pdo;

    $params = array(
        'full_name' => $fullName,
        'email' => $email,
        'department' => $department,
        'id' => (int) $userId,
    );

    $sql = 'UPDATE users
        SET full_name = :full_name,
            email = :email,
            department = :department';

    if ($password !== '') {
        $sql .= ', password_hash = :password_hash';
        $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $sql .= ' WHERE id = :id';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
}

function save_user_by_admin($userId, $fullName, $email, $username, $role, $department, $password)
{
    global $pdo;

    $params = array(
        'full_name' => $fullName,
        'email' => $email,
        'username' => $username,
        'role' => $role,
        'department' => $department,
    );

    if ($userId > 0) {
        $params['id'] = (int) $userId;
        $sql = 'UPDATE users
            SET full_name = :full_name,
                email = :email,
                username = :username,
                role = :role,
                department = :department';

        if ($password !== '') {
            $sql .= ', password_hash = :password_hash';
            $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = :id';

        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        return;
    }

    $params['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
    $statement = $pdo->prepare(
        'INSERT INTO users (full_name, email, username, password_hash, role, department)
        VALUES (:full_name, :email, :username, :password_hash, :role, :department)'
    );
    $statement->execute($params);
}

function fetch_notifications($limit = 0)
{
    global $pdo;

    $limit = (int) $limit;
    $sql = "
        SELECT n.*, u.full_name AS creator_name
        FROM notifications n
        INNER JOIN users u ON u.id = n.created_by
        ORDER BY n.created_at DESC, n.id DESC
    ";

    if ($limit > 0) {
        $sql .= ' LIMIT ' . $limit;
    }

    $statement = $pdo->query($sql);

    return $statement->fetchAll();
}

function fetch_notification_by_id($notificationId)
{
    global $pdo;

    $statement = $pdo->prepare('SELECT * FROM notifications WHERE id = :id LIMIT 1');
    $statement->execute(array('id' => (int) $notificationId));

    return $statement->fetch();
}

function fetch_task_status_report()
{
    global $pdo;

    if (is_admin() || is_manager()) {
        $statement = $pdo->query(
            "
            SELECT s.status_name, s.status_key, COUNT(t.id) AS task_count
            FROM task_statuses s
            LEFT JOIN tasks t ON t.status_id = s.id
            GROUP BY s.id, s.status_name, s.status_key, s.display_order
            ORDER BY s.display_order ASC
            "
        );

        return $statement->fetchAll();
    }

    $statement = $pdo->prepare(
        "
        SELECT
            s.status_name,
            s.status_key,
            COUNT(
                CASE
                    WHEN b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL THEN t.id
                END
            ) AS task_count
        FROM task_statuses s
        LEFT JOIN tasks t ON t.status_id = s.id
        LEFT JOIN boards b ON b.id = t.board_id
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        GROUP BY s.id, s.status_name, s.status_key, s.display_order
        ORDER BY s.display_order ASC
        "
    );
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetchAll();
}

function fetch_assignee_report()
{
    global $pdo;

    if (is_admin() || is_manager()) {
        $statement = $pdo->query(
            "
            SELECT
                COALESCE(u.full_name, 'Chưa giao') AS assignee_name,
                COUNT(t.id) AS task_count,
                SUM(CASE WHEN s.status_key = 'done' THEN 1 ELSE 0 END) AS done_count,
                SUM(CASE WHEN t.deadline < CURDATE() AND s.status_key <> 'done' THEN 1 ELSE 0 END) AS overdue_count,
                ROUND(AVG(t.progress_percent)) AS avg_progress
            FROM tasks t
            INNER JOIN task_statuses s ON s.id = t.status_id
            LEFT JOIN users u ON u.id = t.assignee_id
            GROUP BY t.assignee_id, u.full_name
            ORDER BY task_count DESC, assignee_name ASC
            "
        );

        return $statement->fetchAll();
    }

    $statement = $pdo->prepare(
        "
        SELECT
            COALESCE(u.full_name, 'Chưa giao') AS assignee_name,
            COUNT(t.id) AS task_count,
            SUM(CASE WHEN s.status_key = 'done' THEN 1 ELSE 0 END) AS done_count,
            SUM(CASE WHEN t.deadline < CURDATE() AND s.status_key <> 'done' THEN 1 ELSE 0 END) AS overdue_count,
            ROUND(AVG(t.progress_percent)) AS avg_progress
        FROM tasks t
        INNER JOIN task_statuses s ON s.id = t.status_id
        INNER JOIN boards b ON b.id = t.board_id
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        LEFT JOIN users u ON u.id = t.assignee_id
        WHERE b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL
        GROUP BY t.assignee_id, u.full_name
        ORDER BY task_count DESC, assignee_name ASC
        "
    );
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetchAll();
}

function fetch_calendar_tasks($limit)
{
    global $pdo;

    $limit = (int) $limit;

    if (is_admin() || is_manager()) {
        $sql = "
            SELECT
                t.*,
                s.status_key,
                b.name AS board_name,
                u.full_name AS assignee_name
            FROM tasks t
            INNER JOIN task_statuses s ON s.id = t.status_id
            INNER JOIN boards b ON b.id = t.board_id
            LEFT JOIN users u ON u.id = t.assignee_id
            WHERE t.deadline IS NOT NULL AND s.status_key <> 'done'
            ORDER BY t.deadline ASC, t.progress_percent DESC
            LIMIT {$limit}
        ";
        $statement = $pdo->query($sql);

        return $statement->fetchAll();
    }

    $sql = "
        SELECT
            t.*,
            s.status_key,
            b.name AS board_name,
            u.full_name AS assignee_name
        FROM tasks t
        INNER JOIN task_statuses s ON s.id = t.status_id
        INNER JOIN boards b ON b.id = t.board_id
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        LEFT JOIN users u ON u.id = t.assignee_id
        WHERE
            t.deadline IS NOT NULL
            AND s.status_key <> 'done'
            AND (b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL)
        ORDER BY t.deadline ASC, t.progress_percent DESC
        LIMIT {$limit}
    ";

    $statement = $pdo->prepare($sql);
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetchAll();
}

function fetch_statuses()
{
    global $pdo;

    $statement = $pdo->query('SELECT * FROM task_statuses ORDER BY display_order ASC');

    return $statement->fetchAll();
}

function fetch_board_status_ids()
{
    $statuses = fetch_statuses();
    $statusIds = array();

    foreach ($statuses as $status) {
        $statusIds[] = (int) $status['id'];
    }

    return $statusIds;
}

function fetch_boards_for_current_user()
{
    global $pdo;

    $baseSql = "
        SELECT
            b.*,
            owner.full_name AS owner_name,
            COUNT(DISTINCT bm.user_id) + 1 AS member_count,
            COUNT(DISTINCT t.id) AS task_count,
            COUNT(DISTINCT CASE WHEN s.status_key = 'done' THEN t.id END) AS done_count
        FROM boards b
        INNER JOIN users owner ON owner.id = b.owner_id
        LEFT JOIN board_members bm ON bm.board_id = b.id
        LEFT JOIN tasks t ON t.board_id = b.id
        LEFT JOIN task_statuses s ON s.id = t.status_id
    ";

    if (is_admin() || is_manager()) {
        $sql = $baseSql . '
            GROUP BY b.id
            ORDER BY b.created_at DESC
        ';
        $statement = $pdo->query($sql);
        return $statement->fetchAll();
    }

    $sql = $baseSql . '
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        WHERE b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL
        GROUP BY b.id
        ORDER BY b.created_at DESC
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetchAll();
}

function fetch_board_by_id($boardId)
{
    global $pdo;

    $baseSql = "
        SELECT
            b.*,
            owner.full_name AS owner_name,
            COUNT(DISTINCT bm.user_id) + 1 AS member_count,
            COUNT(DISTINCT t.id) AS task_count
        FROM boards b
        INNER JOIN users owner ON owner.id = b.owner_id
        LEFT JOIN board_members bm ON bm.board_id = b.id
        LEFT JOIN tasks t ON t.board_id = b.id
    ";

    if (is_admin() || is_manager()) {
        $sql = $baseSql . '
            WHERE b.id = :board_id
            GROUP BY b.id
            LIMIT 1
        ';
        $statement = $pdo->prepare($sql);
        $statement->execute(array('board_id' => (int) $boardId));
        return $statement->fetch();
    }

    $sql = $baseSql . '
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        WHERE b.id = :board_id
            AND (b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL)
        GROUP BY b.id
        LIMIT 1
    ';

    $statement = $pdo->prepare($sql);
    $statement->execute(
        array(
            'board_id' => (int) $boardId,
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetch();
}

function fetch_board_members($boardId)
{
    global $pdo;

    $statement = $pdo->prepare(
        "
        SELECT DISTINCT u.id, u.full_name, u.username, u.role, u.department
        FROM users u
        WHERE u.id = (SELECT owner_id FROM boards WHERE id = :board_owner_id)
            OR u.id IN (
                SELECT user_id
                FROM board_members
                WHERE board_id = :board_member_id
            )
        ORDER BY
            CASE u.role
                WHEN 'manager' THEN 0
                ELSE 1
            END,
            u.full_name ASC
        "
    );

    $statement->execute(
        array(
            'board_owner_id' => (int) $boardId,
            'board_member_id' => (int) $boardId,
        )
    );

    return $statement->fetchAll();
}

function fetch_board_member_ids($boardId)
{
    $members = fetch_board_members($boardId);
    $ids = array();

    foreach ($members as $member) {
        $ids[] = (int) $member['id'];
    }

    return $ids;
}

function fetch_tasks_by_board($boardId)
{
    global $pdo;

    $statement = $pdo->prepare(
        "
        SELECT
            t.*,
            s.status_name,
            s.status_key,
            s.display_order,
            assignee.full_name AS assignee_name,
            creator.full_name AS creator_name
        FROM tasks t
        INNER JOIN task_statuses s ON s.id = t.status_id
        LEFT JOIN users assignee ON assignee.id = t.assignee_id
        LEFT JOIN users creator ON creator.id = t.created_by
        WHERE t.board_id = :board_id
        ORDER BY s.display_order ASC, t.position_order ASC, t.deadline ASC, t.id DESC
        "
    );

    $statement->execute(array('board_id' => (int) $boardId));

    return $statement->fetchAll();
}

function fetch_grouped_tasks_by_board($boardId)
{
    $statuses = fetch_statuses();
    $tasks = fetch_tasks_by_board($boardId);
    $grouped = array();

    foreach ($statuses as $status) {
        $grouped[$status['status_key']] = array(
            'status' => $status,
            'tasks' => array(),
        );
    }

    foreach ($tasks as $task) {
        $grouped[$task['status_key']]['tasks'][] = $task;
    }

    return $grouped;
}

function fetch_task_by_id($taskId)
{
    global $pdo;

    $statement = $pdo->prepare(
        "
        SELECT
            t.*,
            s.status_name,
            s.status_key,
            b.name AS board_name,
            b.owner_id
        FROM tasks t
        INNER JOIN task_statuses s ON s.id = t.status_id
        INNER JOIN boards b ON b.id = t.board_id
        WHERE t.id = :task_id
        LIMIT 1
        "
    );
    $statement->execute(array('task_id' => (int) $taskId));

    return $statement->fetch();
}

function fetch_dashboard_stats()
{
    global $pdo;

    if (is_admin() || is_manager()) {
        $sql = "
            SELECT
                COUNT(DISTINCT b.id) AS board_count,
                COUNT(t.id) AS task_count,
                SUM(CASE WHEN s.status_key = 'done' THEN 1 ELSE 0 END) AS done_count,
                SUM(CASE WHEN t.deadline < CURDATE() AND s.status_key <> 'done' THEN 1 ELSE 0 END) AS overdue_count
            FROM boards b
            LEFT JOIN tasks t ON t.board_id = b.id
            LEFT JOIN task_statuses s ON s.id = t.status_id
        ";
        $statement = $pdo->query($sql);
        return $statement->fetch();
    }

    $sql = "
        SELECT
            COUNT(DISTINCT b.id) AS board_count,
            COUNT(t.id) AS task_count,
            SUM(CASE WHEN s.status_key = 'done' THEN 1 ELSE 0 END) AS done_count,
            SUM(CASE WHEN t.deadline < CURDATE() AND s.status_key <> 'done' THEN 1 ELSE 0 END) AS overdue_count
        FROM boards b
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        LEFT JOIN tasks t ON t.board_id = b.id
        LEFT JOIN task_statuses s ON s.id = t.status_id
        WHERE b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL
    ";

    $statement = $pdo->prepare($sql);
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetch();
}

function fetch_upcoming_tasks($limit)
{
    global $pdo;

    $limit = (int) $limit;

    if (is_admin() || is_manager()) {
        $sql = "
            SELECT
                t.*,
                s.status_key,
                b.name AS board_name,
                u.full_name AS assignee_name
            FROM tasks t
            INNER JOIN task_statuses s ON s.id = t.status_id
            INNER JOIN boards b ON b.id = t.board_id
            LEFT JOIN users u ON u.id = t.assignee_id
            WHERE t.deadline IS NOT NULL AND s.status_key <> 'done'
            ORDER BY t.deadline ASC, t.progress_percent DESC
            LIMIT {$limit}
        ";
        $statement = $pdo->query($sql);
        return $statement->fetchAll();
    }

    $sql = "
        SELECT
            t.*,
            s.status_key,
            b.name AS board_name,
            u.full_name AS assignee_name
        FROM tasks t
        INNER JOIN task_statuses s ON s.id = t.status_id
        INNER JOIN boards b ON b.id = t.board_id
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        LEFT JOIN users u ON u.id = t.assignee_id
        WHERE
            t.deadline IS NOT NULL
            AND s.status_key <> 'done'
            AND (b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL)
        ORDER BY t.deadline ASC, t.progress_percent DESC
        LIMIT {$limit}
    ";

    $statement = $pdo->prepare($sql);
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetchAll();
}

function fetch_overdue_tasks($limit)
{
    global $pdo;

    $limit = (int) $limit;

    if (is_admin() || is_manager()) {
        $sql = "
            SELECT
                t.*,
                s.status_key,
                b.name AS board_name,
                u.full_name AS assignee_name
            FROM tasks t
            INNER JOIN task_statuses s ON s.id = t.status_id
            INNER JOIN boards b ON b.id = t.board_id
            LEFT JOIN users u ON u.id = t.assignee_id
            WHERE t.deadline < CURDATE() AND s.status_key <> 'done'
            ORDER BY t.deadline ASC
            LIMIT {$limit}
        ";
        $statement = $pdo->query($sql);
        return $statement->fetchAll();
    }

    $sql = "
        SELECT
            t.*,
            s.status_key,
            b.name AS board_name,
            u.full_name AS assignee_name
        FROM tasks t
        INNER JOIN task_statuses s ON s.id = t.status_id
        INNER JOIN boards b ON b.id = t.board_id
        LEFT JOIN board_members access_member
            ON access_member.board_id = b.id
            AND access_member.user_id = :member_user_id
        LEFT JOIN users u ON u.id = t.assignee_id
        WHERE
            t.deadline < CURDATE()
            AND s.status_key <> 'done'
            AND (b.owner_id = :owner_user_id OR access_member.user_id IS NOT NULL)
        ORDER BY t.deadline ASC
        LIMIT {$limit}
    ";

    $statement = $pdo->prepare($sql);
    $statement->execute(
        array(
            'member_user_id' => current_user_id(),
            'owner_user_id' => current_user_id(),
        )
    );

    return $statement->fetchAll();
}
