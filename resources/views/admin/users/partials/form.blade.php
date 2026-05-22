<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            <option value="">Pilih Role</option>
            <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin</option>
            <option value="kasir" @selected(old('role', $user->role ?? '') === 'kasir')>Kasir</option>
            <option value="pelayan" @selected(old('role', $user->role ?? '') === 'pelayan')>Pelayan</option>
            <option value="dapur" @selected(old('role', $user->role ?? '') === 'dapur')>Dapur</option>
            <option value="customer" @selected(old('role', $user->role ?? '') === 'customer')>Customer</option>
        </select>
        @error('role')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Password {{ isset($user) ? '(Opsional)' : '' }}</label>
        <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
        @error('password')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>
<hr class="my-4">
