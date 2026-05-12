@extends('layouts.app')

@section('topbar-title', 'สร้างโครงการ')

@section('content')
<div class="animate-in">
    <h1 class="page-hero-title">สร้างโครงการใหม่</h1>
    <p class="page-hero-subtitle">กรอกข้อมูลเพื่อเปิดบัญชีงบประมาณสำหรับโครงการนี้</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9 animate-in">
        <div class="apple-card">
            <div class="apple-card-header">
                <span>ข้อมูลโครงการ</span>
                <a href="{{ route('projects.index') }}" class="btn-apple btn-apple-secondary btn-apple-sm"><i class="fas fa-arrow-left" style="font-size:0.7em;"></i> ย้อนกลับ</a>
            </div>
            <div class="apple-card-body apple-form">
                @if ($errors->any())
                    <div class="apple-alert apple-alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>กรุณาตรวจสอบข้อมูลอีกครั้ง:
                            @foreach ($errors->all() as $error) {{ $error }} @endforeach
                        </span>
                    </div>
                @endif

                <form method="POST" action="{{ route('projects.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">เลขโครงการ <span style="color:var(--apple-red);">*</span></label>
                            <input type="text" class="form-control" name="project_no" required value="{{ old('project_no') }}" placeholder="PRJ-69-001">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">ชื่อโครงการ <span style="color:var(--apple-red);">*</span></label>
                            <input type="text" class="form-control" name="project_name" required value="{{ old('project_name') }}" placeholder="โครงการจัดซื้อ...">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">ปีงบประมาณ <span style="color:var(--apple-red);">*</span></label>
                            <input type="number" class="form-control" name="fiscal_year" required value="{{ old('fiscal_year', date('Y')+543) }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">ประเภทงบ <span style="color:var(--apple-red);">*</span></label>
                            <select class="form-select" name="budget_type" required>
                                <option value="">เลือกประเภทงบ</option>
                                <option value="งบดำเนินงาน" {{ old('budget_type') == 'งบดำเนินงาน' ? 'selected' : '' }}>งบดำเนินงาน</option>
                                <option value="งบลงทุน" {{ old('budget_type') == 'งบลงทุน' ? 'selected' : '' }}>งบลงทุน</option>
                                <option value="งบเงินอุดหนุน" {{ old('budget_type') == 'งบเงินอุดหนุน' ? 'selected' : '' }}>งบเงินอุดหนุน</option>
                                <option value="งบรายจ่ายอื่น" {{ old('budget_type') == 'งบรายจ่ายอื่น' ? 'selected' : '' }}>งบรายจ่ายอื่น</option>
                            </select>
                        </div>

                        <div class="col-12"><hr style="border-color: rgba(0,0,0,0.06); margin: 8px 0;"></div>

                        <div class="col-md-6">
                            <label class="form-label">ผู้รับผิดชอบโครงการ</label>
                            <input type="text" class="form-control" name="responsible_person" value="{{ old('responsible_person') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">วันเริ่มต้น</label>
                            <input type="text" class="form-control datepicker" name="start_date" value="{{ old('start_date') }}" placeholder="วว/ดด/ปปปป">
                        </div>

                        <!-- Budget highlight -->
                        <div class="col-12">
                            <div style="background: linear-gradient(135deg, rgba(0,113,227,0.04), rgba(88,86,214,0.04)); border: 1.5px solid rgba(0,113,227,0.15); border-radius: var(--radius-md); padding: 24px; margin-top: 8px;">
                                <label class="form-label" style="font-size: 0.95rem; color: var(--apple-blue); font-weight: 700;">
                                    <i class="fas fa-coins me-1"></i> งบประมาณที่ได้รับจัดสรร (บาท)
                                </label>
                                <input type="text" class="form-control form-control-lg currency-input" name="total_budget" required value="{{ old('total_budget') }}" placeholder="0.00" style="font-size: 1.6rem; font-weight: 700; color: var(--apple-blue); letter-spacing: -0.02em;">
                                <div style="font-size: 0.78rem; color: var(--apple-gray-4); margin-top: 6px;">ยอดเงินนี้จะเป็นยอดตั้งต้นของบัญชีโครงการ (เหมือนเงินฝากในบัญชี)</div>
                            </div>
                        </div>

                        <div class="col-12 text-end" style="margin-top: 16px;">
                            <button type="submit" class="btn-apple btn-apple-primary" style="padding: 12px 32px;">
                                <i class="fas fa-check"></i> บันทึกโครงการ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
