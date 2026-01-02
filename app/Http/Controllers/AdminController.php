<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // 1. Kiểm tra quyền admin (middleware)
    public function __construct()
    {
        // Bạn sẽ thêm middleware kiểm tra user là admin
    }

    // 2. Dashboard - Trang chủ admin
    public function index()
    {
        // Lấy thống kê từ database
        $totalMovies = DB::table('movies')->count();
        $totalUsers = DB::table('users')->count();
        $totalBookings = DB::table('bookings')->count();
        $totalRevenue = DB::table('bookings')
            ->where('status', 'confirmed')
            ->sum('total_price');
        // lấy 10 vé đặt gần nhất
        $recentBookings = DB::table('bookings')
            ->join('users', 'bookings.user_id', '=', 'users.id')
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->select(
                'bookings.id',
                'bookings.total_price',
                'bookings.status',
                'bookings.created_at',
                'users.name as user_name',
                'movies.title as movie_title',
                'showtimes.show_date',
                'showtimes.start_time'
            )
            ->orderBy('bookings.created_at', 'desc')
            ->limit(10)
            ->get();
        return view('admin.dashboard', [
            'totalMovies' => $totalMovies,
            'totalUsers' => $totalUsers,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'recentBookings' => $recentBookings
        ]);
    }

    // 3. Quản lý phim
    public function moviesList()
    {
        // Lấy danh sách phim từ DB
        // return view với dữ liệu
    }

    public function movieCreate()
    {
        // Hiển thị form thêm phim mới
    }

    public function movieStore(Request $request)
    {
        // Xử lý lưu phim vào DB
    }

    public function movieEdit($id)
    {
        // Hiển thị form sửa phim
    }

    public function movieUpdate(Request $request, $id)
    {
        // Xử lý cập nhật phim
    }

    public function movieDelete($id)
    {
        // Xóa phim
    }

    // 4. Quản lý người dùng
    public function usersList()
    {
        // Danh sách người dùng
    }

    // 5. Quản lý đặt vé
    public function bookingsList()
    {
        // Danh sách đặt vé
    }
}