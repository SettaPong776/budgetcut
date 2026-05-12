@extends('layouts.app')

@section('topbar-title', 'จัดการผู้ขอกัน')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 animate-in">
        <h1 class="page-hero-title">จัดการรายชื่อผู้ขอกัน</h1>
        <p class="page-hero-subtitle">เพิ่มชื่อผู้ขอกันที่ใช้ประจำ เพื่อให้ระบบแนะนำชื่ออัตโนมัติตอนกรอกฟอร์ม</p>
    </div>
</div>

<div class="row justify-content-center g-3">
    <!-- Add Form -->
    <div class="col-lg-4 animate-in">
        <div class="apple-card h-100">
            <div class="apple-card-header">
                <span>เพิ่มชื่อผู้ขอกัน</span>
            </div>
            <div class="apple-card-body apple-form">
                <form method="POST" action="{{ route('requesters.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">ชื่อ-นามสกุล <span style="color:var(--apple-red);">*</span></label>
                        <input type="text" class="form-control" name="name" required placeholder="เช่น นายสมชาย ใจดี" value="{{ old('name') }}">
                        @error('name')
                        <div style="color: var(--apple-red); font-size: 0.8rem; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn-apple btn-apple-primary w-100 justify-content-center" style="padding: 10px;">
                        <i class="fas fa-plus"></i> เพิ่มรายชื่อ
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Requester List -->
    <div class="col-lg-4 animate-in">
        <div class="apple-card h-100">
            <div class="apple-card-header">
                <span>รายชื่อทั้งหมด</span>
                <span class="apple-badge apple-badge-gray">{{ $requesters->count() }} คน</span>
            </div>
            <div style="padding: 0; max-height: 500px; overflow-y: auto;">
                @if($requesters->count() > 0)
                <table class="apple-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">ลำดับ</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th class="text-center" style="width: 60px;">ลบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requesters as $req)
                        <tr>
                            <td class="text-center" style="font-weight: 500; color: var(--apple-gray-4);">{{ $loop->iteration }}</td>
                            <td style="font-weight: 600;">{{ $req->name }}</td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('requesters.destroy', $req->id) }}" onsubmit="confirmSubmit(event, this, 'ลบรายชื่อนี้?', 'ชื่อ {{ $req->name }} จะถูกลบออกจากรายการแนะนำ', 'var(--apple-red)', 'ลบ', 'warning');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--apple-red); padding: 4px 8px; font-size: 0.85rem;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center" style="padding: 40px; color: var(--apple-gray-4);">
                    <i class="fas fa-users" style="font-size: 2rem; display: block; margin-bottom: 12px; opacity: 0.2;"></i>
                    ยังไม่มีรายชื่อผู้ขอกัน
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
