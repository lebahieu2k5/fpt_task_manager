# Tài liệu Cập nhật Hệ thống - FPT Task Manager (Modernization Phase)

Tài liệu này tóm tắt các tính năng mới, sự thay đổi về cấu trúc và luồng hoạt động của hệ thống sau đợt cập nhật hiện đại hóa.

## 1. Các Tính năng & Cập nhật mới

### ⚙️ Tái cấu trúc Hệ thống (Refactoring)
- **Hợp nhất CRUD**: Toàn bộ logic xử lý Thêm/Sửa/Xóa của hệ thống cũ đã được chuyển về một đầu mối duy nhất tại `actions/task_legacy_action.php`.
- **Tổ chức thư mục**: Các trang giao diện cũ được gom nhóm vào thư mục `legacy_crud/` để làm gọn thư mục gốc của dự án.
- **Loại bỏ tiến độ %**: Theo yêu cầu, hệ thống đã loại bỏ cột "Tiến độ %" để tập trung vào quản lý Trạng thái (Status) của công việc.

### 📅 Cải tiến Lịch Deadline (`calendar.php`)
- **Bộ lọc Trạng thái**: Cho phép người dùng lọc danh sách task theo từng trạng thái cụ thể (Chưa bắt đầu, Đang làm, Hoàn thành, v.v.).
- **Điều hướng nhanh**: Thêm liên kết trực tiếp từ tên Bảng trong lịch về trang chi tiết Bảng (`board.php`), giúp người dùng chỉnh sửa task ngay lập tức.
- **Tối ưu hiển thị**: Loại bỏ các cột thừa, thêm Badge màu sắc cho Trạng thái theo bộ nhận diện FPT.

### 🎨 Thương hiệu & Giao diện (Branding)
- **Logo FPT Software**: Tích hợp logo vector chính thức của FPT Software.
- **Sea Blue Theme**: Cập nhật toàn bộ hệ thống sang tông màu Xanh nước biển (FPT Blue) hiện đại và chuyên nghiệp.
- **Trang Đăng nhập mới**: Thiết kế lại giao diện đăng nhập với logo và branding đồng bộ.

---

## 2. Luồng Hoạt động (Workflow)

### Luồng Đăng nhập & Điều hướng
1. Người dùng truy cập `login.php`, đăng nhập bằng tài khoản được cấp.
2. Sau khi đăng nhập, hệ thống sẽ chuyển hướng vào **Dashboard** (Trang tổng quan).
3. Thanh Sidebar bên trái cung cấp các lối tắt đến các khu vực chức năng (Bảng công việc, Lịch deadline, Báo cáo).

### Luồng Quản lý Công việc (Legacy CRUD)
1. Truy cập danh sách tại `legacy_crud/list.php`.
2. Khi thực hiện Thêm/Sửa/Xóa, yêu cầu sẽ được gửi tới `actions/task_legacy_action.php?action=...`.
3. Action controller xử lý logic nghiệp vụ và cập nhật Database thông qua `tasks.php` hoặc `repository.php`.
4. Sau khi xử lý xong, người dùng được chuyển hướng về trang danh sách.

### Luồng Theo dõi Deadline
1. Người dùng truy cập `calendar.php` để xem các task sắp đến hạn.
2. Sử dụng bộ lọc để xem các task "Chưa hoàn thành" hoặc "Đã xong".
3. Nhấn vào tên Bảng của task để nhảy nhanh tới Board tương ứng để thực hiện cập nhật nội dung chi tiết.

---

## 3. Cấu trúc Thư mục Quan trọng
- `/actions/task_legacy_action.php`: File xử lý tập trung logic CRUD cũ.
- `/legacy_crud/`: Chứa các file giao diện (`list.php`, `add.php`, `edit.php`).
- `/assets/css/styles.css`: Chứa toàn bộ định nghĩa màu sắc và giao diện mới.
- `/partials/header.php`: Chứa logo và cấu trúc thanh menu điều hướng.

---
*Tài liệu được cập nhật ngày 15/05/2026 bởi Antigravity Assistant.*
