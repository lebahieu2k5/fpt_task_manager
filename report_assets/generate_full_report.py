from pathlib import Path
from datetime import date

import matplotlib.pyplot as plt
from matplotlib.patches import FancyBboxPatch, FancyArrowPatch
from docx import Document
from docx.enum.section import WD_SECTION
from docx.enum.style import WD_STYLE_TYPE
from docx.enum.table import WD_CELL_VERTICAL_ALIGNMENT, WD_TABLE_ALIGNMENT
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Cm, Inches, Pt, RGBColor


ROOT = Path(__file__).resolve().parents[1]
ASSET_DIR = Path(__file__).resolve().parent / "generated"
OUTPUT = ROOT / "BAO_CAO_FPT_TASK_MANAGER_HOAN_CHINH.docx"
ASSET_DIR.mkdir(parents=True, exist_ok=True)

BLUE = "#0B5EA8"
ORANGE = "#F37021"
GREEN = "#16A05D"
RED = "#D63C45"
GRAY = "#667085"
LIGHT = "#F2F6FA"


def box(ax, x, y, w, h, text, color=BLUE, fontsize=10):
    patch = FancyBboxPatch(
        (x, y), w, h,
        boxstyle="round,pad=0.02,rounding_size=0.03",
        linewidth=1.4,
        edgecolor=color,
        facecolor="white",
    )
    ax.add_patch(patch)
    ax.text(x + w / 2, y + h / 2, text, ha="center", va="center",
            fontsize=fontsize, color="#111827", wrap=True)


def arrow(ax, start, end, color=GRAY):
    ax.add_patch(FancyArrowPatch(
        start, end, arrowstyle="-|>", mutation_scale=14,
        linewidth=1.25, color=color
    ))


def finish_diagram(fig, path):
    fig.savefig(path, dpi=190, bbox_inches="tight", facecolor="white")
    plt.close(fig)


def diagram_architecture():
    path = ASSET_DIR / "architecture.png"
    fig, ax = plt.subplots(figsize=(11, 6))
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    ax.text(0.5, 0.95, "KIẾN TRÚC TỔNG THỂ HỆ THỐNG", ha="center", va="center",
            fontsize=17, fontweight="bold", color=BLUE)
    box(ax, .06, .60, .22, .19, "TRÌNH DUYỆT\nHTML5 • CSS3 • Bootstrap\nJavaScript • Drag & Drop", ORANGE, 11)
    box(ax, .39, .60, .22, .19, "TẦNG XỬ LÝ PHP CORE\nTrang giao diện • Actions\nHelpers • Repository", BLUE, 11)
    box(ax, .72, .60, .22, .19, "CƠ SỞ DỮ LIỆU MYSQL\nUsers • Boards • Tasks\nNotifications • Password resets", GREEN, 11)
    arrow(ax, (.28, .695), (.39, .695))
    arrow(ax, (.61, .695), (.72, .695))
    arrow(ax, (.72, .64), (.61, .64))
    box(ax, .16, .22, .22, .17, "DỊCH VỤ NGOÀI\nGoogle Calendar URL", "#7C3AED", 11)
    box(ax, .46, .22, .22, .17, "XUẤT DỮ LIỆU\nExcel • Word • PDF", "#0F766E", 11)
    box(ax, .75, .22, .19, .17, "CÔNG CỤ MYSQL\nmysqldump • mysql", RED, 11)
    arrow(ax, (.50, .60), (.27, .39))
    arrow(ax, (.53, .60), (.57, .39))
    arrow(ax, (.83, .60), (.84, .39))
    finish_diagram(fig, path)
    return path


def diagram_use_cases():
    path = ASSET_DIR / "use_cases.png"
    fig, ax = plt.subplots(figsize=(12, 7))
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    ax.text(.5, .96, "SƠ ĐỒ CHỨC NĂNG THEO VAI TRÒ", ha="center", fontsize=17,
            fontweight="bold", color=BLUE)
    roles = [
        (.05, "ADMIN", RED, [
            "Quản lý tài khoản và phân quyền",
            "Lọc và xuất danh sách tài khoản",
            "Sao lưu, khôi phục dữ liệu",
            "Thông báo, báo cáo, hồ sơ cá nhân",
        ]),
        (.37, "QUẢN LÝ", ORANGE, [
            "Tạo, sửa, xóa board",
            "Thêm thành viên và tạo task",
            "Giao việc, kéo thả trạng thái",
            "Theo dõi tiến độ và xuất báo cáo",
        ]),
        (.69, "NHÂN VIÊN", GREEN, [
            "Xem board được tham gia",
            "Xem task được giao",
            "Cập nhật trạng thái và ghi chú",
            "Xem deadline và thông báo",
        ]),
    ]
    for x, title, color, items in roles:
        box(ax, x, .77, .26, .11, title, color, 12)
        y = .62
        for item in items:
            box(ax, x, y, .26, .095, item, color, 9)
            y -= .125
    finish_diagram(fig, path)
    return path


def diagram_flow(name, title, steps, colors=None):
    path = ASSET_DIR / f"{name}.png"
    fig, ax = plt.subplots(figsize=(12, 5.8))
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    ax.text(.5, .94, title, ha="center", fontsize=16, fontweight="bold", color=BLUE)
    n = len(steps)
    colors = colors or [BLUE] * n
    margin = .04
    gap = .025
    w = (1 - margin * 2 - gap * (n - 1)) / n
    for i, step in enumerate(steps):
        x = margin + i * (w + gap)
        box(ax, x, .44, w, .22, step, colors[i], 9)
        if i < n - 1:
            arrow(ax, (x + w, .55), (x + w + gap, .55))
    finish_diagram(fig, path)
    return path


def diagram_erd():
    path = ASSET_DIR / "erd.png"
    fig, ax = plt.subplots(figsize=(13, 8))
    ax.set_xlim(0, 1)
    ax.set_ylim(0, 1)
    ax.axis("off")
    ax.text(.5, .97, "SƠ ĐỒ QUAN HỆ DỮ LIỆU (ERD RÚT GỌN)", ha="center",
            fontsize=17, fontweight="bold", color=BLUE)
    entities = {
        "USERS": (.04, .61, .22, .23, "USERS\nPK id\nusername, password_hash\nrole, department, created_at"),
        "BOARDS": (.38, .66, .22, .20, "BOARDS\nPK id\nFK owner_id\nname, start_date, end_date"),
        "TASKS": (.72, .61, .23, .25, "TASKS\nPK id\nFK board_id, status_id\nFK assignee_id, created_by\ndeadline, priority, note"),
        "MEMBERS": (.38, .28, .22, .18, "BOARD_MEMBERS\nPK/FK board_id\nPK/FK user_id\njoined_at"),
        "STATUSES": (.72, .28, .23, .18, "TASK_STATUSES\nPK id\nstatus_name, status_key\ndisplay_order"),
        "NOTIFY": (.04, .28, .22, .18, "NOTIFICATIONS\nPK id\nFK created_by\ntitle, content, priority"),
        "RESET": (.04, .05, .22, .16, "PASSWORD_RESETS\nPK id\nFK user_id\nreset_code_hash, expires_at"),
    }
    for _, (x, y, w, h, text) in entities.items():
        box(ax, x, y, w, h, text, BLUE, 9)
    arrow(ax, (.26, .73), (.38, .76))
    arrow(ax, (.60, .76), (.72, .73))
    arrow(ax, (.26, .65), (.38, .40))
    arrow(ax, (.835, .46), (.835, .61))
    arrow(ax, (.15, .61), (.15, .46))
    ax.add_patch(FancyArrowPatch(
        (.04, .66), (.04, .13), arrowstyle="-|>", mutation_scale=14,
        linewidth=1.25, color=GRAY, connectionstyle="arc3,rad=0.22"
    ))
    ax.text(.31, .78, "1 - N", fontsize=9, color=RED)
    ax.text(.65, .78, "1 - N", fontsize=9, color=RED)
    ax.text(.29, .47, "N - N", fontsize=9, color=RED)
    finish_diagram(fig, path)
    return path


DIAGRAMS = {
    "architecture": diagram_architecture(),
    "use_cases": diagram_use_cases(),
    "overall": diagram_flow(
        "overall_flow",
        "LUỒNG NGHIỆP VỤ TỔNG THỂ",
        ["Admin tạo tài khoản\nvà phân quyền", "Quản lý tạo board\nvà thêm thành viên",
         "Quản lý tạo task,\ngiao việc, đặt deadline", "Nhân viên cập nhật\ntrạng thái và ghi chú",
         "Hệ thống tổng hợp\ndashboard, báo cáo"],
        [RED, ORANGE, ORANGE, GREEN, BLUE]
    ),
    "login": diagram_flow(
        "login_flow",
        "LUỒNG ĐĂNG NHẬP VÀ PHÂN QUYỀN",
        ["Nhập username\nvà mật khẩu", "Tìm user bằng\nprepared statement",
         "password_verify()", "Lưu thông tin vào\nPHP Session", "Hiển thị menu\ntheo vai trò"],
        [BLUE, BLUE, GREEN, ORANGE, RED]
    ),
    "validation": diagram_flow(
        "validation_flow",
        "KIỂM TRA MẬT KHẨU THEO TỪNG LỖI",
        ["Đủ ít nhất\n6 ký tự?", "Có chữ\nviết hoa?", "Có cả chữ\nvà số?",
         "Không có\nkhoảng trắng?", "Có ký tự\nđặc biệt?", "Cho phép lưu"],
        [RED, ORANGE, ORANGE, RED, BLUE, GREEN]
    ),
    "backup": diagram_flow(
        "backup_restore_flow",
        "LUỒNG SAO LƯU VÀ KHÔI PHỤC DỮ LIỆU",
        ["Admin xác thực\nquyền truy cập", "Sao lưu bằng\nmysqldump", "Tải file SQL\nvề máy",
         "Upload file SQL +\nCSRF + xác nhận", "Kiểm tra file\nvà dung lượng", "Khôi phục bằng\nmysql client"],
        [RED, GREEN, BLUE, ORANGE, RED, GREEN]
    ),
    "task": diagram_flow(
        "task_lifecycle",
        "VÒNG ĐỜI CỦA MỘT CÔNG VIỆC",
        ["Chưa bắt đầu", "Đang thực hiện", "Chờ duyệt", "Hoàn thành"],
        ["#64748B", ORANGE, "#7C3AED", GREEN]
    ),
    "erd": diagram_erd(),
}


def set_cell_shading(cell, fill):
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = OxmlElement("w:shd")
    shd.set(qn("w:fill"), fill)
    tc_pr.append(shd)


