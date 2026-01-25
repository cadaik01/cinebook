# CHEATSHEET: QUẢN LÝ PHIM (MOVIES)
## CRUD hoàn chỉnh + Quan hệ Many-to-Many

---

## 🎯 MỤC ĐÍCH

Quản lý phim là nền tảng của hệ thống rạp chiếu phim:
- Thêm/sửa/xóa thông tin phim
- Gắn thể loại (genres) cho phim
- Upload poster
- Quản lý trạng thái phim

---

## 📁 FILES LIÊN QUAN

```
Controller: app/Http/Controllers/Admin/AdminMovieController.php
Model:      app/Models/Movie.php
            app/Models/Genre.php
Views:      resources/views/admin/movies/
            ├── index.blade.php    (danh sách)
            ├── create.blade.php   (form tạo mới)
            ├── edit.blade.php     (form sửa)
            └── show.blade.php     (chi tiết)
Routes:
            GET    /admin/movies           → index
            GET    /admin/movies/create    → create
            POST   /admin/movies           → store
            GET    /admin/movies/{id}      → show
            GET    /admin/movies/{id}/edit → edit
            PUT    /admin/movies/{id}      → update
            DELETE /admin/movies/{id}      → destroy
```

---

## 🗄️ DATABASE SCHEMA

### Table: movies

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| title | VARCHAR(255) | Tên phim |
| description | TEXT | Mô tả phim |
| duration | INT | Thời lượng (phút) |
| release_date | DATE | Ngày khởi chiếu |
| poster | VARCHAR(255) | Path to poster image |
| trailer_url | VARCHAR(255) | Link trailer YouTube |
| director | VARCHAR(255) | Đạo diễn |
| cast | TEXT | Diễn viên |
| language | VARCHAR(50) | Ngôn ngữ |
| rating | VARCHAR(10) | Phân loại (P, C13, C16, C18) |
| status | ENUM | active, coming_soon, ended |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

### Table: genres

| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR(100) | Tên thể loại |
| slug | VARCHAR(100) | URL-friendly name |

### Table: genre_movie (Pivot)

| Column | Type | Description |
|--------|------|-------------|
| genre_id | BIGINT | FK to genres |
| movie_id | BIGINT | FK to movies |

---

## 🔗 QUAN HỆ (RELATIONSHIPS)

```php
// Movie.php
class Movie extends Model
{
    // Một phim có nhiều thể loại
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    // Một phim có nhiều suất chiếu
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}

// Genre.php
class Genre extends Model
{
    // Một thể loại có nhiều phim
    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}
```

### Giải thích Many-to-Many

```
Một phim có thể thuộc NHIỀU thể loại:
   "Avengers" → Action, Sci-Fi, Adventure

Một thể loại có thể chứa NHIỀU phim:
   "Action" → Avengers, John Wick, Fast & Furious

→ Cần bảng trung gian (pivot table): genre_movie
```

---

## 💻 CODE QUAN TRỌNG

### Index - Danh sách phim

```php
public function index(Request $request)
{
    $query = Movie::with('genres'); // Eager loading

    // Search
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $movies = $query->latest()->paginate(10);

    return view('admin.movies.index', compact('movies'));
}
```

### Store - Tạo phim mới

```php
public function store(Request $request)
{
    // Validate
    $validated = $request->validate([
        'title' => 'required|max:255',
        'description' => 'required',
        'duration' => 'required|integer|min:1',
        'release_date' => 'required|date',
        'poster' => 'nullable|image|max:2048',
        'genres' => 'required|array',
        'genres.*' => 'exists:genres,id',
    ]);

    // Upload poster
    if ($request->hasFile('poster')) {
        $validated['poster'] = $request->file('poster')
            ->store('posters', 'public');
    }

    // Create movie
    $movie = Movie::create($validated);

    // Sync genres (Many-to-Many)
    $movie->genres()->sync($request->genres);

    return redirect()->route('admin.movies.index')
        ->with('success', 'Phim đã được tạo thành công!');
}
```

### Sync Genres - Điểm quan trọng

```php
// sync() là method đặc biệt cho Many-to-Many
$movie->genres()->sync([1, 3, 5]);

// Nó sẽ:
// 1. Xóa tất cả genre cũ của movie trong pivot table
// 2. Thêm genre 1, 3, 5 vào pivot table

// Khác với attach():
$movie->genres()->attach([1, 3]); // Chỉ thêm, không xóa cũ
$movie->genres()->detach([1]);    // Chỉ xóa
```

### Update - Cập nhật phim

