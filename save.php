<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

if (!isValidCsrfToken($_POST['csrf_token'] ?? null)) {
    flashMessage('Phiên làm việc không hợp lệ. Vui lòng thử lại.', 'error');
    redirect('index.php');
}

$rawId = $_POST['id'] ?? null;
$id = $rawId === null ? null : filter_var($rawId, FILTER_VALIDATE_INT);
$fullname = trim($_POST['fullname'] ?? '');
$rawSumClass = $_POST['sum_class'] ?? '';
$sumClass = filter_var($rawSumClass, FILTER_VALIDATE_INT);

if (($rawId !== null && (!$id || $id < 1))
    || $fullname === ''
    || mb_strlen($fullname, 'UTF-8') > 150
    || $sumClass === false
    || $sumClass < 0
    || $sumClass > 4294967295) {
    flashMessage('Vui lòng nhập họ tên và tổng số lớp hợp lệ.', 'error');
    redirect($id ? 'form.php?id=' . (int) $id : 'form.php');
}

$currentImage = 'uploads/default.svg';
if ($id) {
    $stmt = $conn->prepare('SELECT Image FROM GiangVien WHERE IDGV = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $currentLecturer = $stmt->get_result()->fetch_assoc();

    if (!$currentLecturer) {
        flashMessage('Không tìm thấy giảng viên cần cập nhật.', 'error');
        redirect('index.php');
    }
    $currentImage = $currentLecturer['Image'];
}

$imagePath = $currentImage;
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        flashMessage('Tải ảnh lên không thành công. Vui lòng thử lại.', 'error');
        redirect($id ? 'form.php?id=' . (int) $id : 'form.php');
    }

    if ($_FILES['image']['size'] > 3 * 1024 * 1024) {
        flashMessage('Ảnh quá lớn. Vui lòng chọn ảnh không quá 3 MB.', 'error');
        redirect($id ? 'form.php?id=' . (int) $id : 'form.php');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES['image']['tmp_name']);
    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowedTypes[$mimeType])) {
        flashMessage('Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, PNG hoặc WebP.', 'error');
        redirect($id ? 'form.php?id=' . (int) $id : 'form.php');
    }

    $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
        flashMessage('Không thể tạo thư mục lưu ảnh.', 'error');
        redirect($id ? 'form.php?id=' . (int) $id : 'form.php');
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
    $imagePath = 'uploads/' . $fileName;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDirectory . DIRECTORY_SEPARATOR . $fileName)) {
        flashMessage('Lưu ảnh thất bại. Vui lòng kiểm tra quyền ghi thư mục uploads.', 'error');
        redirect($id ? 'form.php?id=' . (int) $id : 'form.php');
    }
}

if ($id) {
    $stmt = $conn->prepare('UPDATE GiangVien SET Fullname = ?, Image = ?, SumClass = ? WHERE IDGV = ?');
    $stmt->bind_param('ssii', $fullname, $imagePath, $sumClass, $id);
    $stmt->execute();

    if ($imagePath !== $currentImage) {
        $oldImage = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentImage);
        if (strpos($currentImage, 'uploads/') === 0 && is_file($oldImage)) {
            unlink($oldImage);
        }
    }

    flashMessage('Cập nhật thông tin giảng viên thành công.');
} else {
    $stmt = $conn->prepare('INSERT INTO GiangVien (Fullname, Image, SumClass) VALUES (?, ?, ?)');
    $stmt->bind_param('ssi', $fullname, $imagePath, $sumClass);
    $stmt->execute();
    flashMessage('Thêm giảng viên thành công.');
}

redirect('index.php');
