# FPT Task Manager

Day la bai tap lon web duoc dung theo huong don gian, de hoc va de thuyet trinh.

## 1. Y tuong chinh

Project duoc rut gon tu:

- Tai lieu `Use Case phân tích nghiệp vụ.docx` trong `BTL WEB`
- Code mau CRUD sinh vien trong `HieuBa_102136\Buoi 8`
- Cach to chuc file PHP va dashboard trong `web_QLVT`
- Cach hien thi board/list/card cua Trello

## 2. Chuc nang da lam

- Dang nhap bang tai khoan manager va member
- Dashboard tong hop so board, so task, task hoan thanh, task tre han
- Quan ly board cong viec
- Tao task, giao nguoi thuc hien, gan deadline, muc uu tien
- Hien thi task theo 4 cot trang thai:
  - Chua bat dau
  - Dang thuc hien
  - Cho duyet
  - Hoan thanh
- Keo tha task sang cot khac de doi trang thai
- Thanh vien chi cap nhat task duoc giao cho minh

## 3. Cau truc thu muc

- `config/`: ket noi DB va session
- `partials/`: header, footer
- `actions/`: xu ly luu, xoa, keo tha
- `assets/css/styles.css`: giao dien
- `assets/js/app.js`: JS modal va drag-drop
- `database.sql`: file import MySQL

## 4. Cach chay bang XAMPP

1. Dat thu muc `fpt_task_manager` vao `htdocs` cua XAMPP.
2. Mo `phpMyAdmin`.
3. Import file `database.sql`.
4. Sua file `config/database.php` neu may ban khac `DB_USER`, `DB_PASS`, `DB_NAME`.
5. Bat `Apache` va `MySQL`.
6. Truy cap:

```text
http://localhost/fpt_task_manager/login.php
```

## 5. Tai khoan demo

- Quan ly:
  - Username: `manager01`
  - Password: `123456`
- Thanh vien 1:
  - Username: `nhanvien01`
  - Password: `123456`
- Thanh vien 2:
  - Username: `nhanvien02`
  - Password: `123456`
- Thanh vien 3:
  - Username: `nhanvien03`
  - Password: `123456`

## 6. Goi y cach thuyet trinh

- Neu noi theo Use Case:
  - Actor chinh: Chu bang, Thanh vien
  - Nghiep vu chinh: tao board, tao task, giao viec, cap nhat trang thai, xem dashboard
- Neu noi theo giao dien:
  - Dashboard = tong quan tien do
  - Board = du an
  - Column = trang thai cong viec
  - Card = mot task cu the

## 7. Huong mo rong neu can nang cap

- Them binh luan cho task
- Them upload file minh chung
- Them bo loc theo nhan vien / deadline
- Them bieu do thong ke bang Chart.js
- Them lich deadline dang calendar
