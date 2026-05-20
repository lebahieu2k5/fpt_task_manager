# Hướng dẫn sử dụng FPT Task Manager

Tài liệu này hướng dẫn cách cài đặt, đăng nhập và sử dụng các chức năng chính của hệ thống quản lý công việc FPT Task Manager.

## 1. Mục đích hệ thống

FPT Task Manager là website quản lý công việc nội bộ theo dạng board, gần giống Trello ở mức đơn giản. Hệ thống hỗ trợ tạo bảng công việc, tạo task, giao việc cho nhân viên, cập nhật trạng thái, theo dõi tiến độ, xem báo cáo, quản lý thông báo và đồng bộ deadline sang Google Calendar.

Hệ thống có 3 vai trò:

- Admin: quản lý tài khoản, phân quyền, xem báo cáo, xem thống kê và quản lý thông báo.
- Quản lý: quản lý board, task, thành viên trong board, giám sát tiến độ và xem báo cáo.
- Nhân viên: nhận việc, cập nhật trạng thái, cập nhật phần trăm tiến độ và ghi chú công việc được giao.

## 2. Cách cài đặt bằng XAMPP

1. Mở thư mục dự án `fpt_task_manager`.
2. Copy thư mục này vào thư mục `htdocs` của XAMPP.
3. Mở XAMPP Control Panel.
4. Bật `Apache`.
5. Bật `MySQL`.
6. Mở trình duyệt và truy cập `http://localhost/phpmyadmin`.
7. Tạo hoặc import database từ file `database.sql`.
8. Nếu database đã được import trước khi thêm chức năng quên mật khẩu, import thêm file `database_update_forgot_password.sql`.
9. Kiểm tra file `config/database.php`.
10. Nếu dùng XAMPP mặc định thì giữ nguyên:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'fpt_task_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
```

11. Mở website tại:

```text
http://localhost/fpt_task_manager/login.php
```

## 3. Tài khoản demo

Admin:

```text
Username: admin01
Password: 123456
```

Quản lý:

```text
Username: manager01
Password: 123456
```

Nhân viên 1:

```text
Username: nhanvien01
Password: 123456
```

Nhân viên 2:

```text
Username: nhanvien02
Password: 123456
```

Nhân viên 3:

```text
Username: nhanvien03
Password: 123456
```

## 4. Cách đăng nhập

1. Truy cập `login.php`.
2. Nhập username và mật khẩu.
3. Bấm nút `Đăng nhập`.
4. Sau khi đăng nhập thành công, hệ thống chuyển vào trang Dashboard.

Nếu nhập thiếu thông tin, hệ thống báo cần nhập đầy đủ tên đăng nhập và mật khẩu. Nếu nhập sai tài khoản, hệ thống báo thông tin đăng nhập không đúng.

### 4.1. Quên mật khẩu

1. Tại màn hình đăng nhập, bấm `Quên mật khẩu?`.
2. Nhập username và email đúng với tài khoản.
3. Bấm `Tạo mã xác nhận`.
4. Hệ thống chuyển sang màn hình đặt lại mật khẩu và hiển thị mã xác nhận demo.
5. Nhập mã xác nhận, mật khẩu mới và xác nhận mật khẩu.
6. Bấm `Đặt lại mật khẩu`.
7. Đăng nhập lại bằng mật khẩu mới.

Lưu ý: mã xác nhận hết hạn sau 15 phút. Trên XAMPP chưa cấu hình SMTP, mã được hiển thị trực tiếp để thuận tiện demo.

## 5. Thanh điều hướng

Sau khi đăng nhập, thanh menu bên trái hiển thị các mục theo quyền của từng vai trò.

Admin nhìn thấy:

- Dashboard
- Phân quyền
- Báo cáo
- Thông báo
- Tài khoản

Quản lý nhìn thấy:

- Dashboard
- Bảng công việc
- Lịch deadline
- Báo cáo
- Thông báo
- Tài khoản

Nhân viên nhìn thấy:

- Dashboard
- Bảng công việc
- Lịch deadline
- Thông báo
- Tài khoản

## 6. Sử dụng Dashboard

Dashboard là trang tổng quan sau khi đăng nhập.

Các thông tin chính:

- Tổng số bảng công việc.
- Tổng số task.
- Số task đã hoàn thành.
- Số task trễ hạn.
- Tỉ lệ hoàn thành.
- Bảng công việc nổi bật.
- Task đến hạn sớm.
- Thông báo mới.
- Danh sách task trễ hạn.

Cách dùng:

1. Mở `Dashboard`.
2. Xem nhanh tiến độ toàn hệ thống hoặc các board liên quan đến tài khoản hiện tại.
3. Bấm vào một board trong phần bảng công việc nổi bật để xem chi tiết.
4. Kiểm tra danh sách task trễ hạn để ưu tiên xử lý.

## 7. Chức năng Admin

### 7.1. Phân quyền và quản lý tài khoản

Admin vào menu `Phân quyền` để quản lý tài khoản.

Admin có thể:

- Tạo tài khoản mới.
- Cập nhật họ tên.
- Cập nhật username.
- Cập nhật email.
- Cập nhật bộ phận.
- Gán vai trò `Admin`, `Quản lý` hoặc `Nhân viên`.
- Đổi mật khẩu cho tài khoản.

Cách tạo tài khoản:

1. Vào `Phân quyền`.
2. Bấm `Tạo tài khoản`.
3. Nhập họ tên, username, email, bộ phận.
4. Chọn vai trò.
5. Nhập mật khẩu tối thiểu 6 ký tự.
6. Bấm `Lưu tài khoản`.

Cách sửa tài khoản:

1. Vào `Phân quyền`.
2. Bấm `Sửa` ở dòng tài khoản cần chỉnh.
3. Cập nhật thông tin cần thay đổi.
4. Nếu không muốn đổi mật khẩu thì để trống ô mật khẩu.
5. Bấm `Lưu tài khoản`.

Lưu ý:

- Username không được trùng.
- Admin đang đăng nhập không thể tự hạ quyền tài khoản của mình.
- Email phải đúng định dạng nếu có nhập.

### 7.2. Quản lý thông báo

Admin vào menu `Thông báo` để tạo, sửa hoặc xóa thông báo nội bộ.

Cách tạo thông báo:

1. Vào `Thông báo`.
2. Bấm `Tạo thông báo`.
3. Nhập tiêu đề.
4. Chọn mức ưu tiên `Thấp`, `Trung bình` hoặc `Cao`.
5. Nhập nội dung.
6. Bấm `Lưu thông báo`.

Cách sửa thông báo:

1. Vào `Thông báo`.
2. Bấm `Sửa` tại thông báo cần chỉnh.
3. Cập nhật tiêu đề, nội dung hoặc mức ưu tiên.
4. Bấm `Lưu thông báo`.

Cách xóa thông báo:

1. Vào `Thông báo`.
2. Bấm `Xóa`.
3. Xác nhận xóa.

### 7.3. Xem báo cáo và thống kê

Admin vào menu `Báo cáo` để xem:

- Tổng số bảng công việc.
- Tổng số task.
- Tỉ lệ hoàn thành.
- Số task trễ hạn.
- Thống kê task theo trạng thái.
- Tiến độ theo bảng.
- Báo cáo theo nhân sự.
- Nút xuất báo cáo ra Excel, Word và PDF.

Admin dùng trang này để tổng hợp tình hình chung, kiểm tra board nào chậm tiến độ và nhân sự nào có nhiều task trễ hạn.

Cách xuất báo cáo:

1. Vào menu `Báo cáo`.
2. Bấm `Excel`, `Word` hoặc `PDF` trong khung xuất báo cáo.
3. Trình duyệt tải file báo cáo tổng hợp về máy.

## 8. Chức năng Quản lý

### 8.1. Tạo board công việc

Quản lý vào menu `Bảng công việc` để tạo board.

Cách tạo board:

1. Vào `Bảng công việc`.
2. Bấm `Tạo board mới`.
3. Nhập tên board.
4. Chọn ngày bắt đầu.
5. Chọn hạn hoàn thành.
6. Nhập mô tả ngắn.
7. Chọn thành viên tham gia board.
8. Bấm `Lưu board`.

Sau khi tạo, board xuất hiện trong danh sách bảng công việc.

### 8.2. Cập nhật board

Cách sửa board:

1. Vào `Bảng công việc`.
2. Bấm `Sửa` ở board cần chỉnh.
3. Cập nhật tên board, mô tả, ngày bắt đầu, hạn hoàn thành hoặc thành viên.
4. Bấm `Lưu board`.

Cách xóa board:

1. Vào `Bảng công việc`.
2. Bấm `Xóa`.
3. Xác nhận xóa.

Lưu ý: Khi xóa board, các task thuộc board đó cũng bị xóa theo quan hệ dữ liệu.

### 8.3. Tạo task

Quản lý mở chi tiết một board để tạo task.

Cách tạo task:

1. Vào `Bảng công việc`.
2. Bấm `Xem chi tiết` ở board cần thao tác.
3. Bấm `Tạo task`.
4. Nhập tên task.
5. Chọn độ ưu tiên.
6. Nhập mô tả.
7. Chọn người thực hiện.
8. Chọn trạng thái.
9. Chọn deadline.
10. Nhập tiến độ phần trăm.
11. Nhập ghi chú nếu cần.
12. Bấm `Lưu task`.

Các trạng thái task gồm:

- Chưa bắt đầu
- Đang thực hiện
- Chờ duyệt
- Hoàn thành

Nếu chọn trạng thái `Hoàn thành`, tiến độ được đưa về 100%.

### 8.4. Cập nhật task

Cách sửa task:

1. Mở chi tiết board.
2. Bấm `Sửa task` trên thẻ task.
3. Cập nhật tên task, mô tả, người thực hiện, trạng thái, deadline, tiến độ hoặc ghi chú.
4. Bấm `Lưu task`.

Cách xóa task:

1. Mở chi tiết board.
2. Bấm `Xóa` trên thẻ task.
3. Xác nhận xóa.

### 8.5. Kéo thả task để đổi trạng thái

Quản lý có thể kéo thả task giữa các cột trạng thái.

Cách dùng:

1. Mở chi tiết board.
2. Giữ chuột vào thẻ task.
3. Kéo task sang cột trạng thái mong muốn.
4. Thả chuột.
5. Hệ thống tự cập nhật trạng thái task.

Nếu kéo task sang cột `Hoàn thành`, tiến độ task được cập nhật thành 100%.

### 8.6. Giám sát và báo cáo

Quản lý dùng:

- Dashboard để xem nhanh task trễ hạn và task đến hạn sớm.
- Bảng công việc để theo dõi từng board.
- Báo cáo để xem tiến độ theo trạng thái, theo board và theo nhân sự.
- Lịch deadline để xem các task còn hạn xử lý.

## 9. Chức năng Nhân viên

### 9.1. Xem công việc được giao

Nhân viên vào `Bảng công việc`, mở board mà mình được thêm vào để xem task liên quan.

Trên mỗi task, nhân viên xem được:

- Tên task.
- Mô tả task.
- Người thực hiện.
- Deadline.
- Mức ưu tiên.
- Tiến độ hiện tại.
- Ghi chú cập nhật.

### 9.2. Cập nhật trạng thái và tiến độ

Nhân viên chỉ cập nhật được task được giao cho mình.

Cách cập nhật:

1. Mở chi tiết board.
2. Tìm task được giao cho mình.
3. Bấm `Cập nhật`.
4. Chọn trạng thái mới.
5. Nhập phần trăm tiến độ.
6. Nhập ghi chú cập nhật.
7. Bấm `Lưu task`.

Nhân viên không được sửa tên task, mô tả, người thực hiện, độ ưu tiên hoặc deadline.

### 9.3. Kéo thả task

Nhân viên có thể kéo thả task được giao cho mình sang cột trạng thái khác.

Nếu task không được giao cho nhân viên đó, task sẽ không cho kéo thả và không cho cập nhật.

## 10. Lịch deadline và Google Calendar

Menu `Lịch deadline` hiển thị các task có deadline và chưa hoàn thành.

Thông tin hiển thị:

- Tên task.
- Board.
- Người thực hiện.
- Deadline.
- Tiến độ.
- Nút Google Calendar.

Cách đồng bộ sang Google Calendar:

1. Vào `Lịch deadline`.
2. Tìm task cần thêm vào lịch.
3. Bấm `Google Calendar`.
4. Trình duyệt mở trang tạo sự kiện của Google Calendar.
5. Kiểm tra thông tin sự kiện.
6. Lưu sự kiện vào tài khoản Google.

## 11. Cập nhật tài khoản cá nhân

Tất cả vai trò đều có thể vào menu `Tài khoản`.

Người dùng có thể cập nhật:

- Họ tên.
- Email.
- Bộ phận.
- Mật khẩu mới.

Cách đổi mật khẩu:

1. Vào `Tài khoản`.
2. Nhập mật khẩu mới.
3. Nhập lại mật khẩu ở ô xác nhận.
4. Bấm `Lưu tài khoản`.

Lưu ý:

- Mật khẩu mới cần tối thiểu 6 ký tự.
- Nếu không muốn đổi mật khẩu thì để trống hai ô mật khẩu.
- Username không được sửa tại trang tài khoản cá nhân.

## 12. Quy tắc phân quyền

Admin:

- Có quyền quản lý tài khoản và phân quyền.
- Có quyền quản lý thông báo.
- Có quyền xem báo cáo và thống kê.
- Không trực tiếp tạo board trong giao diện hiện tại.

Quản lý:

- Có quyền tạo, sửa, xóa board.
- Có quyền thêm thành viên vào board.
- Có quyền tạo, sửa, xóa task.
- Có quyền giao task cho thành viên trong board.
- Có quyền xem báo cáo.

Nhân viên:

- Có quyền xem board mà mình được thêm vào.
- Có quyền xem task trong board liên quan.
- Có quyền cập nhật task được giao cho mình.
- Không có quyền tạo board, tạo task, xóa task hoặc phân quyền.

## 13. Gợi ý quy trình sử dụng khi thuyết trình

1. Đăng nhập bằng tài khoản Admin.
2. Mở `Phân quyền` để giới thiệu quản lý tài khoản và vai trò.
3. Mở `Thông báo` để tạo một thông báo mới.
4. Mở `Báo cáo` để giới thiệu thống kê tổng hợp.
5. Đăng xuất.
6. Đăng nhập bằng tài khoản Quản lý.
7. Mở `Bảng công việc`.
8. Tạo board mới hoặc mở board có sẵn.
9. Tạo task, giao cho nhân viên, đặt deadline và mức ưu tiên.
10. Kéo thả task qua các cột trạng thái.
11. Mở `Báo cáo` để xem tiến độ.
12. Đăng xuất.
13. Đăng nhập bằng tài khoản Nhân viên.
14. Mở board được tham gia.
15. Cập nhật trạng thái, tiến độ và ghi chú của task được giao.
16. Mở `Lịch deadline` để giới thiệu nút Google Calendar.

## 14. Lỗi thường gặp

Không vào được website:

- Kiểm tra Apache đã bật chưa.
- Kiểm tra đường dẫn có đúng `http://localhost/fpt_task_manager/login.php` không.
- Kiểm tra thư mục đã nằm trong `htdocs` chưa.