```php
public function update(Request $request, Movie $movie)
{
    $validated = $request->validate([...]);

    // Update poster nếu có file mới
    if ($request->hasFile('poster')) {
        // Xóa poster cũ
        if ($movie->poster) {
            Storage::disk('public')->delete($movie->poster);
        }
        $validated['poster'] = $request->file('poster')
            ->store('posters', 'public');
    }

    $movie->update($validated);
    $movie->genres()->sync($request->genres);

    return redirect()->route('admin.movies.index')
        ->with('success', 'Phim đã được cập nhật!');
}
```

### Delete - Xóa phim

```php
public function destroy(Movie $movie)
{
    // Kiểm tra có suất chiếu không
    if ($movie->showtimes()->exists()) {
        return back()->with('error',
            'Không thể xóa phim đang có suất chiếu!');
    }

    // Xóa poster
    if ($movie->poster) {
        Storage::disk('public')->delete($movie->poster);
    }

    // Xóa movie (pivot table tự động xóa nhờ cascade)
    $movie->delete();

    return redirect()->route('admin.movies.index')
        ->with('success', 'Phim đã được xóa!');
}
```

---

## ✅ VALIDATION RULES

| Field | Rules | Giải thích |
|-------|-------|------------|
| title | required, max:255 | Bắt buộc, tối đa 255 ký tự |
| description | required | Bắt buộc |
| duration | required, integer, min:1 | Số nguyên > 0 |
| release_date | required, date | Định dạng ngày |
| poster | image, max:2048 | File ảnh, tối đa 2MB |
| genres | required, array | Phải chọn ít nhất 1 |
| genres.* | exists:genres,id | Genre phải tồn tại |
| rating | in:P,C13,C16,C18 | Chỉ nhận các giá trị này |
| status | in:active,coming_soon,ended | Enum |

---

## 🎨 UI COMPONENTS

### Index Page

```
┌─────────────────────────────────────────────────────────────┐
│  QUẢN LÝ PHIM                            [+ Thêm phim mới] │
├─────────────────────────────────────────────────────────────┤
│  Search: [_______________]  Status: [All ▼]  [Tìm kiếm]   │
├─────────────────────────────────────────────────────────────┤
│  # │ Poster │ Tên phim    │ Thể loại      │ Trạng thái │ Actions │
│────┼────────┼─────────────┼───────────────┼────────────┼─────────│
│  1 │ [img]  │ Aquaman 2   │ Action, Sci-Fi│ ● Active   │ ✏️ 🗑️   │
│  2 │ [img]  │ Wonka       │ Family, Comedy│ ● Active   │ ✏️ 🗑️   │
│  3 │ [img]  │ Dune 2      │ Sci-Fi, Drama │ ○ Coming   │ ✏️ 🗑️   │
├─────────────────────────────────────────────────────────────┤
│                    [< 1 2 3 4 5 >]                          │
└─────────────────────────────────────────────────────────────┘
```

### Create/Edit Form

```
┌─────────────────────────────────────────────────────────────┐
│  THÊM PHIM MỚI                                              │
├─────────────────────────────────────────────────────────────┤
│  Tên phim *        [_________________________________]      │
│                                                             │
│  Mô tả *           [_________________________________]      │
│                    [_________________________________]      │
│                                                             │
│  Thời lượng (phút) [____] │ Ngày khởi chiếu [__/__/____]  │
│                                                             │
│  Thể loại *        ☑ Action  ☑ Sci-Fi  ☐ Comedy           │
│                    ☐ Drama   ☐ Horror  ☐ Family            │
│                                                             │
│  Phân loại         [P - Mọi đối tượng ▼]                   │
│                                                             │
│  Poster            [Choose file...] hoặc kéo thả           │
│                    [Preview image here]                     │
│                                                             │
│  Trailer URL       [https://youtube.com/watch?v=...]       │
│                                                             │
│  Trạng thái        ○ Đang chiếu  ○ Sắp chiếu  ○ Đã kết thúc│
│                                                             │
│                              [Hủy]  [Lưu phim]              │
└─────────────────────────────────────────────────────────────┘
```

---

## ❓ CÂU HỎI THƯỜNG GẶP

### Q: "Tại sao dùng Many-to-Many cho genres?"

```
"Vì một phim có thể thuộc nhiều thể loại cùng lúc.
Ví dụ 'Avengers' vừa là Action, vừa là Sci-Fi, vừa là Adventure.
Nếu dùng One-to-Many (1 phim - 1 genre), sẽ mất thông tin.
Many-to-Many với pivot table là giải pháp chuẩn."
```

### Q: "sync() khác gì với attach()?"

