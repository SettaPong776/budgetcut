@extends('layouts.app')

@section('topbar-title', 'โครงการ')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3 animate-in">
    <div>
        <h1 class="page-hero-title">บัญชีโครงการ</h1>
        <p class="page-hero-subtitle">เลือกโครงการเพื่อดูสมุดบัญชีและทำรายการตัดยอดงบประมาณ</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn-apple btn-apple-danger" onclick="openQuickReserve()" id="btn-quick-reserve">
            <i class="fas fa-bolt"></i> กันเงินทันที
        </button>
        <a href="{{ route('projects.create') }}" class="btn-apple btn-apple-primary">
            <i class="fas fa-plus"></i> สร้างโครงการ
        </a>
    </div>
</div>

<div class="apple-card animate-in">
    <div style="padding: 0;">
        <table class="apple-table">
            <thead>
                <tr>
                    <th>เลขโครงการ</th>
                    <th>ชื่อโครงการ</th>
                    <th>ปีงบ</th>
                    <th class="text-end">งบตั้งต้น</th>
                    <th class="text-end">เบิกจ่ายแล้ว</th>
                    <th class="text-end">คงเหลือ</th>
                    <th class="text-center">ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                @php
                    $used = $project->used_budget ?? 0;
                    $remaining = $project->total_budget - $used;
                    $percent = $project->total_budget > 0 ? round(($used / $project->total_budget) * 100) : 0;
                @endphp
                <tr>
                    <td><span class="apple-badge apple-badge-gray">{{ $project->project_no }}</span></td>
                    <td>
                        <div style="font-weight: 600; color: var(--apple-gray-1);">
                            {{ $project->project_name }}
                            @if($project->status == 'closed')
                                <span class="apple-badge apple-badge-red ms-1" style="font-size: 0.65rem; padding: 2px 6px;">ปิดบัญชี</span>
                            @endif
                        </div>
                        <div style="font-size: 0.78rem; color: var(--apple-gray-4); margin-top: 2px;">{{ $project->budget_type }}</div>
                    </td>
                    <td>{{ $project->fiscal_year }}</td>
                    <td class="text-end" style="font-weight: 600; color: var(--apple-blue);">{{ number_format($project->total_budget, 2) }}</td>
                    <td class="text-end" style="color: var(--apple-red);">{{ number_format($used, 2) }}</td>
                    <td class="text-end" style="font-weight: 600; color: var(--apple-green);">{{ number_format($remaining, 2) }}</td>
                    <td class="text-center">
                        <a href="{{ route('projects.show', $project->id) }}" class="btn-apple btn-apple-outline btn-apple-sm">
                            {{ $project->status == 'closed' ? 'ดูบัญชี' : 'เปิดบัญชี' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 60px; color: var(--apple-gray-4);">
                        <i class="fas fa-folder-open" style="font-size: 2rem; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                        ยังไม่มีโครงการในระบบ
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ======================================
     QUICK RESERVE MODAL
====================================== -->
<div id="quick-reserve-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); z-index:9999; animation: qrFadeIn 0.25s ease;" onclick="if(event.target===this) closeQuickReserve();">
    <div id="quick-reserve-modal" style="
        position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
        background:var(--apple-white); border-radius:var(--radius-xl);
        width:480px; max-width:92vw; max-height:90vh; overflow-y:auto;
        box-shadow:0 24px 80px rgba(0,0,0,0.2), 0 0 0 1px rgba(0,0,0,0.06);
        animation: qrSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    ">
        <!-- Modal Header -->
        <div style="padding:22px 24px 16px; border-bottom:1px solid rgba(0,0,0,0.06); display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:linear-gradient(135deg, #ff3b30, #ff6b6b); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:0.9rem; box-shadow:0 4px 12px rgba(255,59,48,0.3);">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <div style="font-weight:700; font-size:1.05rem; color:var(--apple-gray-1);">กันเงินทันที</div>
                    <div style="font-size:0.75rem; color:var(--apple-gray-4);">พิมพ์เลขโครงการเพื่อกันเงินโดยไม่ต้องเปิดบัญชี</div>
                </div>
            </div>
            <button onclick="closeQuickReserve()" style="background:rgba(0,0,0,0.05); border:none; width:30px; height:30px; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--apple-gray-3); transition:all 0.15s ease;" onmouseenter="this.style.background='rgba(0,0,0,0.1)'" onmouseleave="this.style.background='rgba(0,0,0,0.05)'">
                <i class="fas fa-xmark" style="font-size:0.9rem;"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form method="POST" action="{{ route('transactions.quickStore') }}" id="quick-reserve-form">
            @csrf
            <div style="padding:20px 24px;" class="apple-form">

                <!-- Project No with Autocomplete -->
                <div class="mb-3" style="position:relative;">
                    <label class="form-label"><i class="fas fa-hashtag me-1" style="color:var(--apple-blue); font-size:0.75rem;"></i> เลขโครงการ <span style="color:var(--apple-red);">*</span></label>
                    <input type="text" class="form-control" name="project_no" id="qr-project-no" required autocomplete="off" placeholder="พิมพ์เลขโครงการ..." value="{{ old('project_no') }}">
                    <div id="qr-project-dropdown" style="display:none; position:absolute; top:100%; left:0; right:0; z-index:999; background:var(--apple-white); border:1px solid var(--apple-gray-5); border-radius:10px; margin-top:4px; max-height:220px; overflow-y:auto; box-shadow:var(--shadow-lg);"></div>
                    <div id="qr-project-info" style="display:none; margin-top:8px; padding:10px 14px; background:rgba(0,113,227,0.05); border:1px solid rgba(0,113,227,0.12); border-radius:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <div style="font-size:0.78rem; color:var(--apple-gray-4);">ชื่อโครงการ</div>
                                <div id="qr-project-name" style="font-weight:600; font-size:0.9rem; color:var(--apple-gray-1);"></div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:0.78rem; color:var(--apple-gray-4);">คงเหลือ</div>
                                <div id="qr-project-remaining" style="font-weight:700; font-size:0.95rem; color:var(--apple-green);"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requester -->
                <div class="mb-3" style="position:relative;">
                    <label class="form-label"><i class="fas fa-user-tie me-1" style="color:var(--apple-purple); font-size:0.75rem;"></i> ผู้ขอกัน <span style="color:var(--apple-red);">*</span></label>
                    <input type="text" class="form-control" name="requester" required placeholder="ชื่อผู้กัน" value="{{ old('requester') }}">
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-file-lines me-1" style="color:var(--apple-orange); font-size:0.75rem;"></i> รายการจัดซื้อจัดจ้าง <span style="color:var(--apple-red);">*</span></label>
                    <textarea class="form-control" name="description" rows="2" required placeholder="รายละเอียดสิ่งที่จัดซื้อ...">{{ old('description') }}</textarea>
                </div>

                <!-- Date -->
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-calendar me-1" style="color:var(--apple-blue); font-size:0.75rem;"></i> วันที่ทำรายการ <span style="color:var(--apple-red);">*</span></label>
                    <input type="text" class="form-control datepicker" name="transaction_date" required value="{{ old('transaction_date', date('d/m/') . (date('Y') + 543)) }}" placeholder="วว/ดด/ปปปป" id="qr-date">
                </div>

                <!-- Amount -->
                <div class="mb-1">
                    <label class="form-label" style="color:var(--apple-red); font-weight:700;"><i class="fas fa-coins me-1" style="font-size:0.75rem;"></i> จำนวนเงิน (บาท) <span style="color:var(--apple-red);">*</span></label>
                    <input type="text" class="form-control form-control-lg currency-input" name="amount" required placeholder="0.00" value="{{ old('amount') }}" style="font-weight:700; font-size:1.3rem; color:var(--apple-red); border-color:rgba(255,59,48,0.3);">
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="padding:16px 24px 22px; border-top:1px solid rgba(0,0,0,0.06); display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn-apple btn-apple-secondary" onclick="closeQuickReserve()">ยกเลิก</button>
                <button type="submit" class="btn-apple btn-apple-danger" style="padding:10px 28px;">
                    <i class="fas fa-bolt"></i> ยืนยันกันเงิน
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes qrFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes qrSlideUp {
        from { opacity: 0; transform: translate(-50%, -46%); }
        to { opacity: 1; transform: translate(-50%, -50%); }
    }
    @keyframes qrFadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    #qr-project-dropdown .qr-dropdown-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 0.88rem;
        transition: background 0.12s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    #qr-project-dropdown .qr-dropdown-item:last-child {
        border-bottom: none;
    }
    #qr-project-dropdown .qr-dropdown-item:hover {
        background: var(--apple-blue-light);
    }
    #qr-project-dropdown .qr-dropdown-item .qr-no {
        font-weight: 700;
        color: var(--apple-blue);
        font-size: 0.9rem;
    }
    #qr-project-dropdown .qr-dropdown-item .qr-name {
        font-size: 0.78rem;
        color: var(--apple-gray-3);
        margin-top: 1px;
    }
    #qr-project-dropdown .qr-dropdown-item .qr-remain {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--apple-green);
        white-space: nowrap;
    }
