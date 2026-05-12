@extends('layouts.app')

@section('topbar-title', 'การเบิกจ่าย')

@section('content')
<div class="animate-in">
    <h1 class="page-hero-title">ประวัติการเบิกจ่าย</h1>
    <p class="page-hero-subtitle">รายการตัดยอดงบประมาณทั้งหมดที่เกิดขึ้นในระบบ</p>
</div>

<div class="apple-card animate-in" style="margin-bottom: 16px;">
    <div class="apple-card-body" style="padding: 16px 20px;">
        <form method="GET" action="{{ route('transactions.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--apple-gray-4); margin-bottom: 4px;">ค้นหาข้อความ</label>
                    <div class="position-relative">
                        <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--apple-gray-4); font-size: 0.85rem;"></i>
                        <input type="text" name="search" class="form-control" value="{{ $search ?? '' }}" placeholder="ค้นหา โครงการ, เลขเบิกจ่าย..." style="padding-left: 38px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.08); font-size: 0.9rem;">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--apple-gray-4); margin-bottom: 4px;">จากวันที่</label>
                    <input type="text" name="start_date" class="form-control datepicker" value="{{ $startDate ?? '' }}" placeholder="วว/ดด/ปปปป" style="border-radius: 8px; font-size: 0.9rem;">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--apple-gray-4); margin-bottom: 4px;">ถึงวันที่</label>
                    <input type="text" name="end_date" class="form-control datepicker" value="{{ $endDate ?? '' }}" placeholder="วว/ดด/ปปปป" style="border-radius: 8px; font-size: 0.9rem;">
                </div>
                <div class="col-md-2">
                    <label class="form-label" style="font-size: 0.8rem; color: var(--apple-gray-4); margin-bottom: 4px;">สถานะ</label>
                    <select name="status" class="form-select" style="border-radius: 8px; font-size: 0.9rem; border: 1px solid rgba(0,0,0,0.08);">
                        <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>ทั้งหมด</option>
                        <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>สำเร็จ</option>
                        <option value="cancelled" {{ ($status ?? '') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn-apple btn-apple-primary w-100" style="padding: 8px 12px; font-size: 0.9rem;">
                        <i class="fas fa-search"></i> ค้นหา
                    </button>
                    @if($search || $startDate || $endDate || ($status && $status != 'all'))
                    <a href="{{ route('transactions.index') }}" class="btn-apple btn-apple-secondary" style="padding: 8px 12px; font-size: 0.9rem;" title="ล้างตัวกรอง">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
            
            <div class="mt-3 pt-3 d-flex justify-content-between align-items-center" style="border-top: 1px solid rgba(0,0,0,0.05);">
                <div style="font-size: 0.85rem; color: var(--apple-gray-4);">
                    พบข้อมูลทั้งหมด <strong>{{ $transactions->count() }}</strong> รายการ
                </div>
                <a href="{{ route('transactions.export', ['search' => $search, 'start_date' => $startDate, 'end_date' => $endDate, 'status' => $status]) }}" class="btn-apple btn-apple-secondary btn-apple-sm text-success" style="background: rgba(48,209,88,0.1); border-color: transparent;">
                    <i class="fas fa-file-excel me-1"></i> Export Excel (CSV)
                </a>
            </div>
        </form>
    </div>
</div>

<div class="apple-card animate-in">
    <div style="padding: 0;">
        <table class="apple-table">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">ลำดับ</th>
                    <th>วันที่</th>
                    <th>โครงการ</th>
                    <th>เลขเบิกจ่าย</th>
                    <th>รายการ/ผู้เบิก</th>
                    <th class="text-end">ยอดเงิน</th>
                    <th class="text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td class="text-center" style="font-weight: 500; color: var(--apple-gray-4);">{{ $loop->iteration }}</td>
                    <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($tx->transaction_date)->toThaiShortDate() }}</td>
                    <td>
                        <a href="{{ route('projects.show', $tx->project->id) }}" style="text-decoration: none; color: var(--apple-blue); font-weight: 600;">
                            {{ Str::limit($tx->project->project_name, 30) }}
                        </a>
                        <div style="font-size: 0.75rem; color: var(--apple-gray-4); margin-top: 1px;">{{ $tx->project->project_no }}</div>
                    </td>
                    <td><span class="apple-badge apple-badge-gray">{{ $tx->doc_ref }}</span></td>
                    <td style="color: var(--apple-gray-3);">
                        <div style="color: var(--apple-gray-1);">{{ Str::limit($tx->description, 40) }}</div>
                        @if($tx->requester)
                        <div style="font-size: 0.75rem; margin-top: 2px;"><i class="fas fa-user-tie" style="font-size: 0.8em; margin-right: 3px;"></i> {{ $tx->requester }}</div>
                        @endif
                    </td>
                    <td class="text-end" style="font-weight: 600; color: var(--apple-red);">-{{ number_format($tx->amount, 2) }}</td>
                    <td class="text-center">
                        @if($tx->status == 'approved')
                            <span class="apple-badge apple-badge-green"><i class="fas fa-check" style="font-size:0.6em; margin-right: 3px;"></i> สำเร็จ</span>
                        @elseif($tx->status == 'cancelled')
                            <span class="apple-badge apple-badge-red"><i class="fas fa-times" style="font-size:0.6em; margin-right: 3px;"></i> ยกเลิก</span>
                        @else
                            <span class="apple-badge apple-badge-blue">รอดำเนินการ</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 80px; color: var(--apple-gray-4);">
                        <i class="fas fa-inbox" style="font-size: 2.5rem; display: block; margin-bottom: 14px; opacity: 0.15;"></i>
                        ยังไม่มีรายการเบิกจ่ายในระบบ
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
