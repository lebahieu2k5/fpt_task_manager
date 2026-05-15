# FPT Task Manager

Đây là bài tập lớn web được dựng theo hướng đơn giản, dễ học và dễ thuyết trình.

## 1. Ý tưởng chính

Project được rút gọn từ:

- Tài liệu `Use Case phân tích nghiệp vụ.docx` trong `BTL WEB`
- Code mẫu CRUD sinh viên trong `HieuBa_102136\Buoi 8`
- Cách tổ chức file PHP và dashboard trong `web_QLVT`
- Cách hiển thị board/list/card của Trello

## 2. Chức năng đã làm

- Đăng nhập bằng tài khoản Admin, Quản lý và Nhân viên
- Admin phân quyền, cập nhật tài khoản, xem báo cáo/thống kê và cập nhật thông báo
- Quản lý cập nhật board, task, nhân sự trong board, giám sát và xem báo cáo
- Nhân viên nhận việc, cập nhật trạng thái và tiến độ công việc
- Dashboard tổng hợp số board, số task, task hoàn thành, task trễ hạn
- Quản lý board công việc
- Tạo task, giao người thực hiện, gắn deadline, mức ưu tiên
- Cập nhật tài khoản cá nhân
- Quản lý thông báo nội bộ
- Báo cáo/thống kê tiến độ theo trạng thái, board và nhân sự
- Tạo link đồng bộ deadline task sang Google Calendar
- Hiển thị task theo 4 cột trạng thái:
  - Chưa bắt đầu
  - Đang thực hiện
  - Chờ duyệt
  - Hoàn thành
- Kéo thả task sang cột khác để đổi trạng thái
- Thành viên chỉ cập nhật task được giao cho mình

## 3. Cấu trúc thư mục

- `config/`: kết nối DB và session
- `partials/`: header, footer
- `actions/`: xử lý lưu, xóa, kéo thả
- `assets/css/styles.css`: giao diện
- `assets/js/app.js`: JS modal và drag-drop
- `profile.php`: cập nhật tài khoản
- `users.php`: Admin quản lý tài khoản và phân quyền
- `notifications.php`: quản lý thông báo
- `reports.php`: tổng hợp báo cáo và thống kê
- `calendar.php`: lịch deadline và link Google Calendar
- `database.sql`: file import MySQL

## 4. Cách chạy bằng XAMPP

1. Đặt thư mục `fpt_task_manager` vào `htdocs` của XAMPP.
2. Mở `phpMyAdmin`.
3. Import file `database.sql`.
4. Sửa file `config/database.php` nếu máy bạn khác `DB_USER`, `DB_PASS`, `DB_NAME`.
5. Bật `Apache` và `MySQL`.
6. Truy cập:

```text
http://localhost/fpt_task_manager/login.php
```

## 5. Tài khoản demo

- Admin:
  - Username: `admin01`
  - Password: `123456`

- Quản lý:
  - Username: `manager01`
  - Password: `123456`
- Thành viên 1:
  - Username: `nhanvien01`
  - Password: `123456`
- Thành viên 2:
  - Username: `nhanvien02`
  - Password: `123456`
- Thành viên 3:
  - Username: `nhanvien03`
  - Password: `123456`

## 6. Gợi ý cách thuyết trình

- Nếu nói theo Use Case:
  - Actor chính: Chủ bảng, Thành viên
  - Nghiệp vụ chính: tạo board, tạo task, giao việc, cập nhật trạng thái, xem dashboard
- Nếu nói theo giao diện:
  - Dashboard = tổng quan tiến độ
  - Board = dự án
  - Column = trạng thái công việc
  - Card = một task cụ thể

## 7. Hướng mở rộng nếu cần nâng cấp

- Thêm bình luận cho task
- Thêm upload file minh chứng
- Thêm bộ lọc theo nhân viên / deadline
- Thêm biểu đồ thống kê bằng Chart.js
- Thêm lịch deadline dạng calendar



Role manager không nằm trong 1 file, mà trải ra theo luồng quyền + màn hình + action.

Các file chính của manager:


login.php (line 11): đăng nhập, nạp role vào $_SESSION['user'].

helpers.php (line 71): lõi phân quyền với is_manager(), require_manager(), require_report_access(), can_edit_task().

partials/header.php (line 45): menu hiển thị cho manager như boards.php, calendar.php, reports.php.

dashboard.php (line 8): dashboard của manager lấy số liệu tổng.

boards.php (line 19): manager được tạo/sửa/xóa board và thêm thành viên.

board.php (line 51): manager tạo task, sửa full task, xóa task ngay trong board.

reports.php (line 3): manager được xem báo cáo.

repository.php (line 360): nơi query dữ liệu theo quyền manager, gồm board/task/report/dashboard.

actions/board_save.php (line 3): lưu tạo/sửa board, chỉ manager.

actions/board_delete.php (line 3): xóa board, chỉ manager.

actions/task_save.php (line 73): manager sửa full task; tạo task mới cũng bị chặn bởi require_manager().

actions/task_move.php (line 31): kéo thả đổi trạng thái task nếu có quyền sửa.

actions/task_delete.php (line 3): xóa task, chỉ manager.

assets/js/app.js (line 175): bật modal task và kéo-thả board.

database.sql (line 20): định nghĩa role admin/manager/member, có seed user manager01.


Luồng chạy ngắn của manager:

Admin tạo tài khoản có role manager ở users.php (line 71) + actions/user_save.php (line 13).
Manager đăng nhập ở login.php.
helpers.php xác định quyền, partials/header.php hiện menu phù hợp.
Vào boards.php để xem toàn bộ board; manager có nút tạo/sửa/xóa.
Vào board.php?id=... để quản lý task của board.
Submit form đi về actions/task_save.php, kéo-thả đi về actions/task_move.php.
Vào reports.php để xem thống kê tổng hợp.

Lưu ý: task-add.php, task-edit.php, task-list.php, tasks.php là nhánh CRUD cũ, không phải luồng manager hiện tại trong menu chính. Nếu bạn muốn, tôi có thể vẽ tiếp sơ đồ luồng manager từ login -> boards -> board -> actions chi tiết hơn.