Không kết nối được database:

- Kiểm tra MySQL đã bật chưa.
- Kiểm tra đã import `database.sql` chưa.
- Kiểm tra `DB_NAME`, `DB_USER`, `DB_PASS` trong `config/database.php`.

Không đăng nhập được:

- Kiểm tra username và password.
- Dùng tài khoản demo trong phần trên.
- Kiểm tra bảng `users` trong database đã có dữ liệu mẫu chưa.
- Nếu quên mật khẩu báo thiếu database, import thêm `database_update_forgot_password.sql`.

Không thấy menu cần dùng:

- Kiểm tra vai trò tài khoản đang đăng nhập.
- Admin, Quản lý và Nhân viên có menu khác nhau.

Không sửa được task:

- Nhân viên chỉ sửa được task được giao cho chính mình.
- Nhân viên chỉ được sửa trạng thái, tiến độ và ghi chú.
- Quản lý mới được sửa đầy đủ thông tin task.

Không thấy task trong lịch deadline:

- Task phải có deadline.
- Task chưa được hoàn thành.
- Tài khoản phải có quyền xem board chứa task đó.

## 15. Ghi chú về dữ liệu

File `database.sql` đã có sẵn dữ liệu mẫu gồm:

- 1 tài khoản Admin.
- 1 tài khoản Quản lý.
- 3 tài khoản Nhân viên.
- 4 trạng thái task.
- 2 board mẫu.
- 2 thông báo mẫu.
- 6 task mẫu.

Khi cần làm lại dữ liệu từ đầu, có thể import lại file `database.sql` trong phpMyAdmin. Việc import lại sẽ xóa dữ liệu cũ theo các lệnh `DROP TABLE IF EXISTS`, vì vậy chỉ làm khi muốn reset database.
