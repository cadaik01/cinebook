# CHEATSHEET: USERS, REVIEWS & QR CHECK-IN
## Quản lý người dùng + Đánh giá + Check-in

---

# PHẦN A: QUẢN LÝ USERS

## 🎯 MỤC ĐÍCH

- Xem danh sách người dùng
- Phân quyền (user/admin)
- Vô hiệu hóa tài khoản
- Xem lịch sử booking của user

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminUserController.php
Model:      app/Models/User.php
Views:      resources/views/admin/users/
            ├── index.blade.php
            ├── show.blade.php
            └── edit.blade.php
```

---

## 🗄️ DATABASE: users

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR(255) | Tên người dùng |
| email | VARCHAR(255) | Email (unique) |
| password | VARCHAR(255) | Hashed password |
| phone | VARCHAR(20) | Số điện thoại |
| role | ENUM | user, admin |
| status | ENUM | active, inactive, banned |
| email_verified_at | TIMESTAMP | Đã verify email |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## 💻 CODE QUAN TRỌNG

### Index với Filter

```php
public function index(Request $request)
{
    $query = User::withCount('bookings');

    // Search
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('email', 'like', '%' . $request->search . '%');
        });
    }

    // Filter by role
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $users = $query->latest()->paginate(20);

    return view('admin.users.index', compact('users'));
}
```

### Toggle Role (Đổi quyền)

```php
public function toggleRole(User $user)
{
    // Không cho tự đổi role của chính mình
    if ($user->id === auth()->id()) {
        return back()->with('error', 'Không thể đổi role của chính bạn!');
    }

    $user->update([
        'role' => $user->role === 'admin' ? 'user' : 'admin'
    ]);

    return back()->with('success', 'Đã cập nhật role!');
}
```

### Ban User

```php
public function ban(User $user)
{
    if ($user->id === auth()->id()) {
        return back()->with('error', 'Không thể ban chính bạn!');
    }

    if ($user->role === 'admin') {
        return back()->with('error', 'Không thể ban admin khác!');
    }

    $user->update(['status' => 'banned']);

    return back()->with('success', 'Đã ban user!');
}
```

---

## 🔐 BẢO VỆ ADMIN

### Nguyên tắc

```
1. Admin KHÔNG thể tự đổi role của mình
2. Admin KHÔNG thể ban chính mình
3. Admin KHÔNG thể ban admin khác
4. Admin cuối cùng KHÔNG thể bị demote

→ Đảm bảo luôn có ít nhất 1 admin trong hệ thống
```

### Code Protection

```php
// Kiểm tra có phải admin cuối cùng không
public function canDemote(User $user)
{
    if ($user->role !== 'admin') return true;

    $adminCount = User::where('role', 'admin')->count();
    return $adminCount > 1;
}
```

---

## 🎨 UI - User Index

```
┌─────────────────────────────────────────────────────────────┐
│  QUẢN LÝ NGƯỜI DÙNG                                        │
├─────────────────────────────────────────────────────────────┤
│  Search: [___________]  Role: [All ▼]  Status: [All ▼]     │
├─────────────────────────────────────────────────────────────┤
│  # │ Tên        │ Email           │ Role  │ Bookings │ Sts │
│────┼────────────┼─────────────────┼───────┼──────────┼─────│
│  1 │ Admin      │ admin@test.com  │ 👑    │ 5        │ ✓   │
│  2 │ Nguyễn A   │ a@email.com     │ 👤    │ 12       │ ✓   │
│  3 │ Trần B     │ b@email.com     │ 👤    │ 3        │ 🚫  │
└─────────────────────────────────────────────────────────────┘
```

---

# PHẦN B: QUẢN LÝ REVIEWS

## 🎯 MỤC ĐÍCH

- **Xem và lọc** đánh giá từ users
- **Sắp xếp** theo Latest hoặc Highest Rating
- **Xóa** reviews vi phạm (Admin moderation)
- Xem thống kê rating theo phim

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminReviewController.php
            app/Http/Controllers/ReviewController.php (user)
Model:      app/Models/Review.php
Views:      resources/views/admin/reviews/
            └── index.blade.php
            resources/views/movie_details.blade.php (user reviews)
Routes:
            GET  /admin/reviews           → AdminReviewController@index
            DELETE /admin/reviews/{id}    → AdminReviewController@destroy
```

---