def set_cell_text(cell, text, bold=False, color=None, size=10):
    cell.text = ""
    p = cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.LEFT
    run = p.add_run(str(text))
    run.bold = bold
    run.font.name = "Times New Roman"
    run._element.rPr.rFonts.set(qn("w:eastAsia"), "Times New Roman")
    run.font.size = Pt(size)
    if color:
        run.font.color.rgb = RGBColor.from_string(color)
    cell.vertical_alignment = WD_CELL_VERTICAL_ALIGNMENT.CENTER


def add_page_number(paragraph):
    paragraph.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = paragraph.add_run()
    fld_char1 = OxmlElement("w:fldChar")
    fld_char1.set(qn("w:fldCharType"), "begin")
    instr_text = OxmlElement("w:instrText")
    instr_text.set(qn("xml:space"), "preserve")
    instr_text.text = "PAGE"
    fld_char2 = OxmlElement("w:fldChar")
    fld_char2.set(qn("w:fldCharType"), "end")
    run._r.append(fld_char1)
    run._r.append(instr_text)
    run._r.append(fld_char2)


def add_toc(paragraph):
    run = paragraph.add_run()
    fld_char1 = OxmlElement("w:fldChar")
    fld_char1.set(qn("w:fldCharType"), "begin")
    instr_text = OxmlElement("w:instrText")
    instr_text.set(qn("xml:space"), "preserve")
    instr_text.text = 'TOC \\o "1-3" \\h \\z \\u'
    fld_char2 = OxmlElement("w:fldChar")
    fld_char2.set(qn("w:fldCharType"), "separate")
    fld_char3 = OxmlElement("w:t")
    fld_char3.text = "Nhấn chuột phải và chọn Update Field để cập nhật mục lục."
    fld_char4 = OxmlElement("w:fldChar")
    fld_char4.set(qn("w:fldCharType"), "end")
    run._r.extend([fld_char1, instr_text, fld_char2, fld_char3, fld_char4])


doc = Document()
section = doc.sections[0]
section.top_margin = Cm(2.2)
section.bottom_margin = Cm(2.0)
section.left_margin = Cm(3.0)
section.right_margin = Cm(2.0)

normal = doc.styles["Normal"]
normal.font.name = "Times New Roman"
normal._element.rPr.rFonts.set(qn("w:eastAsia"), "Times New Roman")
normal.font.size = Pt(12)
normal.paragraph_format.line_spacing = 1.28
normal.paragraph_format.space_after = Pt(5)
normal.paragraph_format.first_line_indent = Cm(1)
normal.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY

for style_name, size, color in [
    ("Title", 22, "0B5EA8"),
    ("Heading 1", 17, "0B5EA8"),
    ("Heading 2", 14, "D35400"),
    ("Heading 3", 12, "166534"),
]:
    style = doc.styles[style_name]
    style.font.name = "Times New Roman"
    style._element.rPr.rFonts.set(qn("w:eastAsia"), "Times New Roman")
    style.font.size = Pt(size)
    style.font.bold = True
    style.font.color.rgb = RGBColor.from_string(color)

if "Caption Custom" not in [s.name for s in doc.styles]:
    cap = doc.styles.add_style("Caption Custom", WD_STYLE_TYPE.PARAGRAPH)
    cap.font.name = "Times New Roman"
    cap._element.rPr.rFonts.set(qn("w:eastAsia"), "Times New Roman")
    cap.font.size = Pt(10)
    cap.font.italic = True
    cap.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER

for sec in doc.sections:
    add_page_number(sec.footer.paragraphs[0])


def add_heading(text, level=1):
    p = doc.add_heading(text, level=level)
    p.paragraph_format.keep_with_next = True
    return p


def add_para(text, bold_prefix=None):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    if bold_prefix and text.startswith(bold_prefix):
        r1 = p.add_run(bold_prefix)
        r1.bold = True
        p.add_run(text[len(bold_prefix):])
    else:
        p.add_run(text)
    return p


def add_bullets(items):
    for item in items:
        p = doc.add_paragraph(style="List Bullet")
        p.paragraph_format.left_indent = Cm(.7)
        p.paragraph_format.first_line_indent = Cm(0)
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
        p.add_run(item)


def add_numbered(items):
    for item in items:
        p = doc.add_paragraph(style="List Number")
        p.paragraph_format.left_indent = Cm(.7)
        p.paragraph_format.first_line_indent = Cm(0)
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
        p.add_run(item)


