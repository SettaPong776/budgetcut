@extends('layouts.app')

@section('topbar-title', 'แก้ไขรายการเบิกจ่าย')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 animate-in">
        <div class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ route('projects.show', $transaction->project_id) }}" class="btn-apple btn-apple-secondary btn-apple-sm mb-2">
                    <i class="fas fa-arrow-left"></i> กลับไปโครงการ
                </a>
                <h1 class="page-hero-title" style="font-size: 1.8rem;">แก้ไขรายการ</h1>
                <p class="page-hero-subtitle" style="margin-bottom: 0;">เลขที่อ้างอิง: {{ $transaction->doc_ref }}</p>
            </div>
        </div>

        <div class="apple-card">
            <div class="apple-card-body apple-form">
                <form method="POST" action="{{ route('transactions.update', $transaction->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">โครงการ <span style="color:var(--apple-red);">*</span></label>
                        <select class="form-select" name="project_id" required>
                            @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ $transaction->project_id == $proj->id ? 'selected' : '' }}>
                                {{ $proj->project_name }} (คงเหลือ: {{ number_format($proj->total_budget - $proj->transactions()->where('status', 'approved')->where('id', '!=', $transaction->id)->sum('amount'), 2) }} ฿)
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ผู้ขอกัน <span style="color:var(--apple-red);">*</span></label>
                        <input type="text" class="form-control" name="requester" required value="{{ old('requester', $transaction->requester) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รายการจัดซื้อจัดจ้าง <span style="color:var(--apple-red);">*</span></label>
                        <textarea class="form-control" name="description" rows="3" required>{{ old('description', $transaction->description) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">วันที่ทำรายการ <span style="color:var(--apple-red);">*</span></label>
                        @php
                            $dateObj = \Carbon\Carbon::parse($transaction->transaction_date);
                            $formattedDate = $dateObj->format('d/m/') . ($dateObj->year + 543);
                        @endphp
                        <input type="text" class="form-control datepicker" name="transaction_date" required value="{{ old('transaction_date', $formattedDate) }}" placeholder="วว/ดด/ปปปป">
                    </div>

                    <div class="mb-4">
                        <label class="form-label" style="color: var(--apple-red); font-weight: 700;">จำนวนเงิน (บาท) <span style="color:var(--apple-red);">*</span></label>
                        <input type="text" class="form-control form-control-lg currency-input" name="amount" required value="{{ old('amount', number_format($transaction->amount, 2)) }}" style="font-weight: 700; font-size: 1.3rem; color: var(--apple-red); border-color: rgba(255,59,48,0.3);">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-apple btn-apple-primary flex-grow-1 justify-content-center" style="padding: 12px;">
                            <i class="fas fa-save"></i> บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
