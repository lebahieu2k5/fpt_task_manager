CREATE DATABASE IF NOT EXISTS `fpt_task_manager`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `fpt_task_manager`;

DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `board_members`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `boards`;
DROP TABLE IF EXISTS `task_statuses`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(120) DEFAULT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'manager', 'member') NOT NULL DEFAULT 'member',
    `department` VARCHAR(120) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `boards` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `owner_id` INT NOT NULL,
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_boards_owner` FOREIGN KEY (`owner_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(160) NOT NULL,
    `content` TEXT NOT NULL,
    `priority` ENUM('Thấp', 'Trung bình', 'Cao') NOT NULL DEFAULT 'Trung bình',
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_notifications_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `board_members` (
    `board_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `joined_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`board_id`, `user_id`),
    CONSTRAINT `fk_board_members_board` FOREIGN KEY (`board_id`) REFERENCES `boards`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_board_members_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `task_statuses` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `status_name` VARCHAR(80) NOT NULL,
    `status_key` VARCHAR(30) NOT NULL UNIQUE,
    `display_order` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `board_id` INT NOT NULL,
    `status_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `priority` ENUM('Thấp', 'Trung bình', 'Cao') NOT NULL DEFAULT 'Trung bình',
    `assignee_id` INT DEFAULT NULL,
    `deadline` DATE DEFAULT NULL,
    `progress_percent` INT NOT NULL DEFAULT 0,
    `note` TEXT DEFAULT NULL,
    `position_order` INT NOT NULL DEFAULT 0,
    `created_by` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_tasks_board` FOREIGN KEY (`board_id`) REFERENCES `boards`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tasks_status` FOREIGN KEY (`status_id`) REFERENCES `task_statuses`(`id`),
    CONSTRAINT `fk_tasks_assignee` FOREIGN KEY (`assignee_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tasks_creator` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `full_name`, `email`, `username`, `password_hash`, `role`, `department`) VALUES
(1, 'Nguyễn Quốc Bảo', 'bao.pm@fpt.com', 'manager01', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'manager', 'Phòng quản lý dự án'),
(2, 'Trần Minh Châu', 'chau.backend@fpt.com', 'nhanvien01', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'member', 'Backend Team'),
(3, 'Lê Hoàng Nam', 'nam.frontend@fpt.com', 'nhanvien02', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'member', 'Frontend Team'),
(4, 'Phạm Thu Hà', 'ha.qa@fpt.com', 'nhanvien03', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'member', 'QA Team'),
(5, 'Admin Hệ Thống', 'admin@fpt.com', 'admin01', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'admin', 'Quản trị hệ thống');

INSERT INTO `task_statuses` (`id`, `status_name`, `status_key`, `display_order`) VALUES
(1, 'Chưa bắt đầu', 'todo', 1),
(2, 'Đang thực hiện', 'doing', 2),
(3, 'Chờ duyệt', 'review', 3),
(4, 'Hoàn thành', 'done', 4);

INSERT INTO `boards` (`id`, `name`, `description`, `owner_id`, `start_date`, `end_date`) VALUES
(1, 'Website quản lý công việc FPT', 'Board chính để theo dõi phân tích, code, test và báo cáo tiến độ.', 1, '2026-04-20', '2026-05-20'),
(2, 'Sprint kiểm thử giao diện', 'Board phụ tập trung vào fix giao diện, responsive và kiểm thử chức năng.', 1, '2026-04-25', '2026-05-10');

INSERT INTO `notifications` (`id`, `title`, `content`, `priority`, `created_by`) VALUES
(1, 'Cập nhật deadline', 'Các thành viên kiểm tra task sắp đến hạn và cập nhật tiến độ trước cuối ngày.', 'Cao', 1),
(2, 'Kiểm thử giao diện', 'Nhóm QA tổng hợp lỗi hiển thị và gắn mức ưu tiên cho từng task.', 'Trung bình', 1);

INSERT INTO `board_members` (`board_id`, `user_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(2, 3),
(2, 4);

INSERT INTO `tasks` (`board_id`, `status_id`, `title`, `description`, `priority`, `assignee_id`, `deadline`, `progress_percent`, `note`, `position_order`, `created_by`) VALUES
(1, 1, 'Hoàn thiện phân tích yêu cầu', 'Tổng hợp actor, chức năng và luồng xử lý chính của hệ thống.', 'Cao', 2, '2026-04-30', 20, 'Đang bổ sung mô tả nghiệp vụ.', 1, 1),
(1, 2, 'Dựng giao diện quản lý board', 'Tạo các cột trạng thái, task card và khu vực kéo thả bằng HTML, CSS, Bootstrap.', 'Cao', 3, '2026-05-02', 65, 'Đã xong layout, đang thêm kéo thả task.', 1, 1),
(1, 3, 'Kiểm thử đăng nhập và phân quyền', 'Test luồng manager và thành viên, đảm bảo truy cập đúng chức năng.', 'Trung bình', 4, '2026-05-03', 80, 'Đã test tay đăng nhập, đang ghi lại lỗi.', 1, 1),
(1, 4, 'Thiết kế database và dữ liệu ban đầu', 'Tạo bảng users, boards và tasks cho hệ thống.', 'Trung bình', 2, '2026-04-28', 100, 'Đã import thành công vào MySQL.', 1, 1),
(2, 2, 'Tối ưu giao diện mobile', 'Căn chỉnh sidebar, card và bảng dữ liệu khi màn hình nhỏ.', 'Trung bình', 3, '2026-05-05', 45, 'Đang tính lại breakpoint cho tablet.', 1, 1),
(2, 1, 'Lập danh sách bug UI', 'Tổng hợp các lỗi hiển thị và ghi mức ưu tiên để fix.', 'Thấp', 4, '2026-05-01', 10, 'Mới tạo task, chưa cập nhật thêm.', 1, 1);
