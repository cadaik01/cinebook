# 🔒 PESSIMISTIC LOCKING - HƯỚNG DẪN CHI TIẾT

## 📚 LÝ THUYẾT

### Pessimistic Locking là gì?

**Định nghĩa:** Kỹ thuật khóa dữ liệu TRƯỚC KHI xử lý để đảm bảo chỉ có 1 transaction được truy cập tại 1 thời điểm.

```
Không có Lock (❌ RACE CONDITION):
-----------------------------------
Time | User A                  | User B
-----|-------------------------|-------------------------
10:00| SELECT seat A1          |
10:01|                         | SELECT seat A1
10:02| Check: Available ✅     |
10:03|                         | Check: Available ✅
10:04| INSERT booking A1       |
10:05|                         | INSERT booking A1 ❌
     | → CONFLICT! Cả 2 đều book được

Có Lock (✅ AN TOÀN):
-------------------
Time | User A                  | User B
-----|-------------------------|-------------------------
10:00| SELECT ... FOR UPDATE   |
     | → LOCK seat A1 🔒       |
10:01|                         | SELECT ... FOR UPDATE
     |                         | → WAITING... ⏳
10:02| Check: Available ✅     |
10:03| INSERT booking A1       |
10:04| COMMIT                  |
     | → UNLOCK 🔓             |
10:05|                         | → Lock acquired 🔒
10:06|                         | Check: Already booked ❌
10:07|                         | ROLLBACK
     | → User B nhận thông báo "Ghế đã được đặt"
```

---

## 🔧 CÁCH HOẠT ĐỘNG TRONG CODE

### 1️⃣ **Bắt đầu Transaction**

```php
DB::beginTransaction();
```

**Giải thích:**
- Tạo một "phiên giao dịch" riêng biệt
- Tất cả thay đổi database chưa được lưu thật
- Có thể rollback (hủy) hoặc commit (lưu)

---

### 2️⃣ **Lock Rows với `lockForUpdate()`**

```php
$lockedSeats = Seat::whereIn('id', $selectedSeats)
    ->lockForUpdate()
    ->get();
```

**SQL thực tế được chạy:**
```sql
SELECT * FROM seats 
WHERE id IN (1, 2, 3) 
FOR UPDATE;
```

**Điều gì xảy ra:**
1. ✅ User A query ghế → Database LOCK các row (1, 2, 3)
2. ⏸️ User B query CÙNG ghế → Phải **ĐỢI** User A commit/rollback
3. 🔓 User A commit → Lock được giải phóng
4. ▶️ User B tiếp tục xử lý (nhưng data đã thay đổi)

**Lock Level:**
- 🔒 **Row-level lock:** Chỉ khóa các row được select
- ✅ Các row khác vẫn hoạt động bình thường
- ⚡ Performance tốt hơn table-level lock

---

### 3️⃣ **Validate sau khi Lock**

```php
$bookedSeatIds = DB::table('showtime_seats')
    ->where('showtime_id', $showtime_id)
    ->whereIn('seat_id', $selectedSeats)
    ->whereIn('status', ['booked', 'reserved'])
    ->lockForUpdate() // Lock cả bảng showtime_seats
    ->pluck('seat_id')
    ->toArray();

if (!empty($bookedSeatIds)) {
    DB::rollBack(); // Hủy transaction
    return error('Ghế đã được đặt');
}
```

**Tại sao lock cả `showtime_seats`?**
- Đảm bảo không ai INSERT booking mới trong lúc check
- Tránh "phantom read" (dữ liệu xuất hiện giữa 2 lần query)

---

### 4️⃣ **Xử lý Business Logic**

```php
// Tính tiền, validate couple seats, etc.
// Data đã SAFE vì đang bị lock
foreach ($selectedSeats as $seat_id) {
    // ...
}
```

**Lúc này:**
- ✅ Không ai có thể đọc/ghi các row đang lock
- ✅ An toàn 100% khi xử lý

---

### 5️⃣ **Commit hoặc Rollback**

```php
try {
    // ... logic xử lý
    DB::commit(); // Lưu thay đổi + Giải phóng lock
} catch (\Exception $e) {
    DB::rollBack(); // Hủy tất cả + Giải phóng lock
}
```

**Khi commit:**
- 💾 Tất cả INSERT/UPDATE được lưu vào database
- 🔓 Locks được release
- ▶️ User khác có thể tiếp tục