## 🗄️ DATABASE: reviews

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| user_id | BIGINT | FK to users |
| movie_id | BIGINT | FK to movies |
| rating | TINYINT | 1-5 sao |
| comment | TEXT | Nội dung đánh giá (max 1000 chars) |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

---

## 💻 CODE QUAN TRỌNG

### Admin Index với Filter & Sort

```php
public function index(Request $request)
{
    $query = Review::with(['user', 'movie']);

    // Filter by movie
    if ($request->filled('movie_id')) {
        $query->where('movie_id', $request->movie_id);
    }

    // Filter by rating
    if ($request->filled('rating')) {
        $query->where('rating', $request->rating);
    }

    // Sort: latest (default) or highest_rated
    $sort = $request->get('sort', 'latest');
    if ($sort === 'highest_rated') {
        $query->highestRated(); // Model scope
    } else {
        $query->latest();
    }

    $reviews = $query->paginate(20);
    $movies = Movie::orderBy('title')->get();

    return view('admin.reviews.index', compact('reviews', 'movies'));
}
```

### Admin Delete Review (Moderation)

```php
public function destroy($id)
{
    $review = Review::findOrFail($id);
    $movieId = $review->movie_id;

    $review->delete();

    // Update movie average rating
    $movie = Movie::find($movieId);
    $movie->updateAverageRating();

    return redirect()->back()->with('success', 'Review deleted successfully.');
}
```

### Review Model Scopes (for Sorting)

```php
// app/Models/Review.php
public function scopeLatest($query)
{
    return $query->orderBy('created_at', 'desc');
}

public function scopeHighestRated($query)
{
    return $query->orderBy('rating', 'desc')
                 ->orderBy('created_at', 'desc');
}
```

### User-Facing Review Sorting (Movie Details)

```php
// MovieController.php
public function show(Request $request, $id)
{
    $movie = Movie::with('genres','reviews.user')->findOrFail($id);

    // Get review sort parameter (default: latest)
    $reviewSort = $request->input('review_sort', 'latest');

    return view('movie_details', compact('movie', 'reviewSort'));
}

// In Blade template:
@php
    $sortedReviews = ($reviewSort ?? 'latest') == 'highest'
        ? $movie->reviews->sortBy([['rating', 'desc'], ['created_at', 'desc']])
        : $movie->reviews->sortByDesc('created_at');
@endphp
```

---

## 🎨 UI - Admin Reviews Index

```
┌─────────────────────────────────────────────────────────────┐
│  QUẢN LÝ ĐÁNH GIÁ                                          │
├─────────────────────────────────────────────────────────────┤
│  Statistics: Total [150] │ Avg [4.2/5] │ 5★ [45] │ 1-2★ [8] │
├─────────────────────────────────────────────────────────────┤
│  Filter: Movie [All ▼]  Rating [All ▼]  Sort [Latest ▼]    │
├─────────────────────────────────────────────────────────────┤
│  ID │ User       │ Movie      │ Rating │ Comment    │ Action│
│─────┼────────────┼────────────┼────────┼────────────┼───────│
│  1  │ Nguyễn A   │ Aquaman 2  │ ⭐⭐⭐⭐⭐ │ "Hay..."   │ 🗑️    │
│  2  │ Trần B     │ Wonka      │ ⭐⭐⭐   │ "OK..."    │ 🗑️    │
│  3  │ Lê C       │ Aquaman 2  │ ⭐      │ "Tệ..."    │ 🗑️    │
└─────────────────────────────────────────────────────────────┘
```

## 🎨 UI - User Review Sorting (Movie Details)

```
┌─────────────────────────────────────────────────────────────┐
│  All Reviews                          Sort by: [Latest ▼]  │
│                                              [Highest Rating]│
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────────────────────────────────────────┐   │
│  │ Nguyễn A    ⭐⭐⭐⭐⭐ (5/5)                  2 hours ago │
│  │ "Phim hay quá, diễn viên đóng tuyệt vời!"            │
│  │                                           [Delete]   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

# PHẦN C: QR CHECK-IN

## 🎯 MỤC ĐÍCH

- Scan QR code từ vé điện tử
- Validate QR và check-in khách
- Hiển thị thông tin vé
- Ngăn check-in trùng lặp

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/QRCheckInController.php
Model:      app/Models/BookingSeat.php (lưu QR code)
Views:      resources/views/admin/qr_checkin/
            └── index.blade.php (giao diện scan)
```

---

