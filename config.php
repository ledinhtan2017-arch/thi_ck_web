<?php
session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'QLHocPhan';

try {
    $conn = new mysqli($host, $user, $password, $database);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Không thể kết nối cơ sở dữ liệu. Hãy kiểm tra MySQL và import tệp database.sql.');
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flashMessage($message, $type = 'success')
{
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type,
    ];
}

function showFlashMessage()
{
    if (!isset($_SESSION['flash_message'])) {
        return '';
    }

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
    $class = $flash['type'] === 'error' ? 'alert-error' : 'alert-success';

    return '<div class="alert ' . $class . '" role="status">' . e($flash['message']) . '</div>';
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function isValidCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}
