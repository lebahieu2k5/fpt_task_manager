CREATE DATABASE IF NOT EXISTS `fpt_task_manager`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `fpt_task_manager`;

DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `board_members`;
DROP TABLE IF EXISTS `boards`;
DROP TABLE IF EXISTS `task_statuses`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(120) DEFAULT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('manager', 'member') NOT NULL DEFAULT 'member',
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
    `priority` ENUM('Thap', 'Trung binh', 'Cao') NOT NULL DEFAULT 'Trung binh',
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
(1, 'Nguyen Quoc Bao', 'bao.pm@fpt.com', 'manager01', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'manager', 'Phong quan ly du an'),
(2, 'Tran Minh Chau', 'chau.backend@fpt.com', 'nhanvien01', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'member', 'Backend Team'),
(3, 'Le Hoang Nam', 'nam.frontend@fpt.com', 'nhanvien02', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'member', 'Frontend Team'),
(4, 'Pham Thu Ha', 'ha.qa@fpt.com', 'nhanvien03', '$2y$10$0Nta1cKJDdq6OqGS7GK3UeAHwvq6M0E3WtrDgZJgERN9d7Qr4NfPG', 'member', 'QA Team');

INSERT INTO `task_statuses` (`id`, `status_name`, `status_key`, `display_order`) VALUES
(1, 'Chua bat dau', 'todo', 1),
(2, 'Dang thuc hien', 'doing', 2),
(3, 'Cho duyet', 'review', 3),
(4, 'Hoan thanh', 'done', 4);

INSERT INTO `boards` (`id`, `name`, `description`, `owner_id`, `start_date`, `end_date`) VALUES
(1, 'Website quan li cong viec FPT', 'Board chinh de theo doi phan tich, code, test va bao cao tien do.', 1, '2026-04-20', '2026-05-20'),
(2, 'Sprint kiem thu giao dien', 'Board phu tap trung vao fix giao dien, responsive va kiem thu chuc nang.', 1, '2026-04-25', '2026-05-10');

INSERT INTO `board_members` (`board_id`, `user_id`) VALUES
(1, 2),
(1, 3),
(1, 4),
(2, 3),
(2, 4);

INSERT INTO `tasks` (`board_id`, `status_id`, `title`, `description`, `priority`, `assignee_id`, `deadline`, `progress_percent`, `note`, `position_order`, `created_by`) VALUES
(1, 1, 'Hoan thien phan tich Use Case', 'Tong hop actor, use case va workflow theo file tai lieu trong folder BTL WEB.', 'Cao', 2, '2026-04-30', 20, 'Dang bo sung mo ta nghiep vu.', 1, 1),
(1, 2, 'Dung giao dien board kieu Trello', 'Tao cac cot trang thai, task card va khu vuc keo tha bang HTML, CSS, Bootstrap.', 'Cao', 3, '2026-05-02', 65, 'Da xong layout, dang them keo tha task.', 1, 1),
(1, 3, 'Kiem thu dang nhap va phan quyen', 'Test luong manager va thanh vien, dam bao truy cap dung chuc nang.', 'Trung binh', 4, '2026-05-03', 80, 'Da test tay dang nhap, dang ghi lai loi.', 1, 1),
(1, 4, 'Thiet ke database va du lieu mau', 'Tao bang users, boards, tasks va seed du lieu de demo.', 'Trung binh', 2, '2026-04-28', 100, 'Da import thanh cong vao MySQL.', 1, 1),
(2, 2, 'Toi uu giao dien mobile', 'Can chinh sidebar, card va bang du lieu khi man hinh nho.', 'Trung binh', 3, '2026-05-05', 45, 'Dang tinh lai breakpoint cho tablet.', 1, 1),
(2, 1, 'Lap danh sach bug UI', 'Tong hop cac loi hien thi va ghi muc uu tien de fix.', 'Thap', 4, '2026-05-01', 10, 'Moi tao task, chua cap nhat them.', 1, 1);
