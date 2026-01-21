# 04. HỆ THỐNG ĐĂNG NHẬP/ĐĂNG KÝ

## 🎯 Mục tiêu bài học

Sau bài học này, bạn sẽ có:

- ✅ Hệ thống đăng ký tài khoản
- ✅ Hệ thống đăng nhập/đăng xuất
- ✅ Middleware bảo vệ routes
- ✅ Session management
- ✅ Form validation

**Thời gian ước tính**: 60-75 phút

---

## 📚 Kiến thức cần biết

- HTTP sessions
- Password hashing (bcrypt)
- Laravel authentication basics
- Middleware concept
- Form validation

---

## 🛠️ BƯỚC 1: TẠO AUTHENTICATION CONTROLLER

### 1.1. Generate LoginController

```bash
php artisan make:controller LoginController
```

**File**: `app/Http/Controllers/LoginController.php`

```php
// ================== GIẢI THÍCH CHI TIẾT ==================
// Đây là controller xử lý toàn bộ logic xác thực (authentication) cho hệ thống.
// Sử dụng các Facade mạnh mẽ của Laravel: Auth, Hash, Session.
// Nên tách riêng controller này để dễ bảo trì, mở rộng (ví dụ thêm Google/Facebook login).
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        // Nếu đã đăng nhập, redirect về home
        // Tối ưu: Có thể truyền thêm thông báo "Bạn đã đăng nhập rồi!"
        if (Auth::check()) {
            return redirect()->route('home')->with('info', 'Bạn đã đăng nhập!');
        }
        // Trả về view login
        return view('login.login');
    }

    /**
     * Show register form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('login.register');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Validate input
        // Gợi ý tối ưu: Có thể tách validate thành FormRequest riêng để tái sử dụng và dễ test hơn
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        // Attempt login
        // Gợi ý tối ưu: Có thể giới hạn số lần login sai bằng Laravel Throttle (để chống brute-force)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate session để chống session fixation attack
            $request->session()->regenerate();

            // Check user role and redirect accordingly
            // Gợi ý tối ưu: Nên dùng policy/gate cho phân quyền phức tạp
            if (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Chào mừng Admin ' . Auth::user()->name);
            }

            return redirect()->intended(route('home'))
                ->with('success', 'Đăng nhập thành công!');
        }

        // Login failed
        // Gợi ý tối ưu: Có thể log lại các lần đăng nhập thất bại để phát hiện tấn công
        return back()
            ->withErrors(['email' => 'Email hoặc mật khẩu không đúng'])
            ->withInput($request->only('email'));
    }

    /**
     * Handle register request.
     */
    public function register(Request $request)
    {
        // Validate input
        // Gợi ý tối ưu: Có thể tách validate thành FormRequest riêng, hoặc dùng Rule::unique để custom message tốt hơn
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:100',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Họ tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã được sử dụng',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Create user
        // Gợi ý tối ưu: Có thể dùng sự kiện (event) để gửi email chào mừng hoặc xác thực email
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Default role
        ]);

        // Auto login after register
        Auth::login($user);

        return redirect()->route('home')
            ->with('success', 'Đăng ký thành công! Chào mừng ' . $user->name);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Đăng xuất user
        Auth::logout();

        // Invalidate session để xóa toàn bộ session data
        $request->session()->invalidate();
        // Regenerate CSRF token để tránh tấn công CSRF sau logout
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Đăng xuất thành công!');
    }

    /**
     * Check login status (for AJAX).
     */
    public function checkLoginStatus()
    {
        // API kiểm tra trạng thái đăng nhập, có thể dùng cho frontend SPA hoặc AJAX
        return response()->json([
            'logged_in' => Auth::check(),
            'user' => Auth::check() ? [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'role' => Auth::user()->role,
            ] : null,
        ]);
    }
}
```

📝 **Giải thích**:

- `Auth::attempt()`: Kiểm tra credentials và tự động login
- `Hash::make()`: Hash mật khẩu bằng bcrypt
- `$request->session()->regenerate()`: Tạo session ID mới (bảo mật)
- `redirect()->intended()`: Redirect về trang đã cố truy cập trước khi login

