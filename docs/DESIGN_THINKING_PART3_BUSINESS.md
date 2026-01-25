# DESIGN THINKING PART 3: BUSINESS INTELLIGENCE
## "Kiểm soát lời lỗ của rạp phim"

---

## Lời mở đầu

Một admin panel tốt không chỉ quản lý data - nó phải **giúp ra quyết định kinh doanh**.

Phần này tập trung vào các tính năng giúp chủ rạp phim:
- Hiểu khách hàng
- Tối ưu doanh thu
- Kiểm soát chi phí
- Dự đoán xu hướng

---

## PHẦN A: DASHBOARD ANALYTICS NÂNG CAO

### 1. KPI Cards - Metrics Quan Trọng

#### Hiện tại

```
+----------------+  +----------------+
| Total Revenue  |  | Total Bookings |
| 50,000,000 VND |  | 1,234          |
+----------------+  +----------------+
```

#### Nâng cấp: So sánh với kỳ trước

```
+------------------------+  +------------------------+
| Revenue This Month     |  | Revenue Last Month     |
| 50,000,000 VND         |  | 45,000,000 VND         |
| ▲ +11.1% vs last month |  |                        |
+------------------------+  +------------------------+

+------------------------+  +------------------------+
| Bookings Today         |  | Avg. Booking Value     |
| 156                    |  | 285,000 VND            |
| ▲ +23% vs yesterday    |  | ▼ -5% vs last week     |
+------------------------+  +------------------------+
```

#### Implementation

```php
// DashboardController.php
public function getKPIs()
{
    $thisMonth = Booking::where('status', 'confirmed')
        ->whereMonth('created_at', now()->month)
        ->sum('total_price');

    $lastMonth = Booking::where('status', 'confirmed')
        ->whereMonth('created_at', now()->subMonth()->month)
        ->sum('total_price');

    $growth = $lastMonth > 0
        ? (($thisMonth - $lastMonth) / $lastMonth) * 100
        : 100;

    return [
        'revenue' => $thisMonth,
        'growth' => round($growth, 1),
        'trend' => $growth >= 0 ? 'up' : 'down',
    ];
}
```

---

### 2. Revenue Breakdown

#### Theo nguồn thu

```
┌─────────────────────────────────────────────────┐
│ DOANH THU THEO NGUỒN                            │
├─────────────────────────────────────────────────┤
│ Vé xem phim    ████████████████████  85% │ 42.5M │
│ Đồ ăn/uống     ████████             10% │ 5.0M  │
│ Quảng cáo      ██                    3% │ 1.5M  │
│ Cho thuê       █                     2% │ 1.0M  │
└─────────────────────────────────────────────────┘
```

#### Theo phim

```php
$revenueByMovie = DB::table('bookings')
    ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
    ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
    ->select([
        'movies.title',
        DB::raw('SUM(bookings.total_price) as revenue'),
        DB::raw('COUNT(bookings.id) as tickets'),
    ])
    ->where('bookings.status', 'confirmed')
    ->whereBetween('bookings.created_at', [$startDate, $endDate])
    ->groupBy('movies.id', 'movies.title')
    ->orderByDesc('revenue')
    ->limit(10)
    ->get();
```

#### Theo khung giờ

```
┌─────────────────────────────────────────────────┐
│ DOANH THU THEO KHUNG GIỜ                        │
├─────────────────────────────────────────────────┤
│ Sáng (9-12h)     ████               15% │ 7.5M  │
│ Chiều (12-18h)   ████████           30% │ 15M   │
│ Tối (18-22h)     ████████████████   55% │ 27.5M │
└─────────────────────────────────────────────────┘
```

**Insight**: Tối là "giờ vàng" - cần tập trung suất chiếu phim hot vào tối.

---

### 3. Occupancy Rate (Tỷ lệ lấp đầy)

#### Metric quan trọng nhất

```
Occupancy Rate = (Số ghế bán được / Tổng số ghế) × 100%
```

#### Dashboard View

