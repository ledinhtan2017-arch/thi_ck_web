<?php
require 'config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$lecturer = [
    'IDGV' => '',
    'Fullname' => '',
    'Image' => 'uploads/default.svg',
    'SumClass' => '',
];

if ($id !== null && $id !== false) {
    if ($id < 1) {
        flashMessage('Mã giảng viên không hợp lệ.', 'error');
        redirect('index.php');
    }

    $stmt = $conn->prepare('SELECT IDGV, Fullname, Image, SumClass FROM GiangVien WHERE IDGV = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $foundLecturer = $stmt->get_result()->fetch_assoc();

    if (!$foundLecturer) {
        flashMessage('Không tìm thấy giảng viên cần sửa.', 'error');
        redirect('index.php');
    }
    $lecturer = $foundLecturer;
}

$isEditing = $lecturer['IDGV'] !== '';
$title = $isEditing ? 'Cập nhật giảng viên' : 'Thêm giảng viên mới';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | QL Học phần</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="container form-container">
        <a class="back-link" href="index.php"><span aria-hidden="true">←</span> Quay lại danh sách</a>
        <header class="form-header">
            <p class="eyebrow">QUẢN TRỊ ĐÀO TẠO</p>
            <h1><?= e($title) ?></h1>
            <p class="subtitle">Nhập đầy đủ thông tin giảng viên bên dưới.</p>
        </header>

        <section class="form-card">
            <form action="save.php" method="post" enctype="multipart/form-data">
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?= (int) $lecturer['IDGV'] ?>">
                <?php endif; ?>
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

                <div class="form-group">
                    <label for="fullname">Họ và tên <span class="required">*</span></label>
                    <input id="fullname" name="fullname" type="text" maxlength="150" value="<?= e($lecturer['Fullname']) ?>" placeholder="Ví dụ: Nguyễn Văn An" required>
                </div>

                <div class="form-group">
                    <label for="sum_class">Tổng số lớp <span class="required">*</span></label>
                    <input id="sum_class" name="sum_class" type="number" min="0" max="4294967295" step="1" value="<?= e($lecturer['SumClass']) ?>" placeholder="Ví dụ: 8" required>
                </div>

                <div class="form-group">
                    <label for="image">Ảnh giảng viên</label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    <small>Chấp nhận JPG, PNG hoặc WebP, dung lượng tối đa 3 MB. Có thể bỏ trống khi sửa để giữ ảnh hiện tại.</small>
                    <div class="image-preview">
                        <img src="<?= e($lecturer['Image'] ?: 'uploads/default.svg') ?>" alt="Ảnh hiện tại của giảng viên">
                        <span><?= $isEditing ? 'Ảnh hiện tại' : 'Ảnh mặc định' ?></span>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn btn-primary" type="submit"><?= $isEditing ? 'Lưu thay đổi' : 'Thêm giảng viên' ?></button>
                    <a class="btn btn-secondary" href="index.php">Hủy</a>
                </div>
            </form>
        </section>
        <footer class="page-footer">Các trường có dấu <span class="required">*</span> là bắt buộc.</footer>
    </main>
</body>
</html>
