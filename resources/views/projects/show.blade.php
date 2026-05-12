@extends('layouts.app')

@section('topbar-title', $project->project_no)

@section('content')
<!-- Ledger Hero — like a dark Apple card -->
<div class="ledger-hero mb-4 animate-in">
    <div class="row align-items-center">
        <div class="col-md-7">
            <div style="font-size: 0.78rem; font-weight: 500; opacity: 0.5; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">
                {{ $project->budget_type }}
            </div>
            <h2 style="font-weight: 700; font-size: 1.6rem; margin-bottom: 4px;">
                {{ $project->project_name }}
                @if($project->status == 'closed')
                    <span class="apple-badge apple-badge-red ms-2" style="font-size: 0.8rem; vertical-align: middle;">ปิดบัญชีแล้ว</span>
                @else
                    <span class="apple-badge apple-badge-green ms-2" style="font-size: 0.8rem; vertical-align: middle;">กำลังดำเนินการ</span>
                @endif
            </h2>
            <div style="font-size: 0.85rem; opacity: 0.5;">
                @if($project->responsible_person) <i class="fas fa-user-tie me-1"></i> {{ $project->responsible_person }} · @endif
                @if($project->start_date) เริ่มโครงการ: {{ \Carbon\Carbon::parse($project->start_date)->toThaiShortDate() }} @endif
            </div>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="balance-label">ยอดคงเหลือ</div>
            <div class="balance-value" style="color: #30d158;">
                {{ number_format($remainingBudget, 2) }}
                <span class="balance-currency">฿</span>
            </div>
        </div>
    </div>
</div>

<!-- Mini Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-6 animate-in">
        <div class="apple-card" style="padding: 20px 24px;">
            <div style="font-size: 0.78rem; font-weight: 600; color: var(--apple-gray-4); text-transform: uppercase; letter-spacing: 0.04em;">งบประมาณที่ได้รับจัดสรร</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--apple-blue); margin-top: 4px;">{{ number_format($project->total_budget, 2) }} <small style="font-size:0.6em; opacity:0.6;">฿</small></div>
        </div>
    </div>
    <div class="col-md-6 animate-in">
        <div class="apple-card" style="padding: 20px 24px;">
            <div style="font-size: 0.78rem; font-weight: 600; color: var(--apple-gray-4); text-transform: uppercase; letter-spacing: 0.04em;">เบิกจ่ายไปแล้วทั้งหมด</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: var(--apple-red); margin-top: 4px;">{{ number_format($usedBudget, 2) }} <small style="font-size:0.6em; opacity:0.6;">฿</small></div>
        </div>
    </div>
</div>

<!-- Statement Table — Full Width -->
<div class="animate-in">
    <div class="apple-card">
        <div class="apple-card-header">
            <div>
                <span>ประวัติรายการ (Statement)</span>
                <span class="apple-badge apple-badge-gray" style="margin-left: 8px;">{{ $transactions->count() }} รายการ</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($project->status == 'active')
                    <form action="{{ route('projects.close', $project->id) }}" method="POST" onsubmit="confirmSubmit(event, this, 'ยืนยันการปิดบัญชีโครงการนี้?', 'หากปิดแล้วจะไม่สามารถเบิกจ่ายเพิ่มได้อีก', 'var(--apple-red)', 'ปิดบัญชี', 'warning');" class="mb-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-apple btn-apple-secondary btn-apple-sm text-danger" style="background: rgba(255,59,48,0.1); border-color: transparent;">
                            <i class="fas fa-lock me-1"></i> ปิดบัญชีโครงการ
                        </button>
                    </form>
                @else
                    <form action="{{ route('projects.reopen', $project->id) }}" method="POST" onsubmit="confirmSubmit(event, this, 'ยืนยันการเปิดบัญชีโครงการนี้?', 'ระบบจะกลับมาเปิดให้ทำรายการตัดยอดได้ตามปกติ', 'var(--apple-blue)', 'เปิดบัญชี', 'info');" class="mb-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-apple btn-apple-secondary btn-apple-sm text-success" style="background: rgba(48,209,88,0.1); border-color: transparent;">
                            <i class="fas fa-lock-open me-1"></i> เปิดบัญชีอีกครั้ง
                        </button>
                    </form>
                @endif
                <a href="{{ route('projects.report', $project->id) }}" target="_blank" class="btn-apple btn-apple-secondary btn-apple-sm">
                    <i class="fas fa-print"></i> พิมพ์ Report
                </a>
            </div>
        </div>
        <div style="padding: 0; min-height: 250px; max-height: 600px; overflow-y: auto;">
            <table class="apple-table">
                <thead>
                    <tr>
                        <th style="width: 50px;" class="text-center">ลำดับ</th>
                        <th>วันที่</th>
                        <th>เลขเบิกจ่าย</th>
                        <th>รายการ/ผู้เบิก</th>
                        <th class="text-end">ยอดเงิน</th>
                        <th class="text-center" style="width: 80px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td class="text-center" style="font-weight: 500; color: var(--apple-gray-4);">{{ $loop->iteration }}</td>
                        <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($tx->transaction_date)->toThaiShortDate() }}</td>
                        <td><span class="apple-badge apple-badge-gray">{{ $tx->doc_ref }}</span></td>
                        <td>
                            <div>{{ $tx->description }}</div>
                            @if($tx->requester)
                            <div style="font-size: 0.75rem; color: var(--apple-gray-4); margin-top: 2px;"><i class="fas fa-user-tie" style="font-size: 0.8em; margin-right: 3px;"></i> {{ $tx->requester }}</div>
                            @endif
                        </td>
                        <td class="text-end" style="font-weight: 600; {{ $tx->status == 'cancelled' ? 'color: var(--apple-gray-4); text-decoration: line-through;' : 'color: var(--apple-red);' }}">
                            -{{ number_format($tx->amount, 2) }}
                            @if($tx->status == 'cancelled')
                            <div style="font-size: 0.7rem; color: var(--apple-red); text-decoration: none; margin-top: 2px;">(ยกเลิกแล้ว)</div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($tx->status != 'cancelled')
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border:none; background:transparent;">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: var(--radius-md); border: 1px solid rgba(0,0,0,0.08);">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('transactions.edit', $tx->id) }}" style="font-size: 0.85rem; font-weight: 500;">
                                            <i class="fas fa-edit text-primary me-2"></i> แก้ไขรายการ
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('transactions.destroy', $tx->id) }}" method="POST" onsubmit="confirmDelete(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 text-danger" style="font-size: 0.85rem; font-weight: 500;">
                                                <i class="fas fa-ban me-2"></i> ยกเลิกรายการ
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 60px; color: var(--apple-gray-4);">
                            <i class="fas fa-receipt" style="font-size: 2rem; display: block; margin-bottom: 12px; opacity: 0.2;"></i>
                            ยังไม่มีรายการตัดยอด
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
