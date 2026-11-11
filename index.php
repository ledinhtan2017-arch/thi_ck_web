<?php
require 'config.php';

$result = $conn->query('SELECT IDGV, Fullname, Image, SumClass FROM GiangVien ORDER BY IDGV DESC');
$lecturers = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f7fb">
    <title>Quản lý giảng viên | QL Học phần</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <p class="eyebrow">QUẢN TRỊ ĐÀO TẠO</p>
                <h1>Danh sách giảng viên</h1>
                <p class="subtitle">Quản lý thông tin giảng viên và số lớp đang phụ trách.</p>
            </div>
            <a class="btn btn-primary" href="form.php"><span aria-hidden="true">+</span> Thêm giảng viên</a>
        </header>

        <?= showFlashMessage() ?>

        <section class="summary" aria-label="Thống kê">
            <div class="summary-icon" aria-hidden="true">GV</div>
            <div>
                <span class="summary-label">Tổng số giảng viên</span>
                <strong><?= count($lecturers) ?></strong>
            </div>
        </section>

        <section class="panel" aria-labelledby="lecturer-list-heading">
            <div class="panel-heading">
                <div>
                    <h2 id="lecturer-list-heading">Thông tin giảng viên</h2>
                    <p>Dữ liệu được lưu trong cơ sở dữ liệu QLHocPhan.</p>
                </div>
                <span class="record-count"><?= count($lecturers) ?> giảng viên</span>
            </div>

            <?php if (!$lecturers): ?>
                <div class="empty-state">
                    <div class="empty-icon" aria-hidden="true">GV</div>
                    <h3>Chưa có giảng viên</h3>
                    <p>Thêm giảng viên đầu tiên để bắt đầu quản lý.</p>
                    <a class="btn btn-primary" href="form.php">Thêm giảng viên</a>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Mã GV</th>
                                <th scope="col">Giảng viên</th>
                                <th scope="col">Tổng số lớp</th>
                                <th scope="col" class="actions-heading">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <tr>
                                    <td data-label="Mã GV">
                                        <span class="lecturer-id">GV-<?= str_pad((string) $lecturer['IDGV'], 3, '0', STR_PAD_LEFT) ?></span>
                                    </td>
                                    <td data-label="Giảng viên">
                                        <div class="lecturer-cell">
                                            <img class="avatar" src="<?= e($lecturer['Image'] ?: 'uploads/default.svg') ?>" alt="Ảnh <?= e($lecturer['Fullname']) ?>">
                                            <span class="lecturer-name"><?= e($lecturer['Fullname']) ?></span>
                                        </div>
                                    </td>
                                    <td data-label="Tổng số lớp">
                                        <span class="class-count"><?= (int) $lecturer['SumClass'] ?> lớp</span>
                                    </td>
                                    <td data-label="Thao tác">
                                        <div class="actions">
                                            <a class="btn btn-small btn-secondary" href="form.php?id=<?= (int) $lecturer['IDGV'] ?>">Sửa</a>
                                            <form action="delete.php" method="post" onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên này?');">
                                                <input type="hidden" name="id" value="<?= (int) $lecturer['IDGV'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                                <button class="btn btn-small btn-danger" type="submit">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <footer class="page-footer">Hệ thống quản lý học phần <span>·</span> QLHocPhan</footer>
    </main>
</body>
</html>