---

## 🛠️ BƯỚC 2: TẠO MIDDLEWARE BẢO VỆ

### 2.1. Tạo CheckRole Middleware

```bash
php artisan make:middleware CheckRole
```

**File**: `app/Http/Middleware/CheckRole.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập để tiếp tục');
        }

        // Check if user has required role
        if (Auth::user()->role !== $role) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }

        return $next($request);
    }
}
```

### 2.2. Đăng ký Middleware

**File**: `bootstrap/app.php`

Thêm vào phần middleware aliases:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Đăng ký middleware alias
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

---

## 🛠️ BƯỚC 3: TẠO ROUTES

**File**: `routes/web.php`

Thêm các routes authentication:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// ============================================
// AUTHENTICATION ROUTES
// ============================================

// Guest routes (chỉ dành cho người chưa đăng nhập)
Route::middleware('guest')->group(function () {
    // Show forms
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');
    Route::get('/register', [LoginController::class, 'showRegisterForm'])
        ->name('register');

    // Handle forms
    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.submit');
    Route::post('/register', [LoginController::class, 'register'])
        ->name('register.submit');
    // Gợi ý tối ưu: Có thể thêm route xác thực email, quên mật khẩu ở đây
});

// Authenticated routes (cần đăng nhập)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
    // Gợi ý tối ưu: Có thể thêm route đổi mật khẩu, cập nhật thông tin cá nhân ở đây
});

// AJAX route (check login status)
Route::get('/api/check-login', [LoginController::class, 'checkLoginStatus'])
    ->name('api.check-login');

// ============================================
// HOME ROUTE (Temporary)
// ============================================
Route::get('/', function () {
    return view('welcome');
})->name('home');
```

📝 **Giải thích Middleware**:

- `guest`: Chỉ cho phép người chưa đăng nhập (có sẵn trong Laravel)
- `auth`: Chỉ cho phép người đã đăng nhập (có sẵn)
- `role:admin`: Custom middleware kiểm tra role

---

## 🛠️ BƯỚC 4: TẠO LOGIN VIEW

### 4.1. Tạo CSS cho Login

**File**: `resources/css/login.css`

```css
/* Login & Register Page Styles */

.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
        135deg,
        var(--bg-dark) 0%,
        var(--bg-dark-secondary) 100%
    );
    padding: var(--spacing-lg);
}

.auth-card {
    background: var(--bg-card);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-xl);
    padding: var(--spacing-2xl);
    width: 100%;
    max-width: 450px;
}

.auth-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
}

.auth-logo {
    font-size: var(--font-size-3xl);
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: var(--spacing-sm);
}

.auth-title {
    font-size: var(--font-size-2xl);
    margin-bottom: var(--spacing-xs);
}

.auth-subtitle {
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
}

/* Form Styles */
.auth-form {
    margin-bottom: var(--spacing-lg);
}

.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-label {
    display: block;
    margin-bottom: var(--spacing-sm);
    font-weight: 500;
    color: var(--text-primary);
    font-size: var(--font-size-sm);
}

.form-input {
    width: 100%;
    padding: var(--spacing-md);
    background-color: var(--bg-dark);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: var(--font-size-base);
    transition: all var(--transition-base);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    background-color: var(--bg-dark-secondary);
}

.form-input.error {
    border-color: var(--error-color);
}

.form-error {
    color: var(--error-color);
    font-size: var(--font-size-sm);
    margin-top: var(--spacing-xs);
    display: block;
}