## 🔄 QR CHECK-IN FLOW

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  Scan QR    │ ──▶ │  Validate   │ ──▶ │  Check-in   │
│  Code       │     │  QR Code    │     │  Success    │
└─────────────┘     └──────┬──────┘     └─────────────┘
                           │
                    ┌──────▼──────┐
                    │   Invalid   │
                    │   QR Code   │
                    └─────────────┘
```

---

## 💻 CODE QUAN TRỌNG

### Validate QR Code

```php
public function scan(Request $request)
{
    $qrCode = $request->input('qr_code');

    // Tìm booking seat với QR code
    $bookingSeat = BookingSeat::where('qr_code', $qrCode)
        ->with(['booking.user', 'booking.showtime.movie', 'seat'])
        ->first();

    // QR không tồn tại
    if (!$bookingSeat) {
        return response()->json([
            'success' => false,
            'message' => 'QR Code không hợp lệ!'
        ], 404);
    }

    $booking = $bookingSeat->booking;

    // Booking chưa confirmed
    if ($booking->status !== 'confirmed') {
        return response()->json([
            'success' => false,
            'message' => 'Vé chưa được thanh toán hoặc đã hủy!'
        ], 400);
    }

    // Đã check-in rồi
    if ($bookingSeat->checked_in_at) {
        return response()->json([
            'success' => false,
            'message' => 'Vé này đã được check-in lúc ' .
                $bookingSeat->checked_in_at->format('H:i d/m/Y'),
            'data' => $this->formatTicketData($bookingSeat)
        ], 400);
    }

    // Kiểm tra thời gian suất chiếu
    $showtime = $booking->showtime;
    $showtimeStart = Carbon::parse($showtime->show_date . ' ' . $showtime->start_time);

    // Quá sớm (> 30 phút trước)
    if ($showtimeStart->diffInMinutes(now()) > 30 && now()->lt($showtimeStart)) {
        return response()->json([
            'success' => false,
            'message' => 'Chưa đến giờ check-in. Vui lòng quay lại sau!'
        ], 400);
    }

    // Quá muộn (> 15 phút sau khi bắt đầu)
    if (now()->gt($showtimeStart->addMinutes(15))) {
        return response()->json([
            'success' => false,
            'message' => 'Đã quá giờ check-in!'
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'QR hợp lệ!',
        'data' => $this->formatTicketData($bookingSeat)
    ]);
}
```

### Confirm Check-in

```php
public function checkin(Request $request)
{
    $qrCode = $request->input('qr_code');

    $bookingSeat = BookingSeat::where('qr_code', $qrCode)->first();

    if (!$bookingSeat || $bookingSeat->checked_in_at) {
        return response()->json([
            'success' => false,
            'message' => 'Không thể check-in!'
        ], 400);
    }

    // Update check-in time
    $bookingSeat->update([
        'checked_in_at' => now()
    ]);

    // Nếu tất cả ghế trong booking đã check-in → update booking status
    $booking = $bookingSeat->booking;
    $allCheckedIn = $booking->bookingSeats()
        ->whereNull('checked_in_at')
        ->count() === 0;

    if ($allCheckedIn) {
        $booking->update(['status' => 'checked_in']);
    }

    return response()->json([
        'success' => true,
        'message' => 'Check-in thành công!'
    ]);
}
```

### Format Ticket Data

```php
private function formatTicketData($bookingSeat)
{
    $booking = $bookingSeat->booking;
    $showtime = $booking->showtime;

    return [
        'booking_code' => $booking->booking_code,
        'customer_name' => $booking->user->name,
        'movie_title' => $showtime->movie->title,
        'room_name' => $showtime->room->name,
        'show_date' => $showtime->show_date->format('d/m/Y'),
        'show_time' => $showtime->start_time,
        'seat_code' => $bookingSeat->seat->seat_code,
        'seat_type' => $bookingSeat->seat->type,
        'checked_in' => $bookingSeat->checked_in_at ? true : false,
        'checked_in_at' => $bookingSeat->checked_in_at?->format('H:i d/m/Y'),
    ];
}
```

---

## 🎨 UI - QR Check-in Screen

```
┌─────────────────────────────────────────────────────────────┐
│                    🎬 QR CHECK-IN                           │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│           ┌─────────────────────────────┐                  │
│           │                             │                  │
│           │      📷 CAMERA SCAN         │                  │
│           │                             │                  │
│           │    [Quét mã QR vé ở đây]    │                  │
│           │                             │                  │
│           └─────────────────────────────┘                  │
│                                                             │
│  Hoặc nhập mã thủ công: [____________________] [Kiểm tra]  │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  ✓ VÉ HỢP LỆ                                        │   │
│  │                                                      │   │
│  │  Khách hàng: Nguyễn Văn A                           │   │
│  │  Phim: Aquaman 2                                    │   │
│  │  Phòng: Room 1                                      │   │
│  │  Suất: 19:00 - 15/01/2024                          │   │
│  │  Ghế: E5 (VIP)                                      │   │
│  │                                                      │   │
│  │              [✓ XÁC NHẬN CHECK-IN]                  │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔒 QR SECURITY

### Tại sao QR an toàn?

```
1. SHA-256 Hash: Không thể đoán ngược
   - Input: booking_id + seat_id + showtime_id + app.key
   - Output: 64 ký tự hex string

2. Unique per seat: Mỗi ghế 1 QR riêng
   - 2 ghế cùng booking → 2 QR khác nhau
   - Ai có QR nào thì check-in ghế đó

3. Time validation:
   - Không check-in quá sớm (> 30 phút trước)
   - Không check-in quá muộn (> 15 phút sau)

4. One-time use:
   - Check-in rồi thì checked_in_at != null
   - Scan lại → báo "Đã check-in"
```

### Possible Attacks & Prevention

| Attack | Prevention |
|--------|------------|
| QR Forgery | SHA-256 với app.key secret |
| QR Sharing | One-time check-in |
| Replay Attack | checked_in_at timestamp |
| Brute Force | 64 ký tự = 2^256 combinations |

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: "QR code tạo như thế nào?"

```
"QR code = SHA-256 hash của:
- booking_id
- seat_id
- showtime_id
- app.key (secret config)

Ví dụ: hash('sha256', '123-45-67-abc123secret')
→ 'a1b2c3d4e5f6...' (64 ký tự)

Không thể đoán ngược vì SHA-256 là one-way function."
```

### Q: "Check-in 2 lần thì sao?"

```
"Lần 2 sẽ bị reject.
- Lần 1: checked_in_at = NULL → check-in OK, set timestamp
- Lần 2: checked_in_at = '10:30' → báo 'Đã check-in lúc 10:30'

Đây là bảo vệ chống lạm dụng vé."
```

### Q: "Nếu user mất điện thoại thì sao?"

```
"Admin có thể check-in thủ công:
1. Xác minh danh tính (CMND, email)
2. Tìm booking trong hệ thống
3. Check-in từ admin panel

Hoặc user có thể:
1. Login lại trên thiết bị khác
2. Xem lại QR từ email confirmation"
```

### Q: "Offline check-in có được không?"

```
"Hiện tại: Không, cần internet để validate QR.

Nếu muốn offline:
1. Encode booking info vào QR (không chỉ hash)
2. Sign bằng private key
3. Verify bằng public key (không cần DB)

Trade-off: QR code sẽ lớn hơn, phức tạp hơn."
```

---

## 🎯 DEMO TIPS

### Users

```
1. Show danh sách users
2. Filter theo role: "Đây là cách phân biệt admin và user"
3. Demo toggle role (trên user thường)
4. "Admin không thể tự demote mình - bảo vệ hệ thống"
```

### Reviews

```
1. Show reviews pending
2. Demo approve/reject
3. "Reviews được duyệt mới hiển thị cho user khác"
4. Show average rating của phim
```

### QR Check-in

```
1. Mở trang QR check-in
2. Scan hoặc nhập mã QR của booking đã chuẩn bị
3. "Hệ thống hiển thị thông tin vé"
4. Click confirm check-in
5. Thử scan lại → "Đã check-in"
6. "Mỗi vé chỉ check-in được 1 lần"
```

---

## 📝 GHI NHỚ NHANH

### Users
```
✓ Role: user, admin
✓ Status: active, inactive, banned
✓ Admin không thể self-demote/ban
✓ Phải còn ít nhất 1 admin
```

### Reviews
```
✓ Status: pending, approved, rejected
✓ Rating: 1-5 sao
✓ Chỉ approved reviews hiển thị public
```

### QR Check-in
```
✓ QR = SHA-256(booking_id + seat_id + showtime_id + secret)
✓ Mỗi ghế 1 QR riêng
✓ Check-in window: 30 phút trước → 15 phút sau start time
✓ One-time use (tracked by checked_in_at)
```

