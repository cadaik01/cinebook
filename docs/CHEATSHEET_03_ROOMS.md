# CHEATSHEET: QUẢN LÝ PHÒNG CHIẾU (ROOMS)
## Thuật toán tạo sơ đồ ghế tự động

---

## 🎯 MỤC ĐÍCH

Quản lý phòng chiếu là phần **phức tạp nhất** của admin panel:
- Tạo phòng với thông tin cơ bản
- **Tự động sinh sơ đồ ghế** dựa trên cấu hình
- Hỗ trợ 3 loại ghế với giá khác nhau
- Đảm bảo data integrity với Transaction

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminRoomController.php
Models:     app/Models/Room.php
            app/Models/Seat.php
Views:      resources/views/admin/rooms/
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php (xem sơ đồ ghế)
```

---

## 🗄️ DATABASE SCHEMA

### Table: rooms

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR(100) | Tên phòng (Room 1, VIP Room) |
| capacity | INT | Sức chứa (tính tự động) |
| screen_type | ENUM | 2D, 3D, IMAX, 4DX |
| rows | INT | Số hàng ghế |
| seats_per_row | INT | Số ghế mỗi hàng |
| status | ENUM | active, maintenance, inactive |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### Table: seats

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| room_id | BIGINT | FK to rooms |
| row_label | CHAR(1) | A, B, C, D... |
| seat_number | INT | 1, 2, 3... |
| seat_code | VARCHAR(10) | A1, A2, B1, B2... |
| type | ENUM | standard, vip, couple |
| status | ENUM | available, maintenance |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## 🔗 QUAN HỆ (RELATIONSHIPS)

```php
// Room.php
class Room extends Model
{
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }

    // Accessor: Tính capacity từ số ghế
    public function getCapacityAttribute()
    {
        return $this->seats()->count();
    }
}