```
┌─────────────────────────────────────────────────┐
│ TỶ LỆ LẤP ĐẦY THEO PHÒNG                       │
├─────────────────────────────────────────────────┤
│ Room 1 (120 ghế)  ████████████████  78%         │
│ Room 2 (80 ghế)   ████████████      65%         │
│ Room 3 (150 ghế)  ██████████        52%         │
│ Room 4 VIP (40)   ████████████████████ 95%      │
└─────────────────────────────────────────────────┘

│ Trung bình tuần này: 72%  (▲ +5% vs tuần trước) │
```

#### Implementation

```php
public function getOccupancyRate($roomId, $dateRange)
{
    // Tổng số ghế × số suất chiếu
    $totalCapacity = DB::table('showtimes')
        ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
        ->where('showtimes.room_id', $roomId)
        ->whereBetween('showtimes.show_date', $dateRange)
        ->sum('rooms.capacity');

    // Số ghế đã bán
    $soldSeats = DB::table('booking_seats')
        ->join('showtimes', 'booking_seats.showtime_id', '=', 'showtimes.id')
        ->where('showtimes.room_id', $roomId)
        ->where('booking_seats.status', 'confirmed')
        ->whereBetween('showtimes.show_date', $dateRange)
        ->count();

    return $totalCapacity > 0
        ? round(($soldSeats / $totalCapacity) * 100, 1)
        : 0;
}
```

#### Actionable Insights

```
IF Occupancy < 30%:
  → Giảm suất chiếu hoặc đổi phim
  → Chạy promotion

IF Occupancy > 90%:
  → Thêm suất chiếu
  → Tăng giá nhẹ (dynamic pricing)
```

---

## PHẦN B: BÁO CÁO DOANH THU CHI TIẾT

### 1. Daily Revenue Report

```
┌─────────────────────────────────────────────────────────────┐
│ BÁO CÁO DOANH THU NGÀY 15/01/2024                          │
├──────────────────┬──────────┬──────────┬──────────┬────────┤
│ Suất chiếu       │ Phim     │ Phòng    │ Vé bán   │ Doanh thu │
├──────────────────┼──────────┼──────────┼──────────┼────────┤
│ 09:00            │ Phim A   │ Room 1   │ 45/120   │ 4.5M   │
│ 10:30            │ Phim B   │ Room 2   │ 60/80    │ 6.0M   │
│ 14:00            │ Phim A   │ Room 1   │ 80/120   │ 8.0M   │
│ 16:30            │ Phim C   │ Room 3   │ 100/150  │ 10.0M  │
│ 19:00            │ Phim A   │ Room 1   │ 118/120  │ 14.0M  │
│ 19:30            │ Phim B   │ Room 2   │ 78/80    │ 9.0M   │
│ 21:30            │ Phim C   │ Room 3   │ 130/150  │ 15.0M  │
├──────────────────┴──────────┴──────────┴──────────┴────────┤
│ TỔNG CỘNG                              611 vé    │ 66.5M  │
└─────────────────────────────────────────────────────────────┘
```

### 2. Weekly Comparison

```php
public function getWeeklyComparison()
{
    $thisWeek = $this->getRevenueByDayOfWeek(now()->startOfWeek(), now());
    $lastWeek = $this->getRevenueByDayOfWeek(
        now()->subWeek()->startOfWeek(),
        now()->subWeek()->endOfWeek()
    );

    return [
        'labels' => ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
        'thisWeek' => $thisWeek,
        'lastWeek' => $lastWeek,
    ];
}
```

```
Chart: So sánh doanh thu theo ngày

     This Week ████
     Last Week ░░░░

Mon  ████████░░░░░░  +15%
Tue  ██████░░░░░░░░  -10%
Wed  ████████████░░  +25%
Thu  ██████████░░░░  +5%
Fri  ████████████████ +30%
Sat  ████████████████████ +40%
Sun  ██████████████████░░ +20%
```

---

### 3. Monthly P&L Statement