```
"attach() chỉ THÊM, không xóa cũ.
sync() sẽ XÓA tất cả cũ rồi thêm mới.

Khi update form, user có thể bỏ chọn một genre.
Nếu dùng attach(), genre cũ vẫn còn.
sync() đảm bảo kết quả khớp với những gì user chọn."
```

### Q: "Xóa phim thì genres có bị xóa không?"

```
"Không. Chỉ xóa bản ghi trong pivot table (genre_movie).
Table genres vẫn nguyên.
Đây là đặc điểm của pivot table - chỉ lưu quan hệ."
```

### Q: "Tại sao không cho xóa phim đang có suất chiếu?"

```
"Để đảm bảo data integrity.
Nếu xóa phim mà suất chiếu vẫn còn:
- Suất chiếu reference đến phim không tồn tại
- Booking của user sẽ lỗi
- Báo cáo doanh thu sẽ thiếu thông tin

Phải hủy/xóa suất chiếu trước, hoặc đánh dấu phim là 'ended'."
```

### Q: "Poster lưu ở đâu?"

```
"Poster lưu trong storage/app/public/posters/
Sử dụng Laravel's Storage facade với disk 'public'.
Cần chạy 'php artisan storage:link' để tạo symlink.
URL truy cập: /storage/posters/filename.jpg"
```

---

## 🎯 DEMO TIPS

### Chuẩn bị

```
✅ 5-10 phim với poster đẹp (lấy từ IMDB)
✅ 5-8 genres có sẵn
✅ Mỗi phim gắn 2-3 genres
✅ Có đủ 3 trạng thái: active, coming_soon, ended
```

### Khi demo

```
1. "Đây là trang quản lý phim - trái tim của rạp chiếu"

2. Demo TẠO PHIM:
   - Điền form (chuẩn bị sẵn data để paste nhanh)
   - Chọn nhiều thể loại "Phim này thuộc cả Action và Sci-Fi"
   - Upload poster
   - Submit → Chỉ message thành công

3. Demo SỬA PHIM:
   - Click edit
   - Đổi 1-2 field
   - Bỏ chọn 1 genre, thêm genre khác
   - "Nhờ sync(), kết quả sẽ khớp với selection mới"

4. Demo XÓA PHIM:
   - Thử xóa phim CÓ suất chiếu → Show error message
   - Xóa phim KHÔNG có suất chiếu → Success
   - "Đây là ràng buộc bảo vệ data integrity"
```

---

## 🔧 EDGE CASES

| Tình huống | Xử lý |
|------------|-------|
| Upload file không phải ảnh | Validation reject |
| File quá 2MB | Validation reject |
| Không chọn genre nào | Validation reject |
| Trùng tên phim | Cho phép (phim có thể remake) |
| Xóa phim có suất chiếu | Không cho, hiện error |
| Xóa genre đang được dùng | Pivot record xóa, phim vẫn còn |

---

## 🔍 USER-FACING MOVIE FILTERS

### Filter Criteria (Now Showing / Upcoming Movies)

| Filter | Description | Options |
|--------|-------------|---------|
| **Genre** | Lọc theo thể loại | All Genres, Action, Drama, Comedy, etc. |
| **Language** | Lọc theo ngôn ngữ | All Languages, Vietnamese, English, Korean, etc. |
| **Rating** | Lọc theo đánh giá | All Ratings, 5 Stars, 4+ Stars, 3+ Stars |
| **Date** | Now Showing: Showtime Date | Date picker (default: today) |
|  | Upcoming: Release Date | Date picker |

### Sort Options

| Sort | Description |
|------|-------------|
| Name (A-Z) | Sắp xếp theo tên tăng dần |
| Name (Z-A) | Sắp xếp theo tên giảm dần |
| Rating (High-Low) | Sắp xếp theo rating giảm dần |
| Release Date | Sắp xếp theo ngày phát hành |

### CSS File

```
resources/css/movie-filter.css
```

### UI Layout

```
┌─────────────────────────────────────────────────────────────┐
│  Genre     │ Language │ Rating  │ Date     │ Sort   │ Btns │
│  [All ▼]   │ [All ▼]  │ [All ▼] │ [📅    ] │ [A-Z ▼]│ [✓][↺]│
└─────────────────────────────────────────────────────────────┘
```

---

## 📝 GHI NHỚ NHANH

```
✓ CRUD đầy đủ: Create, Read, Update, Delete
✓ Many-to-Many với genres qua pivot table
✓ sync() để update genres
✓ Không xóa phim có suất chiếu
✓ Poster lưu storage/app/public/posters/
✓ Validate: title, description, duration, genres bắt buộc
✓ User filters: Genre, Language, Rating, Date
✓ Sort: Name, Rating, Release Date
```

