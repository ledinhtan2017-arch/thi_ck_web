CREATE DATABASE IF NOT EXISTS QLHocPhan
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE QLHocPhan;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS GiangVien (
    IDGV INT UNSIGNED NOT NULL AUTO_INCREMENT,
    Fullname VARCHAR(150) NOT NULL,
    Image VARCHAR(255) NOT NULL DEFAULT 'uploads/zidane.jpg',
    SumClass INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (IDGV)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO GiangVien (Fullname, Image, SumClass) VALUES
    ('Nguyễn Thị Minh Anh', 'uploads/default.svg', 8),
    ('Trần Văn Hoàng', 'uploads/default.svg', 6),
    ('Lê Thị Thu Hà', 'uploads/default.svg', 10);