```
┌─────────────────────────────────────────────────────────────┐
│ BÁO CÁO LỜI LỖ THÁNG 01/2024                               │
├─────────────────────────────────────────────────────────────┤
│ DOANH THU                                                   │
│   Bán vé                              450,000,000           │
│   Đồ ăn/uống                           50,000,000           │
│   Quảng cáo                            15,000,000           │
│   Khác                                  5,000,000           │
│                                      ─────────────          │
│   TỔNG DOANH THU                      520,000,000           │
├─────────────────────────────────────────────────────────────┤
│ CHI PHÍ                                                     │
│   Bản quyền phim (55%)               247,500,000           │
│   Lương nhân viên                     80,000,000           │
│   Tiền thuê mặt bằng                  50,000,000           │
│   Điện/nước/internet                  20,000,000           │
│   Bảo trì thiết bị                    10,000,000           │
│   Marketing                           15,000,000           │
│   Chi phí khác                         7,500,000           │
│                                      ─────────────          │
│   TỔNG CHI PHÍ                       430,000,000           │
├─────────────────────────────────────────────────────────────┤
│ LỢI NHUẬN RÒNG                        90,000,000           │
│ Biên lợi nhuận                            17.3%            │
└─────────────────────────────────────────────────────────────┘
```

---

## PHẦN C: DỰ ĐOÁN XU HƯỚNG (FORECASTING)

### 1. Revenue Forecasting

#### Phương pháp đơn giản: Moving Average

```php
public function forecastRevenue($days = 7)
{
    // Lấy doanh thu 30 ngày qua
    $historicalData = DB::table('bookings')
        ->select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as revenue')
        ])
        ->where('status', 'confirmed')
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->get();

    // Simple Moving Average (7 ngày)
    $movingAverage = $historicalData
        ->take(-7)
        ->avg('revenue');

    // Forecast
    $forecast = [];
    for ($i = 1; $i <= $days; $i++) {
        $forecast[] = [
            'date' => now()->addDays($i)->format('Y-m-d'),
            'predicted_revenue' => $movingAverage,
            'confidence' => 'medium', // Có thể tính confidence interval
        ];
    }

    return $forecast;
}
```

#### Hiển thị

```
Chart: Dự đoán doanh thu 7 ngày tới

        Actual ────
        Forecast - - -

    │    ╱╲
    │   ╱  ╲    ╱╲        - - - - -
    │  ╱    ╲  ╱  ╲      - - - - - -
    │ ╱      ╲╱    ╲    - - - - - - -
    │╱              ╲
    └────────────────────────────────
     W1  W2  W3  W4  │ Forecast Zone
                     │
```

### 2. Seat Demand Prediction

```php
public function predictSeatDemand($showtimeId)
{
    $showtime = Showtime::with('movie', 'room')->find($showtimeId);

    // Factors
    $moviePopularity = $this->getMoviePopularityScore($showtime->movie_id);
    $dayOfWeek = $showtime->show_date->dayOfWeek; // 0-6
    $timeSlot = $this->getTimeSlot($showtime->start_time);
    $historicalOccupancy = $this->getHistoricalOccupancy(
        $showtime->movie_id,
        $dayOfWeek,
        $timeSlot
    );

    // Simple weighted prediction
    $prediction = (
        $moviePopularity * 0.4 +
        $this->getDayWeight($dayOfWeek) * 0.3 +
        $this->getTimeWeight($timeSlot) * 0.2 +
        $historicalOccupancy * 0.1
    ) / 100 * $showtime->room->capacity;

    return [
        'predicted_tickets' => round($prediction),
        'predicted_occupancy' => round($prediction / $showtime->room->capacity * 100),
        'confidence' => $this->calculateConfidence($historicalOccupancy),
    ];
}
```

---

## PHẦN D: QUẢN LÝ CHI PHÍ VẬN HÀNH

### 1. Cost Categories

```
┌─────────────────────────────────────────────────────────────┐
│ PHÂN LOẠI CHI PHÍ                                          │
├─────────────────────────────────────────────────────────────┤
│ CHI PHÍ CỐ ĐỊNH (Fixed Costs)                              │
│   • Thuê mặt bằng                                          │
│   • Lương cơ bản                                           │
│   • Bảo hiểm                                               │
│   • Khấu hao thiết bị                                      │
│                                                             │
│ CHI PHÍ BIẾN ĐỔI (Variable Costs)                          │
│   • Bản quyền phim (% doanh thu)                           │
│   • Điện (theo số suất chiếu)                              │
│   • Nhân viên part-time (theo ca)                          │
│   • Vật tư tiêu hao                                        │
└─────────────────────────────────────────────────────────────┘
```

