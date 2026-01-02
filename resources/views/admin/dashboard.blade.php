@extends('admin.layouts.admin')

@section('title', 'Dashboard - Cinebook')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid py-3">
    <div class="alert alert-info mb-3">
        <i class="fas fa-info-circle me-2"></i>
        Dashboard placeholder: add content here.
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-2">Statistics</h5>
                    <p class="text-muted mb-0">Thêm wiget thống kê vào sau.</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-2">Recent Activity</h5>
                    <p class="text-muted mb-0">Danh sách log/hoạt động sẽ hiển thị ở đây.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection