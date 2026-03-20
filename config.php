<?php
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = (int) (getenv('DB_PORT') ?: 3307);
$dbname = getenv('DB_NAME') ?: 'attendance_management';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD');

if ($password === false) {
    $password = '';
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($host, $username, $password, $dbname, $port);

if (!$conn || $conn->connect_errno) {
    http_response_code(500);
    die(
        'Database connection failed. Check DB credentials in config.php or set DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD environment variables.'
    );
}

$conn->set_charset('utf8mb4');

function clean_input($value)
{
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}
?>
