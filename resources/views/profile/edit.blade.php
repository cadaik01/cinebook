@extends('profile.profilepage')

@section('title','Edit Your Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Avatar Upload Section --}}
                        <div class="mb-4 text-center">
                            <div class="mb-3">
                                <img id="avatar-preview" 
                                     src="{{ $user->avatar_url ? asset('storage/' . $user->avatar_url) : asset('images/default-avatar.png') }}" 
                                     alt="Avatar" 
                                     class="rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--deep-teal);">
                            </div>
                            <div>
                                <label for="avatar" class="btn btn-primary btn-sm">
                                    <i class="fas fa-camera"></i> Change Avatar
                                </label>
                                <input type="file" 
                                       class="d-none" 
                                       id="avatar" 
                                       name="avatar" 
                                       accept="image/*"
                                       onchange="previewAvatar(event)">
                                <small class="d-block text-muted mt-2">Max size: 2MB (JPG, PNG)</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <select name="city" id="city" class="form-select">
                                <option value="">-- Select City --</option>
                                <option value="tphcm" {{ old('city', $user->city) == 'tphcm' ? 'selected' : '' }}>Hồ Chí Minh</option>
                                <option value="hanoi" {{ old('city', $user->city) == 'hanoi' ? 'selected' : '' }}>Hà Nội</option>
                                <option value="danang" {{ old('city', $user->city) == 'danang' ? 'selected' : '' }}>Đà Nẵng</option>
                            </select>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('user.profile') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Avatar Preview --}}
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            event.target.value = '';
            return;
        }
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection