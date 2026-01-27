# 📱 BaconQrCode Setup & Usage Guide

## 🎯 Vấn đề gặp phải

- Package `SimpleSoftwareIO/simple-qrcode` yêu cầu **Imagick extension** (khó cài trên Windows/XAMPP)
- Email không hiển thị QR code vì thiếu Imagick
- Cần giải pháp thay thế chỉ dùng **GD extension** (đã có sẵn trong XAMPP)

## ✅ Giải pháp: Sử dụng BaconQrCode trực tiếp

BaconQrCode là thư viện nền tảng của SimpleSoftwareIO, hỗ trợ nhiều backend renderer:
- **SVG** (không cần extension, chỉ cần PHP thuần)
- **PNG** (cần GD extension - đã có sẵn)
- **EPS**, **PDF**, v.v.

---

## 📦 1. Cài đặt

### Kiểm tra package đã có chưa:

```bash
composer show | findstr bacon
```

Nếu chưa có, cài đặt:

```bash
composer require bacon/bacon-qr-code
```

### Kiểm tra GD extension (nếu dùng PNG):

```bash
php -m | findstr gd
```

Nếu chưa có, enable trong `php.ini`:

```ini
extension=gd
```

---

## 🔧 2. Cách sử dụng BaconQrCode

### 2.1. Tạo QR Code dạng SVG (Khuyến nghị - Không cần extension)

```php
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

// Tạo renderer với SVG backend
$renderer = new ImageRenderer(
    new RendererStyle(220, 1), // size: 220px, margin: 1
    new SvgImageBackEnd()
);

// Tạo writer
$writer = new Writer($renderer);

// Generate QR code (trả về SVG string)
$qrCodeSvg = $writer->writeString('data-to-encode');

// Dùng trong HTML (embed trực tiếp)
echo $qrCodeSvg;

// Hoặc convert sang base64 để dùng trong email
$qrImageBase64 = base64_encode($qrCodeSvg);
echo '<img src="data:image/svg+xml;base64,' . $qrImageBase64 . '" alt="QR Code">';
```

### 2.2. Tạo QR Code dạng PNG (Cần GD extension)

```php
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd; // Cần Imagick
// HOẶC
use BaconQrCode\Renderer\Image\SvgImageBackEnd; // Không cần extension
use BaconQrCode\Writer;

// SVG backend (khuyến nghị)
$renderer = new ImageRenderer(
    new RendererStyle(220, 1),
    new SvgImageBackEnd()
);

$writer = new Writer($renderer);
$qrCodePng = $writer->writeString('https://example.com');

// Save to file
file_put_contents('qrcode.svg', $qrCodePng);
```

### 2.3. Tùy chỉnh kích thước và margin

```php
// Cú pháp: RendererStyle(size, margin, foregroundColor, backgroundColor)
$style = new RendererStyle(
    300,                    // Kích thước: 300px
    2,                      // Margin: 2 blocks
    [0, 0, 0],             // Màu foreground: đen (RGB)
    [255, 255, 255]        // Màu background: trắng (RGB)
);

$renderer = new ImageRenderer($style, new SvgImageBackEnd());
$writer = new Writer($renderer);
```

---

## 📧 3. Sử dụng trong Email (Blade Template)

### Ví dụ: booking_confirmation.blade.php

```blade
@foreach($groupedSeats as $qrCode => $seats)
    <div class="qr-code-container">
        <div class="seat-info">
            🪑 Seat(s): {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}
        </div>
        
        @php
            try {
                // Sử dụng BaconQrCode với SVG backend
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(220, 1),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                $qrImage = base64_encode($writer->writeString($qrCode));
                $qrImageType = 'svg+xml';
            } catch (\Exception $e) {
                \Log::error('QR Code generation failed', [
                    'error' => $e->getMessage(),
                    'qr_code' => $qrCode
                ]);
                $qrImage = '';
                $qrImageType = 'svg+xml';
            }
        @endphp
        
        @if($qrImage)
            <img src="data:image/{{ $qrImageType }};base64,{{ $qrImage }}" 
                 alt="QR Code - {{ $seats->map(fn($s) => $s->seat->seat_code)->join(', ') }}"
                 style="max-width: 220px; height: auto;">
        @else
            <p style="color: #dc3545;">Unable to generate QR code. Please contact support.</p>
        @endif
        
        <p class="qr-text">{{ $qrCode }}</p>
    </div>
@endforeach
```

---

## 🧪 4. Testing trong Tinker

### Test SVG generation:

```bash
php artisan tinker
```

```php
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

$renderer = new ImageRenderer(
    new RendererStyle(220, 1),
    new SvgImageBackEnd()
);
$writer = new Writer($renderer);
$qr = $writer->writeString('test-booking-123');

echo "QR Code length: " . strlen($qr) . " bytes\n";
echo "First 100 chars: " . substr($qr, 0, 100) . "...\n";
```

### Test và save file:

```php
$qrCode = $writer->writeString('https://tcacine.com/booking/123');
file_put_contents(storage_path('app/test-qr.svg'), $qrCode);
echo "Saved to: " . storage_path('app/test-qr.svg');
```

---

## 🎨 5. So sánh SVG vs PNG

| Tiêu chí | SVG | PNG |
|----------|-----|-----|
| **Extension cần** | Không | GD (hoặc Imagick) |
| **Kích thước file** | ✅ Nhỏ (~1-2KB) | ❌ Lớn hơn (~5-10KB) |
| **Chất lượng** | ✅ Vector (scale vô hạn) | ❌ Raster (bị vỡ khi phóng to) |
| **Hỗ trợ email** | ✅ Tốt (hầu hết email client) | ✅ Tốt |
| **Hiệu suất** | ✅ Nhanh | ⚠️ Trung bình |
| **Khuyến nghị** | ✅ **Dùng cho email** | Dùng cho print/download |

---

## 🔥 6. Helper Function (Tái sử dụng)

Tạo file: `app/Helpers/QrCodeHelper.php`

```php
<?php

namespace App\Helpers;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class QrCodeHelper
{
    /**
     * Generate QR code as SVG base64
     * 
     * @param string $data Data to encode
     * @param int $size Size in pixels (default: 220)
     * @param int $margin Margin in blocks (default: 1)
     * @return string Base64 encoded SVG
     */
    public static function generateSvgBase64($data, $size = 220, $margin = 1)
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle($size, $margin),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $svg = $writer->writeString($data);
            
            return base64_encode($svg);
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }
    
    /**
     * Generate QR code as raw SVG string
     * 
     * @param string $data Data to encode
     * @param int $size Size in pixels
     * @param int $margin Margin in blocks
     * @return string SVG string
     */
    public static function generateSvg($data, $size = 220, $margin = 1)
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, $margin),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        
        return $writer->writeString($data);
    }
    
    /**
     * Generate QR code and save to file
     * 
     * @param string $data Data to encode
     * @param string $path File path to save
     * @param int $size Size in pixels
     * @return bool Success status
     */
    public static function saveToFile($data, $path, $size = 220)
    {
        try {
            $svg = self::generateSvg($data, $size);
            return file_put_contents($path, $svg) !== false;
        } catch (\Exception $e) {
            \Log::error('QR Code save failed', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
```

### Đăng ký Helper trong `composer.json`:

```json
{
    "autoload": {
        "files": [
            "app/Helpers/QrCodeHelper.php"
        ],
        "psr-4": {
            "App\\": "app/"
        }
    }
}
```

Sau đó chạy:

```bash
composer dump-autoload
```

### Sử dụng Helper:

```php
use App\Helpers\QrCodeHelper;

// Trong Blade template
@php
    $qrImage = QrCodeHelper::generateSvgBase64($qrCode);
@endphp

<img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR Code">

// Trong Controller
$qrBase64 = QrCodeHelper::generateSvgBase64('booking-123');
QrCodeHelper::saveToFile('booking-123', storage_path('app/qrcodes/booking-123.svg'));
```

---

## 🐛 7. Troubleshooting

### Lỗi: "Class not found"

```bash
composer dump-autoload
php artisan clear-compiled
php artisan config:clear
```

### Lỗi: "Call to undefined function imagecreatetruecolor"

➡️ Bạn đang dùng PNG backend nhưng thiếu GD. Chuyển sang SVG:

```php
// ❌ Sai
new ImagickImageBackEnd()

// ✅ Đúng
new SvgImageBackEnd()
```

### QR code bị mờ trong email:

➡️ Tăng kích thước:

```php
new RendererStyle(300, 2) // size: 300px, margin: 2
```

### Email client không hiển thị SVG:

➡️ Một số email client cũ không hỗ trợ SVG. Chuyển sang PNG với GD:

```bash
# Enable GD trong php.ini
extension=gd
```

---

## 📚 8. Tài liệu tham khảo

- **BaconQrCode GitHub**: https://github.com/Bacon/BaconQrCode
- **SimpleSoftwareIO (wrapper)**: https://github.com/SimpleSoftwareIO/simple-qrcode
- **QR Code Specification**: https://www.qrcode.com/en/about/standards.html

---

## ✅ Tóm tắt

| Trước (SimpleSoftwareIO) | Sau (BaconQrCode trực tiếp) |
|--------------------------|------------------------------|
| ❌ Cần Imagick extension | ✅ Chỉ cần PHP thuần |
| ❌ QR không hiển thị | ✅ QR hiển thị SVG |
| ❌ Khó debug | ✅ Dễ debug với try-catch |
| ❌ PNG cố định | ✅ Linh hoạt SVG/PNG |

**Khuyến nghị:** Dùng **SVG backend** cho email, **PNG với GD** cho download/print.