### 2. Cost Per Screening

```php
public function getCostPerScreening($roomId)
{
    $room = Room::find($roomId);

    // Chi phí cố định chia cho số suất/tháng
    $fixedCostPerScreening = [
        'rent' => 50000000 / 30 / 4, // 4 phòng
        'depreciation' => $room->equipment_value / 60 / 30, // 5 năm, 30 ngày
    ];

    // Chi phí biến đổi per suất
    $variableCostPerScreening = [
        'electricity' => 150000, // Ước tính
        'staff' => 200000, // 2 người × 100k/ca
        'cleaning' => 50000,
    ];

    return [
        'fixed' => array_sum($fixedCostPerScreening),
        'variable' => array_sum($variableCostPerScreening),
        'total' => array_sum($fixedCostPerScreening) + array_sum($variableCostPerScreening),
    ];
}
```

### 3. Break-even Analysis

```
Break-even Point = Chi phí cố định / (Giá vé - Chi phí biến đổi per vé)

Ví dụ:
- Chi phí cố định/tháng: 200,000,000
- Giá vé trung bình: 100,000
- Chi phí biến đổi/vé: 55,000 (bản quyền 55%)

Break-even = 200,000,000 / (100,000 - 55,000)
           = 200,000,000 / 45,000
           = 4,444 vé/tháng

→ Cần bán ít nhất 4,444 vé/tháng để hòa vốn
→ ~148 vé/ngày
→ ~37 vé/suất (nếu 4 suất/ngày)
```

---

## PHẦN E: ROI ANALYSIS

### 1. ROI Per Movie

```php
public function getMovieROI($movieId)
{
    $movie = Movie::find($movieId);

    // Doanh thu từ phim
    $revenue = DB::table('bookings')
        ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
        ->where('showtimes.movie_id', $movieId)
        ->where('bookings.status', 'confirmed')
        ->sum('bookings.total_price');

    // Chi phí bản quyền (giả sử 55%)
    $licenseCost = $revenue * 0.55;

    // Chi phí marketing
    $marketingCost = $movie->marketing_budget ?? 0;

    // Chi phí suất chiếu
    $screeningCount = Showtime::where('movie_id', $movieId)->count();
    $screeningCost = $screeningCount * 400000; // 400k/suất

    $totalCost = $licenseCost + $marketingCost + $screeningCost;
    $profit = $revenue - $totalCost;
    $roi = $totalCost > 0 ? ($profit / $totalCost) * 100 : 0;

    return [
        'revenue' => $revenue,
        'cost' => $totalCost,
        'profit' => $profit,
        'roi' => round($roi, 1),
        'screening_count' => $screeningCount,
        'revenue_per_screening' => $screeningCount > 0 ? $revenue / $screeningCount : 0,
    ];
}
```

### 2. ROI Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ ROI THEO PHIM - THÁNG 01/2024                              │
├──────────────┬──────────┬──────────┬──────────┬────────────┤
│ Phim         │ Doanh thu │ Chi phí  │ Lợi nhuận │ ROI       │
├──────────────┼──────────┼──────────┼──────────┼────────────┤
│ Aquaman 2    │ 150M     │ 90M      │ 60M      │ 66.7% ▲   │
│ Wonka        │ 120M     │ 75M      │ 45M      │ 60.0% ▲   │
│ Migration    │ 80M      │ 55M      │ 25M      │ 45.5% →   │
│ Phim Local   │ 30M      │ 25M      │ 5M       │ 20.0% ▼   │
├──────────────┴──────────┴──────────┴──────────┴────────────┤
│ Khuyến nghị: Giảm suất chiếu "Phim Local", tăng Aquaman 2  │
└─────────────────────────────────────────────────────────────┘
```

### 3. Room Profitability

```php
public function getRoomProfitability($roomId, $period)
{
    // Doanh thu từ phòng
    $revenue = DB::table('bookings')
        ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
        ->where('showtimes.room_id', $roomId)
        ->where('bookings.status', 'confirmed')
        ->whereBetween('bookings.created_at', $period)
        ->sum('bookings.total_price');

    // Chi phí vận hành phòng
    $screeningCount = Showtime::where('room_id', $roomId)
        ->whereBetween('show_date', $period)
        ->count();

    $operatingCost = $screeningCount * $this->getCostPerScreening($roomId)['total'];

    // Chi phí bản quyền (55% doanh thu)
    $licenseCost = $revenue * 0.55;

    return [
        'revenue' => $revenue,
        'operating_cost' => $operatingCost,
        'license_cost' => $licenseCost,
        'profit' => $revenue - $operatingCost - $licenseCost,
        'screenings' => $screeningCount,
        'profit_per_screening' => $screeningCount > 0
            ? ($revenue - $operatingCost - $licenseCost) / $screeningCount
            : 0,
    ];
}
```

---

## PHẦN F: CUSTOMER INSIGHTS

### 1. Customer Segmentation

```php
public function segmentCustomers()
{
    $users = User::withCount(['bookings' => function ($q) {
            $q->where('status', 'confirmed')
              ->where('created_at', '>=', now()->subMonths(3));
        }])
        ->withSum(['bookings' => function ($q) {
            $q->where('status', 'confirmed')
              ->where('created_at', '>=', now()->subMonths(3));
        }], 'total_price')
        ->get();

    $segments = [
        'vip' => [],       // > 10 lần/quý, > 3M
        'regular' => [],   // 4-10 lần/quý
        'occasional' => [], // 1-3 lần/quý
        'dormant' => [],   // 0 lần/quý nhưng có history
        'new' => [],       // Mới đăng ký
    ];

    foreach ($users as $user) {
        if ($user->bookings_count > 10 && $user->bookings_sum_total_price > 3000000) {
            $segments['vip'][] = $user;
        } elseif ($user->bookings_count >= 4) {
            $segments['regular'][] = $user;
        } elseif ($user->bookings_count >= 1) {
            $segments['occasional'][] = $user;
        } elseif ($user->created_at >= now()->subMonth()) {
            $segments['new'][] = $user;
        } else {
            $segments['dormant'][] = $user;
        }
    }

    return $segments;
}
```

### 2. Customer Lifetime Value (CLV)

```php
public function calculateCLV($userId)
{
    $user = User::find($userId);

    // Giá trị trung bình mỗi lần mua
    $avgOrderValue = $user->bookings()
        ->where('status', 'confirmed')
        ->avg('total_price');

    // Tần suất mua (lần/tháng)
    $firstBooking = $user->bookings()->min('created_at');
    $monthsActive = now()->diffInMonths($firstBooking) ?: 1;
    $totalBookings = $user->bookings()->where('status', 'confirmed')->count();
    $purchaseFrequency = $totalBookings / $monthsActive;

    // Ước tính thời gian customer lifetime (giả sử 24 tháng)
    $customerLifetime = 24;

    // CLV = AOV × Frequency × Lifetime
    $clv = $avgOrderValue * $purchaseFrequency * $customerLifetime;

    return [
        'avg_order_value' => $avgOrderValue,
        'purchase_frequency' => round($purchaseFrequency, 2),
        'estimated_clv' => round($clv),
    ];
}
```

### 3. Churn Prediction

```
┌─────────────────────────────────────────────────────────────┐
│ CẢNH BÁO KHÁCH HÀNG CÓ NGUY CƠ RỜI BỎ                      │
├─────────────────────────────────────────────────────────────┤
│ Tiêu chí: Không mua vé > 60 ngày (từng mua ≥ 3 lần)        │
├──────────────┬──────────┬──────────┬────────────────────────┤
│ Khách hàng   │ Lần cuối │ Tổng mua │ Hành động đề xuất     │
├──────────────┼──────────┼──────────┼────────────────────────┤
│ Nguyễn A     │ 65 ngày  │ 12 lần   │ 🔥 Gửi voucher VIP    │
│ Trần B       │ 72 ngày  │ 8 lần    │ 📧 Email phim mới     │
│ Lê C         │ 90 ngày  │ 5 lần    │ 📱 SMS khuyến mãi     │
└──────────────┴──────────┴──────────┴────────────────────────┘
```

---

## PHẦN G: DYNAMIC PRICING STRATEGY

### 1. Time-based Pricing

```php
public function calculateDynamicPrice($basePrice, $showtime)
{
    $multiplier = 1.0;

    // Ngày trong tuần
    $dayOfWeek = $showtime->show_date->dayOfWeek;
    if (in_array($dayOfWeek, [0, 6])) { // Weekend
        $multiplier += 0.2; // +20%
    } elseif ($dayOfWeek == 3) { // Wednesday - Happy Day
        $multiplier -= 0.15; // -15%
    }

    // Khung giờ
    $hour = (int) $showtime->start_time->format('H');
    if ($hour >= 18 && $hour <= 21) { // Prime time
        $multiplier += 0.15;
    } elseif ($hour < 12) { // Morning
        $multiplier -= 0.1;
    }

    // Ngày lễ
    if ($this->isHoliday($showtime->show_date)) {
        $multiplier += 0.25;
    }

    return round($basePrice * $multiplier, -3); // Làm tròn nghìn
}
```

### 2. Demand-based Pricing

```php
public function adjustPriceByDemand($showtime)
{
    $currentOccupancy = $this->getCurrentOccupancy($showtime->id);
    $hoursUntilShow = now()->diffInHours($showtime->show_datetime);

    // Nếu sắp chiếu và còn nhiều ghế → giảm giá
    if ($hoursUntilShow <= 3 && $currentOccupancy < 30) {
        return -0.2; // -20%
    }

    // Nếu gần full → tăng giá
    if ($currentOccupancy > 80) {
        return 0.1; // +10%
    }

    return 0;
}
```

### 3. Pricing Matrix

```
┌─────────────────────────────────────────────────────────────┐
│ MA TRẬN GIÁ VÉ                                             │
├───────────┬──────────┬──────────┬──────────┬───────────────┤
│           │ Sáng     │ Chiều    │ Tối      │ Tối muộn      │
│           │ 9-12h    │ 12-18h   │ 18-21h   │ 21h+          │
├───────────┼──────────┼──────────┼──────────┼───────────────┤
│ T2-T4     │ 65,000   │ 80,000   │ 95,000   │ 75,000        │
│ T5        │ 70,000   │ 85,000   │ 100,000  │ 80,000        │
│ T6        │ 75,000   │ 90,000   │ 110,000  │ 85,000        │
│ T7-CN     │ 85,000   │ 100,000  │ 120,000  │ 90,000        │
│ Lễ        │ 95,000   │ 110,000  │ 130,000  │ 100,000       │
└───────────┴──────────┴──────────┴──────────┴───────────────┘

│ * Giá trên là giá ghế Standard. VIP: +50%, Couple: +100%   │
```

---

## PHẦN H: HỆ THỐNG CẢNH BÁO TỰ ĐỘNG

### 1. Alert Types

```php
class AlertService
{
    public function checkAlerts()
    {
        $alerts = [];

        // Revenue alert
        $todayRevenue = $this->getTodayRevenue();
        $avgDailyRevenue = $this->getAvgDailyRevenue(30);
        if ($todayRevenue < $avgDailyRevenue * 0.5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Doanh thu thấp bất thường',
                'message' => "Doanh thu hôm nay thấp hơn 50% so với trung bình",
                'value' => $todayRevenue,
                'threshold' => $avgDailyRevenue * 0.5,
            ];
        }

        // Low occupancy alert
        $lowOccupancyShowtimes = $this->getLowOccupancyShowtimes(20);
        if ($lowOccupancyShowtimes->count() > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Suất chiếu vắng khách',
                'message' => $lowOccupancyShowtimes->count() . " suất chiếu có tỷ lệ đặt < 20%",
                'data' => $lowOccupancyShowtimes,
            ];
        }

        // Inventory alert (snacks, drinks)
        $lowStock = $this->getLowStockItems(10);
        if ($lowStock->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Hàng hóa sắp hết',
                'message' => $lowStock->count() . " sản phẩm cần nhập thêm",
                'data' => $lowStock,
            ];
        }

        return $alerts;
    }
}
```

### 2. Alert Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ 🔔 CẢNH BÁO HỆ THỐNG                                       │
├─────────────────────────────────────────────────────────────┤
│ ⚠️  CẢNH BÁO: Doanh thu hôm nay thấp hơn 50% so với TB     │
│     Hiện tại: 25,000,000 VND | Trung bình: 60,000,000 VND  │
│     [Xem chi tiết] [Tạo promotion]                         │
├─────────────────────────────────────────────────────────────┤
│ ℹ️  THÔNG TIN: 3 suất chiếu có tỷ lệ đặt < 20%             │
│     • 14:00 - Phim X - Room 2 (5/80 ghế)                   │
│     • 15:30 - Phim Y - Room 3 (12/150 ghế)                 │
│     • 16:00 - Phim Z - Room 1 (8/120 ghế)                  │
│     [Gửi notification] [Điều chỉnh giá]                    │
├─────────────────────────────────────────────────────────────┤
│ ⚠️  CẢNH BÁO: Popcorn Large sắp hết hàng                   │
│     Còn lại: 8 phần | Ngưỡng cảnh báo: 10 phần            │
│     [Đặt hàng] [Tạm ngừng bán]                             │
└─────────────────────────────────────────────────────────────┘
```