.form-checkbox-group {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.form-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-checkbox-label {
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
    cursor: pointer;
    user-select: none;
}

/* Alert Messages */
.alert {
    padding: var(--spacing-md);
    border-radius: var(--radius-md);
    margin-bottom: var(--spacing-lg);
    font-size: var(--font-size-sm);
}

.alert-success {
    background-color: rgba(70, 211, 105, 0.1);
    border: 1px solid var(--success-color);
    color: var(--success-color);
}

.alert-error {
    background-color: rgba(244, 67, 54, 0.1);
    border: 1px solid var(--error-color);
    color: var(--error-color);
}

/* Auth Footer */
.auth-footer {
    text-align: center;
    padding-top: var(--spacing-lg);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.auth-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
}

.auth-link:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 576px) {
    .auth-card {
        padding: var(--spacing-lg);
    }

    .auth-logo {
        font-size: var(--font-size-2xl);
    }

    .auth-title {
        font-size: var(--font-size-xl);
    }
}
```

### 4.2. Import CSS vào app.css

**File**: `resources/css/app.css`

Thêm dòng:

```css
@import "./login.css";
```

### 4.3. Tạo Login View

**File**: `resources/views/login/login.blade.php`

```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Cinebook</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            {{-- Header --}}
            <div class="auth-header">
                <div class="auth-logo">🎬 CINEBOOK</div>
                <h1 class="auth-title">Đăng nhập</h1>
                <p class="auth-subtitle">Chào mừng bạn quay trở lại!</p>
            </div>

            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error Message --}}
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Login Form --}}
            <form action="{{ route('login.submit') }}" method="POST" class="auth-form">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input @error('email') error @enderror"
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input @error('password') error @enderror"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="form-group">
                    <div class="form-checkbox-group">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="form-checkbox"
                        >
                        <label for="remember" class="form-checkbox-label">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn btn-primary btn-block">
                    Đăng nhập
                </button>
            </form>

            {{-- Footer --}}
            <div class="auth-footer">
                <p class="text-secondary">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="auth-link">Đăng ký ngay</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 🛠️ BƯỚC 5: TẠO REGISTER VIEW

**File**: `resources/views/login/register.blade.php`

```blade
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Cinebook</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            {{-- Header --}}
            <div class="auth-header">
                <div class="auth-logo">🎬 CINEBOOK</div>
                <h1 class="auth-title">Đăng ký tài khoản</h1>
                <p class="auth-subtitle">Tạo tài khoản để đặt vé xem phim</p>
            </div>

            {{-- Register Form --}}
            <form action="{{ route('register.submit') }}" method="POST" class="auth-form">
                @csrf

                {{-- Name --}}
                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên *</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-input @error('name') error @enderror"
                        value="{{ old('name') }}"
                        placeholder="Nguyễn Văn A"
                        required
                        autofocus
                    >
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input @error('email') error @enderror"
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
                        required
                    >
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="form-group">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        class="form-input @error('phone') error @enderror"
                        value="{{ old('phone') }}"
                        placeholder="0901234567"
                    >
                    @error('phone')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- City --}}
                <div class="form-group">
                    <label for="city" class="form-label">Thành phố</label>
                    <input
                        type="text"
                        id="city"
                        name="city"
                        class="form-input @error('city') error @enderror"
                        value="{{ old('city') }}"
                        placeholder="Hồ Chí Minh"
                    >
                    @error('city')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu *</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input @error('password') error @enderror"
                        placeholder="Ít nhất 6 ký tự"
                        required
                    >
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Xác nhận mật khẩu *</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-input"
                        placeholder="Nhập lại mật khẩu"
                        required
                    >
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn btn-primary btn-block">
                    Đăng ký
                </button>
            </form>

            {{-- Footer --}}
            <div class="auth-footer">
                <p class="text-secondary">
                    Đã có tài khoản?
                    <a href="{{ route('login') }}" class="auth-link">Đăng nhập ngay</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
```

---

## 🛠️ BƯỚC 6: CẬP NHẬT USER MODEL

**File**: `app/Models/User.php`

Đảm bảo password được hash tự động:

```php
// ================== GIẢI THÍCH MODEL ==================
// $fillable: Cho phép gán hàng loạt các trường này khi tạo user
// $hidden: Ẩn password khi trả về JSON (bảo mật)
// $casts: Tự động chuyển kiểu dữ liệu khi lấy từ DB
protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'city',
    'avatar_url',
    'role',
];

protected $hidden = [
    'password',
];

protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];

// Gợi ý tối ưu: Có thể thêm mutator để tự động hash password khi set
// public function setPasswordAttribute($value) {
//     $this->attributes['password'] = Hash::make($value);
// }
// => Khi đó chỉ cần $user->password = '123456'; sẽ tự hash, không lo quên hash khi tạo user thủ công
```