// Seat.php
class Seat extends Model
{
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
```

---

## 💻 THUẬT TOÁN TẠO SƠ ĐỒ GHẾ

### Ý tưởng

```
Input:
- rows = 10 (số hàng)
- seats_per_row = 12 (số ghế/hàng)
- vip_rows = [4, 5, 6] (hàng D, E, F là VIP)
- couple_row = 10 (hàng J là Couple)

Output:
- 120 ghế với type phù hợp
- Row A-C: Standard
- Row D-F: VIP
- Row G-I: Standard
- Row J: Couple (6 cặp = 12 vị trí)
```

### Code Implementation

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|max:100',
        'screen_type' => 'required|in:2D,3D,IMAX,4DX',
        'rows' => 'required|integer|min:1|max:26',
        'seats_per_row' => 'required|integer|min:1|max:30',
        'vip_rows' => 'nullable|array',
        'couple_row' => 'nullable|integer',
    ]);

    // Sử dụng Transaction để đảm bảo tất cả hoặc không gì cả
    DB::transaction(function () use ($validated, $request) {

        // 1. Tạo Room
        $room = Room::create([
            'name' => $validated['name'],
            'screen_type' => $validated['screen_type'],
            'rows' => $validated['rows'],
            'seats_per_row' => $validated['seats_per_row'],
            'status' => 'active',
        ]);

        // 2. Tạo Seats
        $vipRows = $request->input('vip_rows', []);
        $coupleRow = $request->input('couple_row');

        for ($row = 1; $row <= $validated['rows']; $row++) {
            $rowLabel = chr(64 + $row); // 1→A, 2→B, 3→C...

            // Xác định loại ghế cho hàng này
            if ($row == $coupleRow) {
                $type = 'couple';
                $seatsInRow = $validated['seats_per_row'] / 2; // Couple = 2 vị trí
            } elseif (in_array($row, $vipRows)) {
                $type = 'vip';
                $seatsInRow = $validated['seats_per_row'];
            } else {
                $type = 'standard';
                $seatsInRow = $validated['seats_per_row'];
            }

            // Tạo ghế cho hàng
            for ($seat = 1; $seat <= $seatsInRow; $seat++) {
                Seat::create([
                    'room_id' => $room->id,
                    'row_label' => $rowLabel,
                    'seat_number' => $seat,
                    'seat_code' => $rowLabel . $seat,
                    'type' => $type,
                    'status' => 'available',
                ]);
            }
        }

        // 3. Update capacity
        $room->update(['capacity' => $room->seats()->count()]);
    });

    return redirect()->route('admin.rooms.index')
        ->with('success', 'Phòng chiếu đã được tạo!');
}
```

### Giải thích chr(64 + $row)

```
chr() = Character from ASCII code
64 = ASCII code of '@'
64 + 1 = 65 = 'A'
64 + 2 = 66 = 'B'
64 + 3 = 67 = 'C'
...
64 + 26 = 90 = 'Z'

→ row 1 → 'A', row 2 → 'B', row 10 → 'J'
```

---

## 🎨 3 LOẠI GHẾ

### Standard (Ghế thường)

```
┌─────┐
│  A1 │  ← Ghế đơn, giá cơ bản
└─────┘
- Màu: Xanh dương
- Giá: Base price (100%)
- Vị trí: Thường ở các hàng đầu và cuối
```

### VIP (Ghế VIP)

```
┌───────┐
│  VIP  │  ← Ghế rộng hơn, êm hơn
│  D5   │
└───────┘
- Màu: Vàng/Gold
- Giá: Base price + 50%
- Vị trí: Các hàng giữa (best view)
```

### Couple (Ghế đôi)

```
┌─────────────┐
│   J1   J2   │  ← 2 ghế không có tay vịn giữa
│   COUPLE    │
└─────────────┘
- Màu: Hồng/Pink
- Giá: Base price + 100% (tính cho cả cặp)
- Vị trí: Hàng cuối cùng
- Booking: Phải đặt cả cặp, không tách
```

---

## 📐 SƠ ĐỒ GHẾ VISUALIZATION

### View trong Admin

```
                    [====== MÀN HÌNH ======]

         1    2    3    4    5    6    7    8    9   10
    A   [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]   Standard
    B   [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]   Standard
    C   [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]   Standard
    D   [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]   VIP
    E   [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]   VIP
    F   [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]  [◆]   VIP
    G   [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]   Standard
    H   [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]   Standard
    I   [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]  [□]   Standard
    J   [♥    ♥]  [♥    ♥]  [♥    ♥]  [♥    ♥]  [♥    ♥]   Couple

    Chú thích: □ Standard  ◆ VIP  ♥ Couple
```

### CSS Grid Implementation

```css
.seat-map {
    display: grid;
    grid-template-columns: repeat(var(--seats-per-row), 40px);
    gap: 5px;
}

.seat {
    width: 35px;
    height: 35px;
    border-radius: 5px;
    cursor: pointer;
}

.seat.standard { background: #3B82F6; }
.seat.vip { background: #F59E0B; }
.seat.couple {
    width: 75px; /* 2 ghế */
    background: #EC4899;
}
```

---

## 🔒 DATABASE TRANSACTION

### Tại sao cần Transaction?

```
Scenario KHÔNG có Transaction:
1. Tạo Room → Thành công (Room ID = 5)
2. Tạo Seat 1-50 → Thành công
3. Tạo Seat 51 → LỖI (server crash, out of memory, etc.)
4. Kết quả: Room 5 có 50 ghế, thiếu 50 ghế còn lại
   → DATA KHÔNG NHẤT QUÁN!

Scenario CÓ Transaction:
1. BEGIN TRANSACTION
2. Tạo Room → OK
3. Tạo Seat 1-50 → OK
4. Tạo Seat 51 → LỖI
5. ROLLBACK → Tất cả quay về như cũ
6. Kết quả: Không có Room 5, không có ghế nào
   → DATA NHẤT QUÁN!
```

### Code với Transaction

```php
DB::transaction(function () use ($data) {
    // Mọi thứ trong đây được bao bọc bởi transaction
    $room = Room::create([...]);

    foreach ($seats as $seat) {
        Seat::create([...]);
    }

    // Nếu có lỗi ở bất kỳ đâu → ROLLBACK tất cả
});
```

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: "Tại sao dùng Transaction?"

```
"Transaction đảm bảo tính ATOMIC - tất cả hoặc không gì cả.
Khi tạo phòng, cần tạo cả room và 100+ seats.
Nếu tạo được room mà seats bị lỗi giữa chừng,
sẽ có room không hoàn chỉnh trong database.
Transaction đảm bảo nếu có lỗi, rollback toàn bộ."
```

### Q: "Giải thích thuật toán tạo ghế?"

```
"Thuật toán dựa trên nested loop:
- Vòng ngoài: Duyệt qua từng hàng (1 → rows)
- Vòng trong: Tạo ghế cho hàng đó (1 → seats_per_row)

Với mỗi hàng, xác định loại ghế:
- Nếu là hàng couple → type = 'couple', số ghế = seats_per_row/2
- Nếu trong danh sách VIP → type = 'vip'
- Còn lại → type = 'standard'

Row label dùng chr(64 + row) để convert số → chữ cái."
```

### Q: "Xóa phòng thì ghế có bị xóa không?"

```
"Có, nhờ cascade delete trong migration.
Khi Room bị xóa, tất cả Seats có room_id đó cũng bị xóa.

Tuy nhiên, không cho xóa room đang có suất chiếu
để tránh ảnh hưởng đến booking đã tồn tại."
```

### Q: "Couple seat hoạt động thế nào?"

```
"Couple seat là 2 ghế liền nhau, đặt cùng lúc.
Trong database: 1 record với type = 'couple'
Trong UI: Hiển thị rộng gấp đôi

Khi user đặt couple seat:
- Chỉ cần chọn 1 lần
- Tự động book cả cặp
- Giá = base × 2 × 1.0 (không surcharge thêm)
  hoặc = base × 2 × 1.5 (nếu có surcharge)"
```

### Q: "Có thể thay đổi sơ đồ ghế sau khi tạo?"

```
"Về nguyên tắc: Không nên thay đổi nếu đã có suất chiếu.
Vì booking đã reference đến seat cũ.

Nếu cần thay đổi:
1. Hủy tất cả suất chiếu tương lai
2. Xóa room cũ
3. Tạo room mới với layout mới

Hoặc implement soft delete và version control cho seats."
```

---

## 🎯 DEMO TIPS

### Chuẩn bị

```
✅ 3-4 phòng với layout khác nhau:
   - Room 1: 10 hàng × 12 ghế, có VIP row 4-6
   - Room 2: 8 hàng × 10 ghế, có Couple row 8
   - VIP Room: 5 hàng × 8 ghế, toàn VIP
   - Small Room: 6 hàng × 8 ghế, Standard only
```

### Khi demo TẠO PHÒNG

```
1. "Tôi sẽ tạo một phòng chiếu mới với sơ đồ ghế"

2. Điền form:
   - Tên: "Room Demo"
   - Loại màn hình: "2D"
   - Số hàng: 8
   - Số ghế/hàng: 10

3. Chọn VIP rows: "Hàng D, E, F sẽ là VIP - vị trí tốt nhất"

4. Chọn Couple row: "Hàng H - hàng cuối - sẽ là ghế đôi"

5. Submit và QUAN TRỌNG: Show sơ đồ ghế được tạo
   - "Như các bạn thấy, hệ thống tự động tạo 80 ghế"
   - "Hàng A-C: Standard, D-F: VIP, G: Standard, H: Couple"

6. Giải thích Transaction:
   - "Tất cả được tạo trong 1 transaction"
   - "Nếu có lỗi, không có gì được lưu"
```

### Câu hay để nói

```
"Đây là phần phức tạp nhất của hệ thống.
Thuật toán tự động tạo sơ đồ ghế dựa trên config,
admin không cần nhập từng ghế một.
Transaction đảm bảo data integrity."
```

---

## 📊 ROOM TEMPLATES (Gợi ý)

| Template | Rows | Seats/Row | VIP Rows | Couple | Capacity |
|----------|------|-----------|----------|--------|----------|
| Small | 6 | 8 | - | - | 48 |
| Standard | 10 | 12 | 4-6 | 10 | ~114 |
| Large | 15 | 20 | 7-10 | 15 | ~290 |
| VIP Only | 5 | 8 | All | - | 40 |
| Couple Focus | 8 | 10 | - | 6-8 | ~65 |

---

## 📝 GHI NHỚ NHANH

```
✓ Tạo Room + Seats trong DB Transaction
✓ Thuật toán: 2 vòng loop (rows → seats)
✓ chr(64 + n) để convert số → chữ cái
✓ 3 loại ghế: standard, vip (+50%), couple (+100%)
✓ Couple = 1 record trong DB, hiển thị 2 vị trí
✓ Không xóa room có suất chiếu
✓ Cascade delete: xóa room → xóa seats
```