### 3. Scheduled Reports

```php
// Kernel.php
protected function schedule(Schedule $schedule)
{
    // Daily report - 23:59
    $schedule->job(new SendDailyReport)->dailyAt('23:59');

    // Weekly summary - Sunday 20:00
    $schedule->job(new SendWeeklySummary)->weeklyOn(0, '20:00');

    // Monthly P&L - 1st of month 09:00
    $schedule->job(new SendMonthlyPnL)->monthlyOn(1, '09:00');

    // Real-time alerts - every 15 minutes
    $schedule->job(new CheckAlerts)->everyFifteenMinutes();
}
```

---

## PHẦN I: IMPLEMENTATION PRIORITY

### Độ ưu tiên cao (Làm ngay)

1. **KPI Dashboard với so sánh**
   - Effort: Low
   - Impact: High
   - Thời gian: 2-3 ngày

2. **Daily Revenue Report**
   - Effort: Low
   - Impact: High
   - Thời gian: 1-2 ngày

3. **Occupancy Rate Tracking**
   - Effort: Medium
   - Impact: High
   - Thời gian: 3-4 ngày

### Độ ưu tiên trung bình (Sprint 2)

4. **Customer Segmentation**
   - Effort: Medium
   - Impact: Medium
   - Thời gian: 1 tuần

5. **Basic Alert System**
   - Effort: Medium
   - Impact: High
   - Thời gian: 1 tuần

6. **ROI Analysis**
   - Effort: Medium
   - Impact: Medium
   - Thời gian: 1 tuần

### Độ ưu tiên thấp (Future)

7. **Forecasting**
   - Effort: High
   - Impact: Medium
   - Cần data lịch sử nhiều

8. **Dynamic Pricing**
   - Effort: High
   - Impact: High
   - Cần test kỹ trước khi deploy

9. **CLV & Churn Prediction**
   - Effort: High
   - Impact: Medium
   - Cần machine learning

---

## TÓM TẮT

### Giá trị của Business Intelligence cho rạp phim

```
1. VISIBILITY
   Biết chính xác đang lời hay lỗ, ở đâu, bao nhiêu

2. INSIGHT
   Hiểu tại sao lời/lỗ, pattern nào lặp lại

3. ACTION
   Biết phải làm gì: tăng suất, giảm chi phí, target đúng khách

4. PREDICTION
   Dự đoán được tương lai để chuẩn bị trước
```

### Công thức thành công

```
Profit = Revenue - Cost

Tăng Revenue:
- Dynamic pricing
- Upsell (combo, đồ ăn)
- Customer retention

Giảm Cost:
- Optimize occupancy
- Reduce waste
- Automate operations
```

---

**Kết thúc series Design Thinking**

Quay lại: [Phần 1: Tại Sao & Động Cơ](./DESIGN_THINKING_PART1_WHY.md) | [Phần 2: Tối Ưu & Phát Triển](./DESIGN_THINKING_PART2_OPTIMIZATION.md)