---

## ✅ TEST & VERIFY

### Test 1: Đăng ký tài khoản mới

1. Start server: `php artisan serve` và `npm run dev`
2. Truy cập: `http://localhost:8000/register`
3. Điền form đăng ký:
    - Họ tên: Test User
    - Email: test@example.com
    - Mật khẩu: 123456
    - Xác nhận mật khẩu: 123456
4. Click "Đăng ký"

✅ **Kết quả**: Redirect về trang chủ với message "Đăng ký thành công!"

### Test 2: Đăng nhập

1. Truy cập: `http://localhost:8000/login`
2. Điền:
    - Email: test@example.com
    - Mật khẩu: 123456
3. Click "Đăng nhập"

✅ **Kết quả**: Đăng nhập thành công, redirect về home

### Test 3: Validation errors

1. Đăng ký với email đã tồn tại
2. Đăng nhập với sai mật khẩu
3. Đăng ký với mật khẩu không khớp

✅ **Kết quả**: Hiển thị error messages màu đỏ

### Test 4: Kiểm tra database

```sql
SELECT * FROM users ORDER BY id DESC LIMIT 5;
```

✅ **Kết quả**: User mới đã được tạo với password đã hash

---

## 🎯 THỰC HÀNH

### Bài tập 1: Thêm "Forgot Password" link

Thêm link "Quên mật khẩu?" vào trang login (chỉ UI, chưa cần chức năng)

### Bài tập 2: Custom validation messages

Thử sửa các validation messages thành tiếng Việt đẹp hơn

### Bài tập 3: Test với Tinker

```bash
php artisan tinker

# Tạo user mới
$user = User::create([
    'name' => 'Admin Test',
    'email' => 'admin@test.com',
    'password' => Hash::make('admin123'),
    'role' => 'admin'
]);

# Kiểm tra password
Hash::check('admin123', $user->password); // true
```

---

## 🐛 TROUBLESHOOTING

### Lỗi 1: "Class 'Hash' not found"

**Giải pháp**: Thêm `use Illuminate\Support\Facades\Hash;`

### Lỗi 2: Validation errors không hiển thị

**Giải pháp**: Kiểm tra `@error` directive và `$errors` variable

### Lỗi 3: Session không lưu sau login

**Giải pháp**:

- Kiểm tra `SESSION_DRIVER` trong `.env` (phải là `file` hoặc `database`)
- Chạy: `php artisan session:table` và `php artisan migrate`

### Lỗi 4: Redirect loop

**Giải pháp**: Kiểm tra middleware `guest` và `auth` đã đúng chưa

---

## 📝 TÓM TẮT

### Đã học được gì?

1. **Authentication flow**: Register → Login → Logout
2. **Password hashing**: Dùng `Hash::make()` và `bcrypt`
3. **Form validation**: Validate input, hiển thị errors
4. **Session management**: Regenerate session, remember me
5. **Middleware**: Bảo vệ routes theo role
6. **Blade directives**: `@auth`, `@guest`, `@error`

### Files đã tạo

```
app/
├── Http/
│   ├── Controllers/
│   │   └── LoginController.php
│   └── Middleware/
│       └── CheckRole.php
resources/
├── css/
│   └── login.css
└── views/
    └── login/
        ├── login.blade.php
        └── register.blade.php
```

---

## 🚀 BƯỚC TIẾP THEO

**Bài tiếp**: [05. Frontend Basics →](05_frontend_basics.md)

Trong bài tiếp theo, bạn sẽ tạo:

1. Layout chính (header, footer)
2. Trang chủ với phim nổi bật
3. Navigation menu
4. Responsive design

**Thời gian ước tính**: 75-90 phút

---

**Bài trước**: [← 03. Models Step by Step](03_models_step_by_step.md)
**Series**: Cinebook Tutorial - Step by Step
**Cập nhật**: January 2026