def add_table(headers, rows, widths=None):
    table = doc.add_table(rows=1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.style = "Table Grid"
    hdr = table.rows[0].cells
    for i, h in enumerate(headers):
        set_cell_text(hdr[i], h, bold=True, color="FFFFFF", size=10)
        set_cell_shading(hdr[i], "0B5EA8")
    for row in rows:
        cells = table.add_row().cells
        for i, val in enumerate(row):
            set_cell_text(cells[i], val, size=9)
            if len(table.rows) % 2 == 0:
                set_cell_shading(cells[i], "F2F6FA")
    if widths:
        for row in table.rows:
            for idx, width in enumerate(widths):
                row.cells[idx].width = Cm(width)
    doc.add_paragraph()
    return table


def add_figure(path, caption, width=6.6):
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run().add_picture(str(path), width=Inches(width))
    cp = doc.add_paragraph(caption, style="Caption Custom")
    return cp


def page_break():
    doc.add_page_break()


def page_title(title, subtitle=None):
    add_heading(title, 1)
    if subtitle:
        p = doc.add_paragraph()
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
        r = p.add_run(subtitle)
        r.bold = True
        r.font.color.rgb = RGBColor.from_string("667085")


def standard_analysis(subject, benefit, risk, implementation):
    add_para(
        f"Về mặt nghiệp vụ, {subject} được thiết kế để {benefit}. "
        "Luồng xử lý được chia thành bước nhập dữ liệu, kiểm tra điều kiện, thực hiện nghiệp vụ, "
        "ghi nhận kết quả và phản hồi lại cho người dùng. Cách tổ chức này giúp mỗi thao tác có đầu vào, "
        "đầu ra và trách nhiệm rõ ràng, thuận lợi cho kiểm thử cũng như thuyết trình."
    )
    add_para(
        f"Rủi ro chính của chức năng là {risk}. Hệ thống hạn chế rủi ro bằng việc kiểm tra quyền ở phía máy chủ, "
        "không chỉ dựa vào việc ẩn nút trên giao diện. Dữ liệu nhập được chuẩn hóa trước khi truy vấn và các câu lệnh "
        "SQL sử dụng prepared statement để giảm nguy cơ chèn mã SQL."
    )
    add_para(
        f"Trong mã nguồn, chức năng được triển khai thông qua {implementation}. Việc tách trang hiển thị, action xử lý, "
        "hàm trợ giúp và repository làm cho chương trình dễ theo dõi hơn so với đặt toàn bộ logic trong một file."
    )


# Trang 1: Bìa
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("BỘ XÂY DỰNG\nBỘ GIÁO DỤC VÀ ĐÀO TẠO\nTRƯỜNG ĐẠI HỌC HÀNG HẢI VIỆT NAM")
r.bold = True
r.font.size = Pt(13)
doc.add_paragraph()
logo = ROOT / "assets" / "img" / "logo.png"
if logo.exists():
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p.add_run().add_picture(str(logo), width=Inches(1.35))
doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("BÁO CÁO BÀI TẬP LỚN WEB")
r.bold = True
r.font.size = Pt(20)
r.font.color.rgb = RGBColor.from_string("0B5EA8")
doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("WEBSITE QUẢN LÝ TIẾN ĐỘ CÔNG VIỆC\nFPT TASK MANAGER")
r.bold = True
r.font.size = Pt(20)
r.font.color.rgb = RGBColor.from_string("F37021")
for _ in range(7):
    doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("HẢI PHÒNG - 2026")
r.bold = True
r.font.size = Pt(13)

# Trang 2
page_break()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("TRƯỜNG ĐẠI HỌC HÀNG HẢI VIỆT NAM\nKHOA CÔNG NGHỆ THÔNG TIN")
r.bold = True
r.font.size = Pt(14)
doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("BÁO CÁO BÀI TẬP LỚN WEB")
r.bold = True
r.font.size = Pt(20)
doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
r = p.add_run("XÂY DỰNG WEBSITE QUẢN LÝ TIẾN ĐỘ CÔNG VIỆC\nCHO DOANH NGHIỆP")
r.bold = True
r.font.size = Pt(18)
r.font.color.rgb = RGBColor.from_string("0B5EA8")
doc.add_paragraph()
add_table(
    ["Nội dung", "Thông tin"],
    [
        ["Giảng viên hướng dẫn", "TS. Nguyễn Thuỳ Trang"],
        ["Nhóm thực hiện", "Nhóm 1"],
        ["Sinh viên", "Lê Bá Hiếu - 102136"],
        ["Sinh viên", "Hoàng Mạnh Hướng - 102145"],
        ["Sinh viên", "Đỗ Tuấn Hưng - 102144"],
        ["Công nghệ", "PHP Core, MySQL, HTML, CSS, JavaScript, Bootstrap"],
    ],
    [5, 10],
)
for _ in range(5):
    doc.add_paragraph()
p = doc.add_paragraph()
p.alignment = WD_ALIGN_PARAGRAPH.CENTER
p.add_run("HẢI PHÒNG - 2026").bold = True

# Trang 3
page_break()
page_title("LỜI CẢM ƠN VÀ LỜI CAM ĐOAN")
add_heading("Lời cảm ơn", 2)
add_para(
    "Nhóm thực hiện xin chân thành cảm ơn TS. Nguyễn Thuỳ Trang cùng các giảng viên Khoa Công nghệ thông tin, "
    "Trường Đại học Hàng Hải Việt Nam đã hướng dẫn kiến thức nền tảng về phân tích nghiệp vụ, thiết kế cơ sở dữ liệu, "
    "lập trình web và kiểm thử phần mềm. Những góp ý trong quá trình thực hiện giúp nhóm nhìn nhận hệ thống không chỉ "
    "ở góc độ giao diện mà còn ở tính đúng đắn của luồng nghiệp vụ, phân quyền và bảo vệ dữ liệu."
)
add_para(
    "Nhóm cũng cảm ơn các thành viên đã phối hợp khảo sát, xây dựng dữ liệu mẫu, phát triển chức năng và kiểm thử. "
    "Báo cáo này là kết quả của quá trình vừa học vừa hoàn thiện sản phẩm; do giới hạn thời gian, hệ thống vẫn còn "
    "những điểm có thể tiếp tục nâng cấp trong tương lai."
)
add_heading("Lời cam đoan", 2)
add_para(
    "Nhóm cam đoan nội dung báo cáo phản ánh đúng chương trình FPT Task Manager đang được triển khai trong thư mục dự án. "
    "Những tài liệu tham khảo được dùng để định hướng kiến thức đều được liệt kê ở cuối báo cáo. Các sơ đồ, bảng kiểm thử, "
    "mô tả nghiệp vụ và ví dụ minh họa được xây dựng từ việc phân tích trực tiếp mã nguồn và cơ sở dữ liệu của hệ thống."
)

# Trang 4
page_break()
page_title("TÓM TẮT ĐỀ TÀI")
add_para(
    "FPT Task Manager là website quản lý tiến độ công việc nội bộ theo mô hình bảng công việc. Hệ thống giải quyết bài toán "
    "phân công nhiệm vụ, theo dõi deadline, cập nhật trạng thái và tổng hợp báo cáo trong một nơi thống nhất. Ba vai trò "
    "Admin, Quản lý và Nhân viên có phạm vi truy cập khác nhau, giúp nghiệp vụ rõ ràng và giảm thao tác ngoài thẩm quyền."
)
add_para(
    "Điểm nổi bật của phiên bản hoàn thiện gồm quản lý tài khoản và phân quyền; bộ lọc tài khoản theo nhiều cột; xuất danh "
    "sách tài khoản và báo cáo ra Excel, Word, PDF; kiểm tra tuần tự email, username và mật khẩu; quên mật khẩu bằng mã xác "
    "nhận; sao lưu và khôi phục MySQL; quản lý board và task dạng Kanban; lịch deadline; tạo liên kết Google Calendar; "
    "dashboard, thông báo và báo cáo theo trạng thái, board, nhân sự."
)
add_table(
    ["Thuộc tính", "Mô tả"],
    [
        ["Kiến trúc", "Ứng dụng web PHP Core, xử lý phía máy chủ, dữ liệu MySQL"],
        ["Xác thực", "PHP Session, password_hash/password_verify"],
        ["Phân quyền", "Admin, Quản lý, Nhân viên"],
        ["Dữ liệu chính", "Tài khoản, board, thành viên, task, trạng thái, thông báo"],
        ["Đầu ra", "Giao diện web, Excel, Word, PDF, file sao lưu SQL"],
    ]
)

# Trang 5
page_break()
page_title("MỤC LỤC")
add_toc(doc.add_paragraph())
add_para(
    "Ghi chú: Mục lục là trường tự động của Microsoft Word. Sau khi mở tài liệu, chọn toàn bộ bằng Ctrl+A rồi nhấn F9 "
    "để cập nhật số trang và tiêu đề."
)

# Trang 6
page_break()
page_title("DANH MỤC TỪ VIẾT TẮT VÀ HÌNH MINH HỌA")
add_table(
    ["Từ viết tắt", "Ý nghĩa"],
    [
        ["Admin", "Quản trị viên hệ thống"],
        ["Manager", "Quản lý hoặc chủ bảng công việc"],
        ["Member", "Nhân viên tham gia board và thực hiện task"],
        ["Board", "Bảng công việc hoặc phạm vi dự án"],
        ["Task", "Công việc cụ thể cần thực hiện"],
        ["CRUD", "Tạo, đọc, cập nhật và xóa dữ liệu"],
        ["CSRF", "Tấn công giả mạo yêu cầu từ trình duyệt người dùng"],
        ["ERD", "Sơ đồ quan hệ thực thể của cơ sở dữ liệu"],
        ["PDO", "Thư viện PHP Data Objects dùng kết nối cơ sở dữ liệu"],
        ["SQL", "Ngôn ngữ truy vấn dữ liệu"],
    ]
)
add_bullets([
    "Hình 1: Kiến trúc tổng thể hệ thống.",
    "Hình 2: Phân nhóm chức năng theo ba vai trò.",
    "Hình 3: Luồng nghiệp vụ tổng thể.",
    "Hình 4: Luồng đăng nhập và phân quyền.",
    "Hình 5: Kiểm tra mật khẩu theo từng lỗi.",
    "Hình 6: Luồng sao lưu và khôi phục dữ liệu.",
    "Hình 7: Vòng đời task.",
    "Hình 8: ERD rút gọn.",
])

# Trang 7
page_break()
page_title("MỞ ĐẦU")
add_heading("1. Tính cấp thiết của đề tài", 2)
add_para(
    "Trong môi trường doanh nghiệp, tiến độ công việc thường bị phân tán qua tin nhắn, bảng tính và trao đổi trực tiếp. "
    "Khi số lượng dự án, phòng ban và nhân sự tăng lên, cách quản lý rời rạc làm phát sinh các vấn đề: khó biết ai đang "
    "thực hiện nhiệm vụ nào, deadline nào sắp đến, công việc nào bị trễ và tỷ lệ hoàn thành thực tế của từng dự án."
)
add_para(
    "Một hệ thống quản lý công việc tập trung giúp chuẩn hóa quy trình giao việc, giảm nguy cơ bỏ sót nhiệm vụ và tạo nguồn "
    "dữ liệu thống nhất để báo cáo. Đề tài lựa chọn mô hình Kanban đơn giản vì trực quan, phù hợp với nhóm nhỏ và dễ theo dõi "
    "sự dịch chuyển của công việc từ khi tạo đến khi hoàn thành."
)
add_heading("2. Mục tiêu", 2)
add_bullets([
    "Xây dựng website quản lý board, task, deadline, trạng thái và thành viên.",
    "Thiết lập phân quyền rõ ràng giữa Admin, Quản lý và Nhân viên.",
    "Hỗ trợ báo cáo, xuất file, sao lưu và khôi phục dữ liệu.",
    "Tăng độ tin cậy thông qua kiểm tra dữ liệu ở cả JavaScript và PHP.",
    "Tạo sản phẩm có thể chạy trực tiếp bằng XAMPP và MySQL.",
])

# Trang 8
page_break()
page_title("CHƯƠNG 1. KHẢO SÁT VÀ XÁC LẬP DỰ ÁN")
add_heading("1.1. Thực trạng quản lý công việc", 2)
add_para(
    "Qua khảo sát mô hình làm việc theo nhóm, công việc thường được giao bằng trao đổi trực tiếp, Zalo hoặc bảng tính. "
    "Mỗi công cụ giải quyết được một phần vấn đề nhưng thiếu liên kết dữ liệu. Tin nhắn thuận tiện để trao đổi nhưng khó "
    "tổng hợp; bảng tính dễ nhập dữ liệu nhưng thiếu cơ chế phân quyền và cảnh báo; báo cáo thủ công mất thời gian và có "
    "nguy cơ sai lệch."
)
add_table(
    ["Vấn đề", "Tác động", "Giải pháp trong hệ thống"],
    [
        ["Thông tin phân tán", "Khó tìm lại lịch sử và trạng thái", "Lưu tập trung theo board và task"],
        ["Không rõ người chịu trách nhiệm", "Dễ bỏ sót hoặc giao trùng", "Mỗi task gắn người thực hiện"],
        ["Khó theo dõi deadline", "Công việc trễ không được phát hiện sớm", "Dashboard và lịch deadline"],
        ["Báo cáo thủ công", "Tốn thời gian và dễ sai", "Tổng hợp tự động, xuất Excel/Word/PDF"],
        ["Dữ liệu thiếu phương án phục hồi", "Rủi ro mất dữ liệu", "Sao lưu và khôi phục file SQL"],
    ]
)
add_para(
    "Từ các vấn đề trên, yêu cầu cốt lõi của dự án là tạo một luồng làm việc khép kín: quản trị viên chuẩn bị tài khoản, "
    "quản lý lập board và giao task, nhân viên cập nhật kết quả, hệ thống tổng hợp dữ liệu và cung cấp báo cáo."
)

# Trang 9
page_break()
page_title("1.2. ĐỐI TƯỢNG, PHẠM VI VÀ PHƯƠNG PHÁP")
add_heading("1.2.1. Đối tượng và phạm vi", 2)
add_para(
    "Đối tượng nghiên cứu là quy trình quản lý tiến độ công việc trong nhóm dự án. Phạm vi phiên bản hiện tại tập trung vào "
    "quản lý người dùng, board, task, deadline, trạng thái, thông báo, báo cáo và bảo vệ dữ liệu. Hệ thống chưa hướng tới "
    "thay thế các nền tảng quản trị doanh nghiệp quy mô lớn; thay vào đó, nó cung cấp một giải pháp dễ triển khai, dễ học "
    "và phù hợp với yêu cầu bài tập lớn."
)
add_heading("1.2.2. Phương pháp thực hiện", 2)
add_numbered([
    "Khảo sát vấn đề và xác định ba nhóm người dùng chính.",
    "Mô hình hóa luồng nghiệp vụ, quyền truy cập và dữ liệu cần lưu.",
    "Thiết kế cơ sở dữ liệu quan hệ với khóa chính, khóa ngoại và quy tắc xóa liên quan.",
    "Cài đặt giao diện, xử lý phía máy chủ và truy vấn dữ liệu.",
    "Kiểm thử bằng dữ liệu hợp lệ, dữ liệu sai và các tình huống truy cập trái quyền.",
    "Đánh giá kết quả, giới hạn và hướng phát triển tiếp theo.",
])
add_heading("1.2.3. Tiêu chí thành công", 2)
add_bullets([
    "Người dùng chỉ truy cập được chức năng phù hợp vai trò.",
    "Dữ liệu tài khoản và mật khẩu được kiểm tra trước khi lưu.",
    "Quản lý có thể tạo board, giao việc và theo dõi tiến độ.",
    "Nhân viên chỉ cập nhật task được giao cho mình.",
    "Dữ liệu báo cáo, file xuất và file sao lưu có thể sử dụng được.",
])

# Trang 10
page_break()
page_title("1.3. YÊU CẦU CHỨC NĂNG THEO VAI TRÒ")
add_figure(DIAGRAMS["use_cases"], "Hình 1. Phân nhóm chức năng theo vai trò", 6.8)
add_table(
    ["Chức năng", "Admin", "Quản lý", "Nhân viên"],
    [
        ["Đăng nhập, quên mật khẩu, cập nhật hồ sơ", "Có", "Có", "Có"],
        ["Quản lý tài khoản và phân quyền", "Có", "Không", "Không"],
        ["Lọc và xuất danh sách tài khoản", "Có", "Không", "Không"],
        ["Sao lưu và khôi phục dữ liệu", "Có", "Không", "Không"],
        ["Tạo, sửa, xóa board/task", "Không", "Có", "Không"],
        ["Cập nhật task được giao", "Không", "Có", "Có"],
        ["Xem báo cáo", "Có", "Có", "Không"],
        ["Xem lịch deadline", "Không qua menu", "Có", "Có"],
    ]
)

# Trang 11
page_break()
page_title("1.4. YÊU CẦU PHI CHỨC NĂNG")
add_table(
    ["Nhóm yêu cầu", "Mô tả áp dụng"],
    [
        ["Bảo mật", "Mật khẩu băm; kiểm tra quyền server-side; prepared statement; CSRF cho khôi phục dữ liệu"],
        ["Tính đúng đắn", "Kiểm tra dữ liệu trước khi lưu; ràng buộc khóa ngoại; trạng thái task hợp lệ"],
        ["Khả dụng", "Giao diện nhất quán; thông báo lỗi rõ; hỗ trợ responsive"],
        ["Hiệu năng", "Truy vấn tổng hợp trực tiếp trong MySQL; giới hạn số task hiển thị ở dashboard/lịch"],
        ["Khả năng phục hồi", "Xuất bản sao lưu SQL và hỗ trợ import khôi phục"],
        ["Bảo trì", "Tách helpers, repository, actions, partials và assets"],
        ["Tương thích", "Chạy trên XAMPP, trình duyệt hiện đại, mã hóa UTF-8"],
    ]
)
add_para(
    "Yêu cầu phi chức năng không phải là phần phụ. Ví dụ, một chức năng tạo tài khoản có thể lưu đúng dữ liệu nhưng vẫn "
    "không đạt nếu mật khẩu lưu dạng văn bản thuần hoặc người không có quyền gọi trực tiếp action. Vì vậy, báo cáo đánh giá "
    "cả kết quả nghiệp vụ lẫn cách hệ thống ngăn dữ liệu sai và thao tác trái quyền."
)
standard_analysis(
    "việc xác định yêu cầu phi chức năng",
    "biến các kỳ vọng chung thành tiêu chí có thể kiểm thử",
    "chỉ chú trọng giao diện mà bỏ qua bảo mật, dữ liệu và khả năng phục hồi",
    "các hàm require_admin(), require_manager(), can_edit_task(), password_hash(), PDO và công cụ sao lưu MySQL"
)

# Trang 12
page_break()
page_title("1.5. LỰA CHỌN CÔNG NGHỆ")
add_table(
    ["Công nghệ", "Vai trò", "Lý do lựa chọn"],
    [
        ["PHP Core", "Xử lý request, session, nghiệp vụ và render HTML", "Dễ triển khai trên XAMPP, phù hợp phạm vi đề tài"],
        ["MySQL", "Lưu trữ dữ liệu quan hệ", "Ổn định, phổ biến, hỗ trợ khóa ngoại và truy vấn tổng hợp"],
        ["PDO", "Kết nối và truy vấn MySQL", "Prepared statement, giao diện nhất quán"],
        ["HTML/CSS", "Cấu trúc và trình bày giao diện", "Chuẩn web, dễ tùy chỉnh"],
        ["Bootstrap", "Grid, modal, bảng, nút, responsive", "Rút ngắn thời gian xây dựng giao diện"],
        ["JavaScript", "Modal, kéo thả task, kiểm tra dữ liệu trực tiếp", "Tăng phản hồi tức thời cho người dùng"],
        ["mysqldump/mysql", "Sao lưu và khôi phục", "Công cụ chuẩn đi kèm MySQL/XAMPP"],
    ]
)
add_para(
    "Hệ thống không sử dụng JWT hoặc refresh token. Xác thực thực tế dựa trên PHP Session; do đó báo cáo mô tả đúng cơ chế "
    "đang có thay vì đưa vào công nghệ chưa triển khai. Tương tự, Google Calendar được tích hợp bằng đường dẫn tạo sự kiện, "
    "không phải đồng bộ hai chiều qua Google API."
)
add_para(
    "Việc lựa chọn PHP Core giúp nhóm nhìn rõ chu kỳ xử lý request và cách phân quyền, nhưng cũng yêu cầu kỷ luật tổ chức mã "
    "nguồn. Các file action chỉ chịu trách nhiệm xử lý thao tác; repository chứa truy vấn; helpers chứa hàm dùng chung; "
    "partials tái sử dụng phần đầu và cuối trang."
)

# Trang 13
page_break()
page_title("CHƯƠNG 2. PHÂN TÍCH VÀ THIẾT KẾ HỆ THỐNG")
add_figure(DIAGRAMS["architecture"], "Hình 2. Kiến trúc tổng thể hệ thống", 7.0)
add_para(
    "Trình duyệt gửi request đến các trang PHP hoặc action. PHP đọc session để xác định người dùng, gọi repository truy vấn "
    "MySQL, thực hiện nghiệp vụ rồi render HTML hoặc chuyển hướng kèm thông báo flash. Các chức năng xuất báo cáo tạo nội dung "
    "tải xuống; chức năng sao lưu gọi công cụ MySQL; lịch deadline tạo liên kết mở Google Calendar."
)
add_para(
    "Kiến trúc này phù hợp ứng dụng nhỏ và vừa vì ít thành phần triển khai, nhưng vẫn có ranh giới trách nhiệm tương đối rõ. "
    "Điểm quan trọng là mọi action nhạy cảm đều phải kiểm tra quyền lại ở phía máy chủ, kể cả khi giao diện đã ẩn nút."
)

# Trang 14
page_break()
page_title("2.1. TÁC NHÂN VÀ USE CASE")
add_heading("2.1.1. Admin", 2)
add_para(
    "Admin chịu trách nhiệm quản trị người dùng và an toàn dữ liệu. Admin tạo tài khoản, gán vai trò, sửa thông tin, xóa tài "
    "khoản, lọc danh sách, xuất file, quản lý thông báo, xem báo cáo và thực hiện sao lưu/khôi phục. Hệ thống ngăn Admin đang "
    "đăng nhập tự hạ quyền của chính mình để tránh mất quyền quản trị ngoài ý muốn."
)
add_heading("2.1.2. Quản lý", 2)
add_para(
    "Quản lý tổ chức công việc theo board, chọn thành viên, tạo task, chỉ định người thực hiện, mức ưu tiên, trạng thái và "
    "deadline. Quản lý có thể sửa hoặc xóa task, kéo thả task giữa các cột và xem báo cáo tổng hợp."
)
add_heading("2.1.3. Nhân viên", 2)
add_para(
    "Nhân viên chỉ nhìn thấy board mà mình tham gia và chỉ cập nhật task được giao. Phạm vi cập nhật bị giới hạn vào trạng "
    "thái và ghi chú; các thuộc tính quản lý như tiêu đề, người thực hiện, mức ưu tiên và deadline không được phép sửa."
)
add_table(
    ["Tác nhân", "Đầu vào chính", "Kết quả chính"],
    [
        ["Admin", "Thông tin tài khoản, bộ lọc, file SQL", "Tài khoản, file xuất, dữ liệu được phục hồi"],
        ["Quản lý", "Board, thành viên, task, deadline", "Kế hoạch và tiến độ được tổ chức"],
        ["Nhân viên", "Trạng thái và ghi chú task", "Tiến độ thực tế được cập nhật"],
    ]
)

# Trang 15
page_break()
page_title("2.2. LUỒNG NGHIỆP VỤ TỔNG THỂ")
add_figure(DIAGRAMS["overall"], "Hình 3. Luồng nghiệp vụ tổng thể", 7.0)
add_numbered([
    "Admin tạo tài khoản và gán vai trò phù hợp với trách nhiệm của từng người.",
    "Quản lý đăng nhập, tạo board, xác định thời gian thực hiện và thêm thành viên.",
    "Quản lý tạo task, mô tả yêu cầu, chỉ định người thực hiện, trạng thái, ưu tiên và deadline.",
    "Nhân viên mở board được tham gia, xử lý task và cập nhật trạng thái hoặc ghi chú.",
    "Hệ thống tổng hợp dữ liệu thành dashboard, lịch deadline, thông báo và báo cáo.",
    "Admin hoặc Quản lý xuất báo cáo; Admin sao lưu dữ liệu định kỳ.",
])
add_para(
    "Luồng tổng thể thể hiện một chu trình dữ liệu khép kín. Dữ liệu đầu vào từ Admin và Quản lý trở thành nhiệm vụ cho Nhân "
    "viên; dữ liệu cập nhật từ Nhân viên quay lại thành số liệu giám sát. Nhờ vậy, báo cáo không cần nhập lại thủ công."
)

# Trang 16
page_break()
page_title("2.3. LUỒNG ĐĂNG NHẬP VÀ PHÂN QUYỀN")
add_figure(DIAGRAMS["login"], "Hình 4. Luồng đăng nhập và phân quyền", 7.0)
standard_analysis(
    "chức năng đăng nhập",
    "xác định đúng danh tính và thiết lập phạm vi chức năng cho phiên làm việc",
    "tài khoản giả mạo, mật khẩu sai hoặc truy cập trực tiếp URL không thuộc quyền",
    "login.php, find_user_by_username(), password_verify(), PHP Session và các hàm require_* trong helpers.php"
)
add_table(
    ["Tình huống", "Xử lý mong đợi"],
    [
        ["Chưa đăng nhập mở trang nội bộ", "Chuyển về login và hiển thị yêu cầu đăng nhập"],
        ["Nhân viên mở users.php", "Bị từ chối vì trang yêu cầu Admin"],
        ["Nhân viên sửa task người khác", "can_edit_task() trả false và action từ chối"],
        ["Admin mở trang quản trị dữ liệu", "Được phép sao lưu/khôi phục"],
    ]
)

# Trang 17
page_break()
page_title("2.4. LUỒNG QUẢN LÝ TÀI KHOẢN VÀ PHÂN QUYỀN")
add_numbered([
    "Admin mở trang Phân quyền; hệ thống gọi fetch_all_users() với các điều kiện lọc hiện tại.",
    "Admin chọn Tạo tài khoản hoặc Sửa; modal nhận dữ liệu và hiển thị các trường cần thiết.",
    "JavaScript kiểm tra trực tiếp email, username và mật khẩu để phản hồi sớm.",
    "Khi submit, actions/user_save.php kiểm tra lại toàn bộ dữ liệu ở phía máy chủ.",
    "Repository tạo mới hoặc cập nhật user bằng prepared statement; mật khẩu được băm trước khi lưu.",
    "Hệ thống chuyển về danh sách và hiển thị thông báo kết quả.",
])
add_table(
    ["Trường dữ liệu", "Quy tắc chính", "Ví dụ hợp lệ"],
    [
        ["Họ tên", "Bắt buộc khi tạo/sửa", "Nguyễn Văn An"],
        ["Username", "Bắt buộc, duy nhất, theo quy tắc hiện tại chỉ nhận chữ", "nguyenvanan"],
        ["Email", "Không khoảng trắng, đúng cấu trúc email", "an@fpt.com"],
        ["Vai trò", "admin, manager hoặc member", "manager"],
        ["Mật khẩu", "Ít nhất 6 ký tự, chữ hoa, chữ + số, không khoảng trắng, ký tự đặc biệt", "Abc123@"],
    ]
)
add_para(
    "Việc kiểm tra ở JavaScript tạo trải nghiệm tốt nhưng không thay thế kiểm tra phía PHP, vì request có thể được gửi trực "
    "tiếp mà không đi qua giao diện. Đây là nguyên tắc quan trọng khi trình bày phần bảo mật của hệ thống."
)

# Trang 18
page_break()
page_title("2.5. KIỂM TRA MẬT KHẨU THEO TỪNG LỖI")
add_figure(DIAGRAMS["validation"], "Hình 5. Thứ tự kiểm tra mật khẩu", 7.0)
add_para(
    "Hệ thống chỉ hiển thị một lỗi mật khẩu tại mỗi thời điểm. Người dùng xử lý xong điều kiện đầu tiên thì lỗi tiếp theo mới "
    "xuất hiện. Cách này tránh tình trạng một danh sách cảnh báo dài làm người dùng khó tập trung. Cùng một quy tắc được dùng "
    "ở tạo tài khoản, đổi mật khẩu trong hồ sơ và đặt lại mật khẩu."
)
add_table(
    ["Mật khẩu thử", "Lỗi đầu tiên hiển thị"],
    [
        ["A1@", "Mật khẩu phải có tối thiểu 6 ký tự"],
        ["abcdef1@", "Mật khẩu phải có ít nhất 1 chữ cái viết hoa"],
        ["Abcdef@", "Mật khẩu phải có cả chữ và số"],
        ["Abc 123@", "Mật khẩu không được chứa khoảng trắng"],
        ["Abc1234", "Mật khẩu phải có ít nhất 1 ký tự đặc biệt"],
        ["Abc123@", "Hợp lệ"],
    ]
)
add_para(
    "Phía PHP sử dụng hàm password_validation_error() làm nguồn quy tắc dùng chung. Phía JavaScript mô phỏng cùng thứ tự để "
    "hiển thị lỗi trực tiếp. Sau khi hợp lệ, mật khẩu không được lưu nguyên bản mà được chuyển thành password_hash."
)

# Trang 19
page_break()
page_title("2.6. LỌC VÀ XUẤT DANH SÁCH TÀI KHOẢN")
add_para(
    "Bộ lọc tài khoản hỗ trợ kết hợp họ tên, username, email, bộ phận, vai trò và ngày tạo. Các điều kiện văn bản dùng LIKE, "
    "vai trò dùng so sánh bằng, ngày tạo được chuyển thành mốc thời gian. Khi nhiều điều kiện có giá trị, repository nối bằng "
    "AND để chỉ trả về tài khoản thỏa mãn đồng thời."
)
add_table(
    ["Ví dụ lọc", "Điều kiện", "Kết quả mong đợi"],
    [
        ["Nhân sự Backend", "department chứa Backend", "Các tài khoản thuộc Backend Team"],
        ["Quản lý", "role = manager", "Chỉ tài khoản vai trò Quản lý"],
        ["Tài khoản tạo từ ngày", "created_at >= ngày 00:00:00", "Các tài khoản từ ngày chọn trở đi"],
        ["Kết hợp", "role = member AND email chứa @fpt.com", "Nhân viên có email FPT"],
    ]
)
add_para(
    "Các liên kết xuất Excel, Word và PDF giữ lại query string của bộ lọc. Vì vậy, file tải xuống chứa đúng danh sách đang "
    "hiển thị thay vì toàn bộ dữ liệu. Thông tin nhạy cảm như password_hash không được đưa vào file xuất."
)
standard_analysis(
    "chức năng lọc và xuất tài khoản",
    "giúp Admin tìm nhanh nhóm người dùng cần quản lý và tạo tài liệu phục vụ đối soát",
    "xuất nhầm dữ liệu không liên quan hoặc làm lộ thông tin nhạy cảm",
    "users.php, actions/user_export_helpers.php và ba action xuất Excel, Word, PDF"
)

# Trang 20
page_break()
page_title("2.7. LUỒNG QUÊN VÀ ĐẶT LẠI MẬT KHẨU")
add_numbered([
    "Người dùng nhập username và email đã đăng ký.",
    "Hệ thống tìm tài khoản khớp và tạo mã xác nhận sáu chữ số.",
    "Mã không lưu nguyên bản; hệ thống lưu reset_code_hash cùng thời hạn hết hiệu lực.",
    "Người dùng nhập mã, mật khẩu mới và xác nhận mật khẩu.",
    "Hệ thống kiểm tra mã còn hạn, chưa dùng, mật khẩu hợp lệ và hai lần nhập khớp nhau.",
    "Mật khẩu mới được băm, mã xác nhận được đánh dấu đã dùng, người dùng quay lại đăng nhập.",
])
add_table(
    ["Biện pháp", "Ý nghĩa"],
    [
        ["Mã có thời hạn", "Giảm nguy cơ sử dụng mã cũ"],
        ["Mã lưu dạng hash", "Hạn chế lộ mã nếu dữ liệu bị đọc trái phép"],
        ["Mã chỉ dùng một lần", "Ngăn tái sử dụng sau khi đổi mật khẩu"],
        ["Đối chiếu username + email", "Xác định đúng tài khoản cần khôi phục"],
        ["Kiểm tra mật khẩu dùng chung", "Giữ chính sách nhất quán"],
    ]
)
add_para(
    "Trong môi trường demo chưa cấu hình SMTP, mã xác nhận có thể được hiển thị để thuận tiện kiểm thử. Khi triển khai thực tế, "
    "mã phải được gửi qua kênh xác minh đáng tin cậy và không hiển thị công khai."
)

# Trang 21
page_break()
page_title("2.8. LUỒNG SAO LƯU VÀ KHÔI PHỤC DỮ LIỆU")
add_figure(DIAGRAMS["backup"], "Hình 6. Luồng sao lưu và khôi phục dữ liệu", 7.0)
add_para(
    "Sao lưu tạo file SQL chứa cấu trúc và dữ liệu bằng mysqldump. Khôi phục là thao tác nguy hiểm hơn nên phải qua nhiều lớp "
    "xác nhận: chỉ Admin được truy cập, request phải là POST, CSRF token hợp lệ, nội dung xác nhận đúng, file có đuôi .sql, "
    "dung lượng từ 1 byte đến 40 MB và mẫu nội dung có dấu hiệu là SQL."
)
add_table(
    ["Bước kiểm tra khôi phục", "Trường hợp bị từ chối"],
    [
        ["Quyền Admin", "Manager hoặc Member gọi trực tiếp action"],
        ["CSRF token", "Request không xuất phát từ form hợp lệ"],
        ["Chuỗi xác nhận", "Không nhập chính xác KHOI PHUC DU LIEU"],
        ["Phần mở rộng", "File không phải .sql"],
        ["Dung lượng", "File rỗng hoặc vượt 40 MB"],
        ["Nội dung", "Không chứa lệnh SQL nhận biết được"],
    ]
)

# Trang 22
page_break()
page_title("2.9. QUẢN LÝ BOARD VÀ THÀNH VIÊN")
add_para(
    "Board là đơn vị tổ chức công việc cấp cao nhất trong nghiệp vụ quản lý dự án. Mỗi board có tên, mô tả, chủ sở hữu, ngày "
    "bắt đầu, ngày kết thúc và danh sách thành viên. Quan hệ nhiều-nhiều giữa board và người dùng được lưu tại board_members."
)
add_numbered([
    "Quản lý mở danh sách board và chọn tạo mới.",
    "Nhập tên, mô tả, khoảng thời gian và chọn thành viên.",
    "Action kiểm tra quyền Quản lý và dữ liệu bắt buộc.",
    "Board được lưu; các thành viên được đồng bộ vào board_members.",
    "Khi mở chi tiết board, hệ thống truy vấn thông tin board, thành viên và task.",
])
add_table(
    ["Quy tắc", "Lý do"],
    [
        ["Chỉ Quản lý tạo/sửa/xóa board", "Giữ trách nhiệm lập kế hoạch rõ ràng"],
        ["Member chỉ xem board được thêm vào", "Ngăn đọc dữ liệu dự án không liên quan"],
        ["Xóa board kéo theo board_members và tasks", "Tránh dữ liệu mồ côi"],
        ["Chủ board được tính là một thành viên", "Phản ánh đúng số người tham gia"],
    ]
)
standard_analysis(
    "chức năng quản lý board",
    "gom các task có cùng mục tiêu và nhóm thực hiện vào một phạm vi quản lý",
    "người ngoài board xem dữ liệu hoặc việc xóa board để lại task không còn cha",
    "boards.php, board.php, actions/board_save.php, actions/board_delete.php và các truy vấn trong repository.php"
)

# Trang 23
page_break()
page_title("2.10. QUẢN LÝ TASK VÀ VÒNG ĐỜI CÔNG VIỆC")
add_figure(DIAGRAMS["task"], "Hình 7. Vòng đời cơ bản của task", 7.0)
add_para(
    "Task chứa tên, mô tả, mức ưu tiên, người thực hiện, deadline, trạng thái, ghi chú, vị trí và người tạo. Bốn trạng thái "
    "được lưu trong bảng task_statuses thay vì viết cố định trong từng task, giúp giao diện sắp xếp cột theo display_order."
)
add_table(
    ["Thuộc tính", "Vai trò trong nghiệp vụ"],
    [
        ["Priority", "Giúp xác định mức độ cần ưu tiên xử lý"],
        ["Assignee", "Xác định người chịu trách nhiệm thực hiện"],
        ["Deadline", "Mốc hoàn thành và cơ sở xác định trễ hạn"],
        ["Status", "Giai đoạn hiện tại của task"],
        ["Note", "Thông tin cập nhật trong quá trình thực hiện"],
        ["Position order", "Duy trì thứ tự task trong từng cột"],
    ]
)
add_para(
    "Quản lý được phép sửa đầy đủ task; Nhân viên chỉ cập nhật trạng thái và ghi chú của task được giao. Khi kéo thả, action "
    "task_move.php kiểm tra quyền chỉnh sửa trước khi cập nhật trạng thái, vì thuộc tính draggable ở giao diện không đủ để "
    "bảo vệ nghiệp vụ."
)

# Trang 24
page_break()
page_title("2.11. DASHBOARD, LỊCH, THÔNG BÁO VÀ BÁO CÁO")
add_table(
    ["Phân hệ", "Dữ liệu hiển thị", "Giá trị sử dụng"],
    [
        ["Dashboard", "Tổng board, tổng task, hoàn thành, trễ hạn, task sắp tới", "Nắm nhanh tình hình"],
        ["Lịch deadline", "Task có deadline, trạng thái, người thực hiện", "Ưu tiên theo thời gian"],
        ["Google Calendar", "Liên kết tạo sự kiện từ task", "Đưa deadline vào lịch cá nhân"],
        ["Thông báo", "Tiêu đề, nội dung, ưu tiên, thời gian", "Truyền đạt thông tin nội bộ"],
        ["Báo cáo", "Trạng thái, tiến độ board, số liệu theo nhân sự", "Đánh giá và đối soát"],
        ["Xuất báo cáo", "Excel, Word, PDF", "Lưu trữ và chia sẻ"],
    ]
)
add_para(
    "Dashboard và báo cáo đều dùng dữ liệu thật từ repository. Với Admin và Quản lý, số liệu có phạm vi toàn hệ thống; với "
    "Nhân viên, dashboard chỉ tính các board liên quan. Cách xử lý này bảo đảm một giao diện có thể tái sử dụng nhưng kết quả "
    "vẫn tuân theo quyền."
)
add_para(
    "Tích hợp Google Calendar tạo URL chứa tiêu đề task, ngày bắt đầu/kết thúc, tên board, người thực hiện và mô tả. Người dùng "
    "được chuyển sang Google Calendar để kiểm tra rồi tự lưu sự kiện. Đây là tích hợp một chiều, đơn giản và phù hợp phạm vi."
)

# Trang 25
page_break()
page_title("2.12. THIẾT KẾ CƠ SỞ DỮ LIỆU")
add_figure(DIAGRAMS["erd"], "Hình 8. ERD rút gọn của hệ thống", 7.1)
add_para(
    "Cơ sở dữ liệu gồm bảy bảng chính. Users là trung tâm danh tính; boards liên kết với owner; board_members tạo quan hệ "
    "nhiều-nhiều; tasks liên kết board, trạng thái, người thực hiện và người tạo; notifications liên kết người tạo; "
    "password_resets lưu yêu cầu đặt lại mật khẩu."
)
add_para(
    "Các bảng dùng InnoDB, utf8mb4 và utf8mb4_unicode_ci để hỗ trợ tiếng Việt. Khóa ngoại kiểm soát tính toàn vẹn; các quy tắc "
    "ON DELETE CASCADE hoặc SET NULL được lựa chọn theo ý nghĩa nghiệp vụ."
)

# Trang 26
page_break()
page_title("2.13. MÔ TẢ CHI TIẾT CÁC BẢNG DỮ LIỆU")
add_table(
    ["Bảng", "Khóa/quan hệ", "Mục đích"],
    [
        ["users", "PK id; username unique", "Danh tính, vai trò, bộ phận, mật khẩu băm"],
        ["password_resets", "FK user_id", "Mã đặt lại mật khẩu, hạn dùng và trạng thái sử dụng"],
        ["boards", "FK owner_id", "Thông tin bảng công việc và chủ sở hữu"],
        ["board_members", "PK kép board_id + user_id", "Danh sách thành viên của board"],
        ["task_statuses", "status_key unique", "Danh mục trạng thái và thứ tự hiển thị"],
        ["tasks", "FK board/status/assignee/creator", "Nội dung và tiến độ công việc"],
        ["notifications", "FK created_by", "Thông báo nội bộ"],
    ]
)
add_heading("Quy tắc xóa dữ liệu", 2)
add_bullets([
    "Xóa user làm chủ board sẽ xóa board và các task thuộc board theo chuỗi CASCADE.",
    "Xóa user là thành viên sẽ xóa quan hệ board_members.",
    "Xóa người được giao task đặt assignee_id về NULL để task vẫn tồn tại.",
    "Xóa board sẽ xóa thành viên và task của board.",
    "Xóa user sẽ xóa các yêu cầu đặt lại mật khẩu liên quan.",
])
add_para(
    "Các quy tắc trên cần được cân nhắc khi triển khai thực tế vì thao tác xóa tài khoản chủ board có phạm vi ảnh hưởng lớn. "
    "Một hướng nâng cấp là dùng trạng thái khóa tài khoản thay vì xóa vật lý."
)

# Trang 27
page_break()
page_title("2.14. THIẾT KẾ BẢO MẬT VÀ TOÀN VẸN DỮ LIỆU")
add_table(
    ["Nguy cơ", "Biện pháp hiện có", "Đánh giá"],
    [
        ["Lộ mật khẩu", "password_hash và password_verify", "Tốt trong phạm vi đề tài"],
        ["SQL Injection", "PDO prepared statement", "Giảm đáng kể nguy cơ"],
        ["Truy cập trái quyền", "require_admin/manager/login và can_edit_task", "Kiểm tra ở server"],
        ["XSS khi hiển thị", "Hàm e() dùng htmlspecialchars", "Cần sử dụng nhất quán"],
        ["CSRF", "Token ở chức năng import", "Nên mở rộng sang các form thay đổi dữ liệu khác"],
        ["Mất dữ liệu", "Sao lưu và khôi phục SQL", "Có phương án phục hồi"],
        ["Sai mã hóa tiếng Việt", "UTF-8 và utf8mb4", "Hỗ trợ dữ liệu tiếng Việt"],
    ]
)
add_para(
    "Một điểm đáng chú ý là bảo mật phải được triển khai theo nhiều lớp. Việc ẩn menu chỉ giúp giao diện gọn, không phải là "
    "kiểm soát quyền. Action vẫn cần require_admin() hoặc require_manager(). Tương tự, kiểm tra JavaScript không thể thay cho "
    "kiểm tra PHP vì người dùng có thể gửi request thủ công."
)
add_para(
    "Phiên bản tiếp theo nên thêm CSRF cho toàn bộ thao tác tạo, sửa, xóa; giới hạn số lần đăng nhập sai; ghi nhật ký thay đổi; "
    "và dùng HTTPS khi triển khai ngoài localhost."
)

# Trang 28
page_break()
page_title("CHƯƠNG 3. XÂY DỰNG CHƯƠNG TRÌNH")
add_heading("3.1. Cấu trúc mã nguồn", 2)
add_table(
    ["Thành phần", "Trách nhiệm"],
    [
        ["config/", "Kết nối MySQL và thiết lập session"],
        ["partials/", "Header, menu theo quyền và footer dùng chung"],
        ["actions/", "Xử lý form, lưu, xóa, xuất file, backup/import"],
        ["assets/css/", "Giao diện và responsive"],
        ["assets/js/", "Modal, kéo thả và kiểm tra dữ liệu trực tiếp"],
        ["helpers.php", "Phân quyền, định dạng, CSRF, chính sách mật khẩu"],
        ["repository.php", "Truy vấn và cập nhật dữ liệu"],
        ["Các trang PHP", "Render giao diện từng chức năng"],
    ]
)
add_para(
    "Mỗi request bắt đầu từ bootstrap.php để nạp session, kết nối dữ liệu, helpers và repository. Trang hiển thị chuẩn bị dữ "
    "liệu rồi gọi partials/header.php; action xử lý dữ liệu rồi chuyển hướng kèm flash message. Cách tổ chức này tạo luồng "
    "dễ theo dõi khi thuyết trình."
)
add_heading("3.2. Quy trình xây dựng", 2)
add_numbered([
    "Khởi tạo dữ liệu mẫu và các vai trò.",
    "Xây dựng đăng nhập, session và phân quyền.",
    "Xây dựng board, task và kéo thả trạng thái.",
    "Hoàn thiện dashboard, lịch, thông báo và báo cáo.",
    "Bổ sung bộ lọc, xuất file, quên mật khẩu, sao lưu/khôi phục.",
    "Kiểm tra lỗi dữ liệu, mã hóa tiếng Việt và trải nghiệm người dùng.",
])

# Trang 29
page_break()
page_title("3.3. GIAO DIỆN DASHBOARD VÀ ĐIỀU HƯỚNG")
add_para(
    "Thanh điều hướng bên trái thay đổi theo vai trò. Admin thấy Phân quyền và Sao lưu dữ liệu; Quản lý thấy Bảng công việc, "
    "Lịch deadline và Báo cáo; Nhân viên thấy Bảng công việc và Lịch deadline. Mọi vai trò đều có Dashboard, Thông báo và "
    "Tài khoản."
)
add_table(
    ["Khu vực Dashboard", "Cách đọc"],
    [
        ["Thẻ số liệu", "Tổng board, tổng task, số hoàn thành, số trễ hạn"],
        ["Board nổi bật", "Tên, mô tả, số thành viên và số task"],
        ["Task đến hạn sớm", "Công việc cần ưu tiên theo deadline"],
        ["Thông báo mới", "Thông tin nội bộ theo mức ưu tiên"],
        ["Task trễ hạn", "Danh sách cần xử lý hoặc điều chỉnh kế hoạch"],
    ]
)
add_para(
    "Ví dụ: nếu hệ thống có 10 task, trong đó 6 task hoàn thành và 2 task quá hạn, Dashboard hiển thị tỷ lệ hoàn thành 60%, "
    "số hoàn thành là 6 và số trễ hạn là 2. Người quản lý có thể mở board liên quan để kiểm tra nguyên nhân."
)
add_para(
    "Giao diện dùng card, badge và màu trạng thái để người dùng nhận biết nhanh. Tuy nhiên, màu chỉ là hỗ trợ; nội dung chữ vẫn "
    "được giữ để tránh phụ thuộc hoàn toàn vào khả năng phân biệt màu."
)

# Trang 30
page_break()
page_title("3.4. GIAO DIỆN ADMIN")
add_heading("Trang Phân quyền", 2)
add_para(
    "Trang Phân quyền gồm nhóm nút xuất file, bộ lọc tài khoản, bảng kết quả và modal tạo/sửa. Khi bộ lọc hoạt động, số điều "
    "kiện được hiển thị và file xuất giữ đúng kết quả. Modal cung cấp các trường họ tên, username, email, bộ phận, vai trò và "
    "mật khẩu."
)
add_heading("Trang Sao lưu dữ liệu", 2)
add_para(
    "Trang quản lý dữ liệu chia thành khu vực tải bản sao lưu, form khôi phục và bảng thống kê dung lượng từng bảng. Cảnh báo "
    "được đặt trước form khôi phục để nhấn mạnh khả năng ghi đè hoặc xóa dữ liệu hiện tại."
)
add_heading("Ví dụ thao tác Admin", 2)
add_numbered([
    "Đăng nhập admin01 và mở Phân quyền.",
    "Lọc vai trò Nhân viên và bộ phận Backend.",
    "Xuất danh sách kết quả sang PDF để đối soát.",
    "Tạo tài khoản mới với mật khẩu Abc123@.",
    "Mở Sao lưu dữ liệu và tải file SQL trước khi thay đổi lớn.",
])
add_para(
    "Khi demo, nên cố tình nhập mật khẩu sai từng bước để thể hiện cơ chế cảnh báo tuần tự, sau đó sửa đến khi form được chấp nhận."
)

# Trang 31
page_break()
page_title("3.5. GIAO DIỆN QUẢN LÝ")
add_para(
    "Quản lý làm việc chủ yếu tại danh sách board, chi tiết board, lịch deadline và báo cáo. Trong chi tiết board, task được "
    "chia thành bốn cột theo trạng thái. Mỗi card thể hiện mức ưu tiên, tên, mô tả, người thực hiện, deadline, ghi chú và cảnh "
    "báo trễ hạn."
)
add_table(
    ["Thao tác", "Phản hồi của hệ thống"],
    [
        ["Tạo board", "Board xuất hiện trong danh sách cùng số thành viên và task"],
        ["Tạo task", "Task xuất hiện ở cột trạng thái đã chọn"],
        ["Giao task", "Người thực hiện phải nằm trong board"],
        ["Kéo thả task", "Trạng thái được cập nhật ở server"],
        ["Sửa task", "Tên, mô tả, ưu tiên, người thực hiện, trạng thái, deadline và ghi chú được cập nhật"],
        ["Xóa task", "Yêu cầu xác nhận trước khi xóa"],
    ]
)
add_para(
    "Ví dụ: quản lý tạo task “Kiểm thử chức năng xuất PDF”, giao cho nhân viên QA, mức ưu tiên Cao và deadline ngày mai. "
    "Nhân viên thấy task trong board; sau khi hoàn thành kiểm thử, nhân viên chuyển task sang Chờ duyệt và ghi chú kết quả."
)

# Trang 32
page_break()
page_title("3.6. GIAO DIỆN NHÂN VIÊN")
add_para(
    "Nhân viên nhìn thấy các board mà mình được thêm vào. Trong chi tiết board, nút cập nhật chỉ xuất hiện đối với task được "
    "giao cho chính người đang đăng nhập. Khi mở modal, các trường dành cho quản lý bị khóa; nhân viên chỉ thay đổi trạng thái "
    "và ghi chú."
)
add_heading("Kịch bản sử dụng", 2)
add_numbered([
    "Nhân viên đăng nhập và kiểm tra Dashboard để xem task sắp đến hạn.",
    "Mở Bảng công việc và chọn board liên quan.",
    "Tìm task được giao, đọc mô tả và deadline.",
    "Cập nhật trạng thái từ Chưa bắt đầu sang Đang thực hiện.",
    "Ghi chú tiến độ hoặc khó khăn cần hỗ trợ.",
    "Khi hoàn tất, chuyển sang Chờ duyệt hoặc Hoàn thành theo quy trình nhóm.",
])
add_para(
    "Giới hạn quyền giúp dữ liệu kế hoạch không bị thay đổi ngoài ý muốn. Nhân viên không thể tự sửa deadline, mức ưu tiên hoặc "
    "chuyển task cho người khác. Nếu cần thay đổi các thông tin này, nhân viên trao đổi với Quản lý."
)
add_heading("Lịch deadline", 2)
add_para(
    "Nhân viên có thể lọc task theo trạng thái, mở board từ bảng lịch hoặc tạo sự kiện Google Calendar. Các task hoàn thành "
    "được loại khỏi danh sách mặc định để người dùng tập trung vào công việc còn lại."
)

# Trang 33
page_break()
page_title("CHƯƠNG 4. KIỂM THỬ HỆ THỐNG")
add_heading("4.1. Chiến lược kiểm thử", 2)
add_para(
    "Kiểm thử được thực hiện theo hướng chức năng và quyền truy cập. Mỗi nghiệp vụ được kiểm tra với dữ liệu hợp lệ, dữ liệu "
    "thiếu, dữ liệu sai định dạng và người dùng không có quyền. Ngoài kết quả trên giao diện, cần kiểm tra dữ liệu trong MySQL "
    "để xác nhận thao tác thực sự đúng."
)
add_table(
    ["Nhóm kiểm thử", "Mục tiêu"],
    [
        ["Xác thực", "Đăng nhập đúng/sai, quên mật khẩu, mã hết hạn"],
        ["Phân quyền", "Mỗi vai trò chỉ dùng đúng chức năng"],
        ["Dữ liệu nhập", "Email, username, mật khẩu, deadline, file upload"],
        ["Board/Task", "Tạo, sửa, xóa, giao việc, kéo thả"],
        ["Báo cáo/Xuất file", "Số liệu đúng và file mở được"],
        ["Sao lưu/Khôi phục", "File SQL hợp lệ và dữ liệu phục hồi đúng"],
        ["Giao diện", "Hiển thị tiếng Việt, responsive và thông báo lỗi"],
    ]
)
add_para(
    "Tiêu chí đạt là kết quả thực tế khớp kết quả mong đợi, không phát sinh cảnh báo PHP, không tạo dữ liệu sai và không cho "
    "phép vượt quyền bằng cách gọi trực tiếp URL action."
)

# Trang 34
page_break()
page_title("4.2. KIỂM THỬ ĐĂNG NHẬP, TÀI KHOẢN VÀ MẬT KHẨU")
add_table(
    ["Mã", "Tình huống", "Dữ liệu", "Kết quả mong đợi"],
    [
        ["TC-A01", "Đăng nhập đúng", "admin01 + mật khẩu đúng", "Vào Dashboard Admin"],
        ["TC-A02", "Đăng nhập sai", "Mật khẩu sai", "Hiển thị lỗi, không tạo session"],
        ["TC-A03", "Username trùng", "Username đã tồn tại", "Từ chối lưu"],
        ["TC-A04", "Username chứa số", "user01", "Báo username chỉ nhận chữ theo quy tắc hiện tại"],
        ["TC-A05", "Email có khoảng trắng", "a @fpt.com", "Báo lỗi email"],
        ["TC-A06", "Mật khẩu quá ngắn", "A1@", "Báo tối thiểu 6 ký tự"],
        ["TC-A07", "Mật khẩu thiếu hoa", "abc123@", "Báo thiếu chữ hoa"],
        ["TC-A08", "Mật khẩu hợp lệ", "Abc123@", "Cho phép lưu và băm mật khẩu"],
        ["TC-A09", "Admin tự hạ quyền", "Sửa vai trò bản thân thành member", "Từ chối thao tác"],
        ["TC-A10", "Mã reset hết hạn", "Mã cũ quá 15 phút", "Không cho đổi mật khẩu"],
    ]
)
add_para(
    "Khi kiểm thử mật khẩu, cần quan sát thứ tự cảnh báo. Hệ thống phải chỉ hiển thị lỗi đầu tiên chưa đạt; sau khi sửa lỗi đó "
    "mới hiển thị lỗi tiếp theo. Sau khi lưu, kiểm tra cột password_hash không chứa mật khẩu nguyên bản."
)

# Trang 35
page_break()
page_title("4.3. KIỂM THỬ BOARD, TASK VÀ PHÂN QUYỀN")
add_table(
    ["Mã", "Tình huống", "Kết quả mong đợi"],
    [
        ["TC-B01", "Manager tạo board hợp lệ", "Board và thành viên được lưu"],
        ["TC-B02", "Member gọi action tạo board", "Bị từ chối quyền"],
        ["TC-B03", "Manager giao task cho người ngoài board", "Từ chối lưu"],
        ["TC-B04", "Deadline nhỏ hơn ngày hiện tại", "Hiển thị lỗi deadline"],
        ["TC-B05", "Member cập nhật task được giao", "Cập nhật trạng thái và ghi chú"],
        ["TC-B06", "Member cập nhật task người khác", "Bị từ chối"],
        ["TC-B07", "Kéo thả task hợp lệ", "Task chuyển cột và trạng thái được lưu"],
        ["TC-B08", "Xóa board", "Board, board_members và tasks liên quan bị xóa"],
        ["TC-B09", "Xem board không tham gia", "Member không truy cập được"],
        ["TC-B10", "Task quá hạn chưa hoàn thành", "Hiển thị cảnh báo trễ hạn"],
    ]
)
add_para(
    "Kiểm thử quyền cần thực hiện bằng cả giao diện và URL trực tiếp. Ví dụ, sau khi đăng nhập Member, nhập đường dẫn action "
    "xóa task hoặc trang Phân quyền. Kết quả đúng là hệ thống từ chối ở phía server, không phụ thuộc nút có hiển thị hay không."
)

# Trang 36
page_break()
page_title("4.4. KIỂM THỬ LỌC, XUẤT FILE VÀ BÁO CÁO")
add_table(
    ["Mã", "Tình huống", "Kết quả mong đợi"],
    [
        ["TC-R01", "Lọc theo vai trò Admin", "Chỉ hiển thị tài khoản Admin"],
        ["TC-R02", "Kết hợp bộ phận và vai trò", "Kết quả thỏa đồng thời hai điều kiện"],
        ["TC-R03", "Không có kết quả", "Hiển thị thông báo không tìm thấy"],
        ["TC-R04", "Xuất Excel sau khi lọc", "File chỉ chứa kết quả đang lọc"],
        ["TC-R05", "Xuất Word/PDF", "File mở được và tiếng Việt đúng"],
        ["TC-R06", "Kiểm tra dữ liệu nhạy cảm", "File không chứa password_hash"],
        ["TC-R07", "Tỷ lệ hoàn thành", "done_count/task_count tính đúng"],
        ["TC-R08", "Báo cáo theo nhân sự", "Task, đã xong và trễ hạn đúng dữ liệu"],
    ]
)
add_para(
    "Sau khi xuất file, cần mở bằng ứng dụng tương ứng thay vì chỉ kiểm tra file đã tải. Đối chiếu một số dòng với bảng trên "
    "website, kiểm tra tiếng Việt, bộ lọc, tổng số bản ghi và xác nhận không có dữ liệu mật khẩu."
)
add_para(
    "Với báo cáo, nên tạo dữ liệu mẫu có đủ bốn trạng thái và một số task quá hạn. Điều này giúp kiểm thử các nhánh tính toán "
    "thay vì chỉ kiểm thử trường hợp dữ liệu trống."
)

# Trang 37
page_break()
page_title("4.5. KIỂM THỬ SAO LƯU, KHÔI PHỤC VÀ AN TOÀN")
add_table(
    ["Mã", "Tình huống", "Kết quả mong đợi"],
    [
        ["TC-D01", "Admin tải sao lưu", "Nhận file .sql có dữ liệu"],
        ["TC-D02", "Member mở data_management.php", "Bị từ chối quyền"],
        ["TC-D03", "Import thiếu CSRF", "Từ chối khôi phục"],
        ["TC-D04", "Nhập sai chuỗi xác nhận", "Từ chối khôi phục"],
        ["TC-D05", "Upload file .txt", "Từ chối phần mở rộng"],
        ["TC-D06", "Upload file rỗng", "Từ chối dung lượng"],
        ["TC-D07", "Upload SQL hợp lệ", "Dữ liệu được phục hồi"],
        ["TC-D08", "Admin không còn hợp lệ sau restore", "Đăng xuất và yêu cầu đăng nhập lại"],
        ["TC-D09", "Hiển thị chuỗi HTML trong dữ liệu", "Được escape, không chạy script"],
    ]
)
add_para(
    "Trước khi thử khôi phục, luôn tạo một bản sao lưu mới. Sau khôi phục, kiểm tra số lượng bảng, tài khoản, board, task và "
    "khả năng đăng nhập. Đây là kiểm thử có khả năng thay đổi dữ liệu lớn nên phải thực hiện trên môi trường demo."
)

# Trang 38
page_break()
page_title("CHƯƠNG 5. TRIỂN KHAI VÀ HƯỚNG DẪN SỬ DỤNG")
add_heading("5.1. Cài đặt bằng XAMPP", 2)
add_numbered([
    "Cài XAMPP và bật Apache, MySQL.",
    "Đặt thư mục fpt_task_manager trong htdocs.",
    "Mở phpMyAdmin, import database.sql.",
    "Kiểm tra thông tin DB_HOST, DB_NAME, DB_USER, DB_PASS trong config/database.php.",
    "Truy cập /fpt_task_manager/login.php và đăng nhập bằng tài khoản demo.",
])
add_heading("5.2. Quy trình vận hành đề xuất", 2)
add_bullets([
    "Admin sao lưu dữ liệu trước khi thay đổi lớn hoặc khôi phục.",
    "Admin tạo tài khoản đúng vai trò và kiểm tra email.",
    "Quản lý tạo board, thành viên và task theo kế hoạch.",
    "Nhân viên cập nhật trạng thái, ghi chú hằng ngày.",
    "Quản lý theo dõi Dashboard, lịch và báo cáo định kỳ.",
    "Admin xuất dữ liệu tài khoản và sao lưu theo lịch.",
])
add_para(
    "Trong môi trường thực tế, nên đặt lịch sao lưu tự động, giới hạn quyền truy cập máy chủ, đổi tài khoản demo và mật khẩu "
    "mặc định, sử dụng HTTPS, đồng thời kiểm tra bản sao lưu có thể khôi phục được."
)

# Trang 39
page_break()
page_title("5.3. KỊCH BẢN THUYẾT TRÌNH VÀ DEMO")
add_table(
    ["Bước", "Vai trò", "Nội dung trình diễn", "Điểm cần nhấn mạnh"],
    [
        ["1", "Admin", "Đăng nhập, mở Phân quyền", "Menu và quyền thay đổi theo vai trò"],
        ["2", "Admin", "Nhập mật khẩu sai rồi sửa dần", "Cảnh báo tuần tự JavaScript + PHP"],
        ["3", "Admin", "Lọc tài khoản và xuất PDF", "File giữ đúng điều kiện lọc"],
        ["4", "Admin", "Tải bản sao lưu SQL", "Khả năng phục hồi dữ liệu"],
        ["5", "Manager", "Tạo board và task", "Giao việc, deadline, ưu tiên"],
        ["6", "Manager", "Kéo task qua các cột", "Cập nhật trạng thái trực quan"],
        ["7", "Member", "Cập nhật task được giao", "Giới hạn quyền chỉnh sửa"],
        ["8", "Manager", "Mở báo cáo", "Số liệu tổng hợp tự động"],
    ]
)
add_para(
    "Kịch bản trên tạo một câu chuyện hoàn chỉnh thay vì trình diễn từng màn hình rời rạc. Người xem thấy rõ dữ liệu đi từ "
    "khâu quản trị tài khoản, lập kế hoạch, thực hiện đến báo cáo và sao lưu. Nên chuẩn bị trước một task có deadline gần và "
    "một task trễ hạn để Dashboard thể hiện rõ hơn."
)
add_heading("Câu hỏi thường gặp khi bảo vệ", 2)
add_bullets([
    "Tại sao phải kiểm tra quyền ở action khi đã ẩn menu?",
    "Vì sao password_hash an toàn hơn lưu mật khẩu nguyên bản?",
    "Vì sao file xuất không chứa password_hash?",
    "Sự khác nhau giữa Admin, Quản lý và Nhân viên là gì?",
    "Khôi phục dữ liệu được bảo vệ bằng những lớp kiểm tra nào?",
])

# Trang 40
page_break()
page_title("CHƯƠNG 6. ĐÁNH GIÁ, HƯỚNG PHÁT TRIỂN VÀ KẾT LUẬN")
add_heading("6.1. Kết quả đạt được", 2)
add_bullets([
    "Hoàn thiện luồng quản lý tiến độ từ tài khoản, board, task đến báo cáo.",
    "Phân quyền ba vai trò và kiểm tra quyền ở phía máy chủ.",
    "Bổ sung bộ lọc, xuất Excel/Word/PDF, quên mật khẩu và sao lưu/khôi phục.",
    "Kiểm tra mật khẩu tuần tự, đồng nhất giữa nhiều màn hình.",
    "Hỗ trợ tiếng Việt UTF-8 và triển khai thuận tiện trên XAMPP.",
])
add_heading("6.2. Hạn chế", 2)
add_bullets([
    "Chưa có gửi email thật cho mã đặt lại mật khẩu.",
    "Google Calendar mới là liên kết tạo sự kiện một chiều.",
    "Chưa có nhật ký thao tác, bình luận task và file đính kèm.",
    "CSRF chưa được triển khai cho toàn bộ form thay đổi dữ liệu.",
    "Xóa dữ liệu hiện vẫn là xóa vật lý, cần cân nhắc soft delete.",
])
add_heading("6.3. Hướng phát triển", 2)
add_bullets([
    "Thêm SMTP, thông báo email và nhắc deadline tự động.",
    "Thêm tìm kiếm, phân trang và chỉ mục dữ liệu cho quy mô lớn.",
    "Thêm biểu đồ trực quan, lịch dạng tháng và xuất báo cáo theo thời gian.",
    "Bổ sung audit log, khóa tài khoản, xác thực hai lớp và CSRF toàn hệ thống.",
    "Đóng gói Docker và triển khai trên máy chủ HTTPS.",
])
add_heading("Kết luận", 2)
add_para(
    "FPT Task Manager đã đáp ứng mục tiêu xây dựng một website quản lý tiến độ công việc có luồng nghiệp vụ rõ, giao diện trực "
    "quan và dữ liệu tập trung. Giá trị của đề tài không chỉ nằm ở số lượng màn hình mà còn ở cách kết nối phân quyền, kiểm tra "
    "dữ liệu, báo cáo và phương án phục hồi thành một hệ thống thống nhất. Qua quá trình thực hiện, nhóm củng cố kỹ năng phân "
    "tích yêu cầu, thiết kế cơ sở dữ liệu, lập trình PHP/MySQL, kiểm thử và trình bày sản phẩm."
)

# Tài liệu tham khảo đặt tiếp sau phần kết luận để tổng độ dài giữ trong khoảng 35-40 trang.
page_title("TÀI LIỆU THAM KHẢO")
refs = [
    "PHP Documentation. Password Hashing Functions, Sessions và PDO. https://www.php.net/docs.php",
    "MySQL 8.0 Reference Manual. InnoDB, Foreign Keys, mysqldump và mysql client. https://dev.mysql.com/doc/",
    "Bootstrap Documentation. Layout, Forms, Modal và Responsive Utilities. https://getbootstrap.com/docs/",
    "MDN Web Docs. HTML, CSS, JavaScript và Drag and Drop API. https://developer.mozilla.org/",
    "OWASP Foundation. Authentication, Password Storage, CSRF và Input Validation Cheat Sheets. https://cheatsheetseries.owasp.org/",
    "Ahmad, M. O., Markkula, J., Oivo, M. Kanban in Software Development: A Systematic Literature Review, 2018.",
    "Kerzner, H. Project Management: A Systems Approach to Planning, Scheduling, and Controlling, 12th Edition, 2017.",
    "Trường Đại học Hàng Hải Việt Nam. Giáo trình quản lý dự án công nghệ thông tin, 2025.",
]
add_numbered(refs)
add_heading("Phụ lục: Tài khoản dữ liệu mẫu", 2)
add_table(
    ["Vai trò", "Username", "Mục đích demo"],
    [
        ["Admin", "admin01", "Phân quyền, xuất tài khoản, sao lưu/khôi phục"],
        ["Quản lý", "manager01", "Board, task, báo cáo"],
        ["Nhân viên", "nhanvien01", "Cập nhật task được giao"],
        ["Nhân viên", "nhanvien02", "Kiểm thử quyền theo board"],
        ["Nhân viên", "nhanvien03", "Kiểm thử deadline và trạng thái"],
    ]
)
add_para(
    "Lưu ý an toàn: tài khoản và mật khẩu demo chỉ dùng trong môi trường học tập. Khi triển khai, phải đổi mật khẩu, giới hạn "
    "quyền truy cập và không công bố thông tin đăng nhập trong tài liệu phát hành rộng rãi."
)

# Header and core properties
for sec in doc.sections:
    header = sec.header.paragraphs[0]
    header.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = header.add_run("BÁO CÁO WEBSITE QUẢN LÝ TIẾN ĐỘ CÔNG VIỆC - FPT TASK MANAGER")
    run.font.name = "Times New Roman"
    run.font.size = Pt(9)
    run.font.color.rgb = RGBColor.from_string("667085")

doc.core_properties.title = "Báo cáo Website Quản lý tiến độ công việc FPT Task Manager"
doc.core_properties.subject = "Báo cáo bài tập lớn Web"
doc.core_properties.author = "Nhóm 1"
doc.core_properties.keywords = "PHP, MySQL, quản lý công việc, FPT Task Manager"
doc.core_properties.comments = "Tài liệu được tổng hợp từ mã nguồn phiên bản hiện tại."

doc.save(OUTPUT)
print(OUTPUT)
