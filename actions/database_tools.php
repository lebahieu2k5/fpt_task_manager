<?php

function database_tool_path($toolName)
{
    $extension = PHP_OS_FAMILY === 'Windows' ? '.exe' : '';
    $candidates = array(
        'C:/xampp/mysql/bin/' . $toolName . $extension,
        '/usr/bin/' . $toolName,
        '/usr/local/bin/' . $toolName,
    );

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            return $candidate;
        }
    }

    return $toolName;
}

function database_command_argument($value)
{
    return escapeshellarg((string) $value);
}

function database_connection_arguments()
{
    return array(
        '--host=' . DB_HOST,
        '--user=' . DB_USER,
        '--password=' . DB_PASS,
        '--default-character-set=utf8mb4',
    );
}

function database_build_command($tool, $arguments)
{
    $toolPath = database_tool_path($tool);
    $parts = array(PHP_OS_FAMILY === 'Windows' ? escapeshellcmd($toolPath) : database_command_argument($toolPath));

    foreach ($arguments as $argument) {
        $parts[] = database_command_argument($argument);
    }

    return implode(' ', $parts);
}

function database_create_backup_file(&$errorMessage)
{
    $errorMessage = '';
    $tempFile = tempnam(sys_get_temp_dir(), 'fpt_backup_');

    if ($tempFile === false) {
        $errorMessage = 'Không thể tạo file tạm để sao lưu.';
        return '';
    }

    $arguments = array_merge(
        database_connection_arguments(),
        array(
            '--single-transaction',
            '--routines',
            '--triggers',
            '--events',
            '--hex-blob',
            '--add-drop-table',
            '--databases',
            DB_NAME,
        )
    );
    $command = database_build_command('mysqldump', $arguments);
    $descriptors = array(
        0 => array('pipe', 'r'),
        1 => array('file', $tempFile, 'w'),
        2 => array('pipe', 'w'),
    );
    $process = proc_open($command, $descriptors, $pipes);

    if (!is_resource($process)) {
        @unlink($tempFile);
        $errorMessage = 'Không thể khởi chạy công cụ sao lưu MySQL.';
        return '';
    }

    fclose($pipes[0]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0 || !is_file($tempFile) || filesize($tempFile) === 0) {
        @unlink($tempFile);
        $errorMessage = trim($stderr) !== '' ? trim($stderr) : 'Sao lưu database không thành công.';
        return '';
    }

    return $tempFile;
}

function database_restore_from_file($sqlFile, &$errorMessage)
{
    $errorMessage = '';
    $arguments = array_merge(database_connection_arguments(), array(DB_NAME));
    $command = database_build_command('mysql', $arguments);
    $descriptors = array(
        0 => array('file', $sqlFile, 'r'),
        1 => array('pipe', 'w'),
        2 => array('pipe', 'w'),
    );
    $process = proc_open($command, $descriptors, $pipes);

    if (!is_resource($process)) {
        $errorMessage = 'Không thể khởi chạy công cụ import MySQL.';
        return false;
    }

    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        $errorMessage = trim($stderr) !== '' ? trim($stderr) : trim($stdout);
        if ($errorMessage === '') {
            $errorMessage = 'Import database không thành công.';
        }
        return false;
    }

    return true;
}

function database_sql_file_is_valid($filePath)
{
    $handle = fopen($filePath, 'rb');

    if ($handle === false) {
        return false;
    }

    $sample = fread($handle, 1024 * 1024);
    fclose($handle);

    if ($sample === false || trim($sample) === '' || strpos($sample, "\0") !== false) {
        return false;
    }

    return preg_match('/\b(CREATE|INSERT|ALTER|DROP|USE|SET)\b/i', $sample) === 1;
}

function database_table_stats()
{
    global $pdo;

    $statement = $pdo->prepare(
        'SELECT
            TABLE_NAME AS table_name,
            TABLE_ROWS AS estimated_rows,
            DATA_LENGTH + INDEX_LENGTH AS size_bytes
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = :database_name
        ORDER BY TABLE_NAME ASC'
    );
    $statement->execute(array('database_name' => DB_NAME));

    return $statement->fetchAll();
}

function database_format_bytes($bytes)
{
    $bytes = (float) $bytes;
    $units = array('B', 'KB', 'MB', 'GB');
    $index = 0;

    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }

    return number_format($bytes, $index === 0 ? 0 : 2) . ' ' . $units[$index];
}