**Khi rollback:**
- 🗑️ Tất cả thay đổi bị hủy
- 🔓 Locks được release
- ↩️ Database về trạng thái ban đầu

---

## 🎯 SO SÁNH VỚI CÁC PHƯƠNG PHÁP KHÁC

| Method | Lock Time | Race Safe | Complexity | Use Case |
|--------|-----------|-----------|------------|----------|
| **No Lock** | ❌ None | ❌ No | ⭐ Easy | Demo only |
| **Optimistic Lock** | ❌ None | 🟡 Retry | ⭐⭐ Medium | Low conflict |
| **Pessimistic Lock** | 🔒 Yes | ✅ Yes | ⭐⭐⭐ Advanced | High conflict |
| **Queue** | ❌ None | ✅ Yes | ⭐⭐⭐⭐ Hard | Very high load |

---

## 🧪 TEST CASES

### Test 1: Concurrent Booking (Cùng 1 ghế)

**Setup:**
1. Mở 2 browser/tab khác nhau
2. Login 2 user khác nhau
3. Vào cùng 1 showtime, chọn cùng ghế A1

**Expected:**
- ✅ User A submit trước → Thành công
- ✅ User B submit sau → Báo lỗi "Seat A1 is no longer available"

**Command test nhanh:**
```bash
# Terminal 1 (User A)
curl -X POST http://localhost/booking/book/1 -d "seats=[1,2,3]"

# Terminal 2 (User B) - chạy ngay sau
curl -X POST http://localhost/booking/book/1 -d "seats=[1,2,3]"
```

---

### Test 2: Deadlock Prevention

**Scenario:**
- User A: Lock seats [1, 2, 3]
- User B: Lock seats [3, 2, 1] (ngược thứ tự)

**Expected:**
- ✅ MySQL tự động detect deadlock
- ✅ Rollback 1 trong 2 transaction
- ✅ Transaction còn lại thành công

**MySQL Error Message:**
```
SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock
```

**Code đã handle:**
```php
} catch (\Exception $e) {
    DB::rollBack();
    return error('Booking failed: ' . $e->getMessage());
}
```

---

## ⚠️ NHƯỢC ĐIỂM & LƯU Ý

### 1. **Performance Impact**

**Lock = Chờ đợi:**
```
High Traffic:
- 100 users cùng book 1 phòng
- Chỉ 1 user xử lý tại 1 thời điểm
- 99 users khác phải chờ
```

**Giảm thiểu:**
- ✅ Lock chỉ rows cần thiết (không lock toàn bảng)
- ✅ Giữ transaction ngắn (< 1 giây)
- ✅ Sử dụng index trên seat_id

---

### 2. **Timeout Risk**

**MySQL timeout mặc định:**
```ini
innodb_lock_wait_timeout = 50 (seconds)
```

**Nếu vượt quá:**
```
ERROR 1205 (HY000): Lock wait timeout exceeded
```

**Solution:**
```php
DB::statement('SET SESSION innodb_lock_wait_timeout = 10');
```

---

### 3. **Connection Pool**

**Vấn đề:**
- Transaction giữ 1 connection
- Connection pool có giới hạn (default: 151)
- 151 users đang book → User 152 bị reject

**Config:**
```env
DB_POOL_MAX=500
```

---

## 📊 MONITORING

### Query để xem Lock hiện tại:

```sql
-- MySQL 8.0
SELECT 
    trx_id, 
    trx_state, 
    trx_started,
    trx_wait_started,
    trx_mysql_thread_id
FROM information_schema.innodb_trx;
```

### Xem Deadlock logs:

```sql
SHOW ENGINE INNODB STATUS\G
```

---

## 🎓 KẾT LUẬN

### Khi nào dùng Pessimistic Locking?

✅ **NÊN DÙNG khi:**
- Conflict cao (nhiều user cùng book 1 ghế)
- Data critical (booking, payment, inventory)
- Acceptable latency (có thể chờ vài giây)

❌ **KHÔNG NÊN DÙNG khi:**
- Read-heavy (95% read, 5% write)
- Low conflict (ít khi trùng)
- High performance requirement (< 100ms)

---

## 📚 TÀI LIỆU THAM KHẢO

1. [MySQL Locking Reads](https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html)
2. [Laravel Database Transactions](https://laravel.com/docs/11.x/database#database-transactions)
3. [Pessimistic vs Optimistic Locking](https://stackoverflow.com/questions/129329)

---

**Tác giả:** Your Name  
**Ngày tạo:** 22/01/2026  
**Version:** 1.0
