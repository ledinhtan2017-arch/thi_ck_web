<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!isValidCsrfToken($_POST['csrf_token'] ?? null)) {
    flashMessage('Phiên làm việc không hợp lệ. Vui lòng thử lại.', 'error');
    redirect('index.php');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flashMessage('Mã giảng viên cần xóa không hợp lệ.', 'error');
    redirect('index.php');
}

$stmt = $conn->prepare('SELECT Image FROM GiangVien WHERE IDGV = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$lecturer = $stmt->get_result()->fetch_assoc();

if (!$lecturer) {
    flashMessage('Không tìm thấy giảng viên cần xóa.', 'error');
    redirect('index.php');
}

$stmt = $conn->prepare('DELETE FROM GiangVien WHERE IDGV = ?');
$stmt->bind_param('i', $id);
$stmt->execute();

$imagePath = $lecturer['Image'];
$fullImagePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath);
if (strpos($imagePath, 'uploads/') === 0 && is_file($fullImagePath)) {
    unlink($fullImagePath);
}

flashMessage('Đã xóa giảng viên.');
redirect('index.php');