</style>

<script>
    // ======================================
    // QUICK RESERVE MODAL LOGIC
    // ======================================
    let qrSearchTimer = null;

    function openQuickReserve() {
        const overlay = document.getElementById('quick-reserve-overlay');
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Re-initialize flatpickr on the modal's datepicker
        setTimeout(() => {
            const dateInput = document.getElementById('qr-date');
            if (dateInput && !dateInput._flatpickr) {
                initDatePicker();
            }
            document.getElementById('qr-project-no').focus();
        }, 100);
    }

    function closeQuickReserve() {
        const overlay = document.getElementById('quick-reserve-overlay');
        overlay.style.animation = 'qrFadeOut 0.2s ease forwards';
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.style.animation = 'qrFadeIn 0.25s ease';
            document.body.style.overflow = '';
        }, 200);
    }

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const overlay = document.getElementById('quick-reserve-overlay');
            if (overlay.style.display !== 'none') {
                closeQuickReserve();
            }
        }
    });

    // Project No Autocomplete
    document.getElementById('qr-project-no').addEventListener('input', function() {
        const val = this.value.trim();
        const dropdown = document.getElementById('qr-project-dropdown');
        const infoBox = document.getElementById('qr-project-info');

        if (val.length === 0) {
            dropdown.style.display = 'none';
            infoBox.style.display = 'none';
            return;
        }

        clearTimeout(qrSearchTimer);
        qrSearchTimer = setTimeout(() => {
            fetch(`{{ route('api.projects.search') }}?q=${encodeURIComponent(val)}`)
                .then(res => res.json())
                .then(projects => {
                    dropdown.innerHTML = '';

                    if (projects.length === 0) {
                        dropdown.innerHTML = `
                            <div style="padding:14px; text-align:center; color:var(--apple-gray-4); font-size:0.85rem;">
                                <i class="fas fa-exclamation-triangle" style="color:var(--apple-orange); margin-right:4px;"></i>
                                ไม่พบเลขโครงการที่ตรงกับ "${val}"
                            </div>`;
                        dropdown.style.display = 'block';
                        infoBox.style.display = 'none';
                        return;
                    }

                    projects.forEach(p => {
                        const item = document.createElement('div');
                        item.className = 'qr-dropdown-item';
                        item.innerHTML = `
                            <div>
                                <div class="qr-no">${p.project_no}</div>
                                <div class="qr-name">${p.project_name}</div>
                            </div>
                            <div class="qr-remain">คงเหลือ ${Number(p.remaining).toLocaleString('en', {minimumFractionDigits: 2})} ฿</div>
                        `;
                        item.addEventListener('mousedown', function(e) {
                            e.preventDefault();
                            selectProject(p);
                        });
                        dropdown.appendChild(item);
                    });

                    dropdown.style.display = 'block';
                })
                .catch(() => {
                    dropdown.style.display = 'none';
                });
        }, 200);
    });

    // Hide dropdown on blur
    document.getElementById('qr-project-no').addEventListener('blur', function() {
        setTimeout(() => {
            document.getElementById('qr-project-dropdown').style.display = 'none';
        }, 200);
    });

    // Show dropdown again on focus if value exists
    document.getElementById('qr-project-no').addEventListener('focus', function() {
        if (this.value.trim().length > 0) {
            this.dispatchEvent(new Event('input'));
        }
    });

    function selectProject(project) {
        const input = document.getElementById('qr-project-no');
        const dropdown = document.getElementById('qr-project-dropdown');
        const infoBox = document.getElementById('qr-project-info');

        input.value = project.project_no;
        dropdown.style.display = 'none';

        // Show project info card
        document.getElementById('qr-project-name').textContent = project.project_name;
        document.getElementById('qr-project-remaining').textContent = Number(project.remaining).toLocaleString('en', {minimumFractionDigits: 2}) + ' ฿';
        infoBox.style.display = 'block';
        infoBox.style.animation = 'qrSlideUp 0.25s ease';
    }

    // Auto-open modal if there's a validation error from quickStore
    @if(session('error') && old('project_no'))
    document.addEventListener('DOMContentLoaded', function() {
        openQuickReserve();
    });
    @endif
</script>
@endsection
