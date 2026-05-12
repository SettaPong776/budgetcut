@extends('layouts.app')

@section('topbar-title', 'ภาพรวม')
@section('title', 'แดชบอร์ด')

@section('content')
<div class="animate-in">
    <h1 class="page-hero-title">ภาพรวมงบประมาณ</h1>
    <p class="page-hero-subtitle">สรุปยอดงบประมาณทั้งหมดในระบบของคุณ</p>
</div>

<!-- Stat Tiles -->
<div class="row g-3 mb-4">
    <div class="col-md-4 animate-in">
        <div class="stat-tile blue">
            <div class="stat-label">งบประมาณรวมทั้งสิ้น</div>
            <div class="stat-value">{{ number_format($totalBudget, 2) }} <small style="font-size:0.7em; opacity:0.7;">฿</small></div>
            <div class="stat-icon-bg"><i class="fas fa-coins"></i></div>
        </div>
    </div>
    <div class="col-md-4 animate-in">
        <div class="stat-tile orange">
            <div class="stat-label">เบิกจ่ายไปแล้ว</div>
            <div class="stat-value">{{ number_format($usedBudget, 2) }} <small style="font-size:0.7em; opacity:0.7;">฿</small></div>
            <div class="stat-icon-bg"><i class="fas fa-arrow-trend-down"></i></div>
        </div>
    </div>
    <div class="col-md-4 animate-in">
        <div class="stat-tile green">
            <div class="stat-label">คงเหลือ</div>
            <div class="stat-value">{{ number_format($remainingBudget, 2) }} <small style="font-size:0.7em; opacity:0.7;">฿</small></div>
            <div class="stat-icon-bg"><i class="fas fa-vault"></i></div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-lg-7 animate-in">
        <div class="apple-card h-100">
            <div class="apple-card-header">
                <span>ยอดเบิกจ่ายรายเดือน</span>
            </div>
            <div class="apple-card-body">
                <canvas id="monthlySpendingChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5 animate-in">
        <div class="apple-card h-100">
            <div class="apple-card-header">
                <span>สัดส่วนงบประมาณ 5 โครงการสูงสุด</span>
            </div>
            <div class="apple-card-body">
                <canvas id="projectBalancesChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions & Projects Count -->
<div class="row g-3">
    <div class="col-lg-8 animate-in">
        <div class="apple-card">
            <div class="apple-card-header">
                <span>รายการเบิกจ่ายล่าสุด</span>
                <a href="{{ route('transactions.index') }}" class="btn-apple btn-apple-secondary btn-apple-sm">ดูทั้งหมด <i class="fas fa-arrow-right" style="font-size:0.7em;"></i></a>
            </div>
            <div style="padding: 0;">
                <table class="apple-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">ลำดับ</th>
                            <th>วันที่</th>
                            <th>โครงการ</th>
                            <th>รายการ</th>
                            <th class="text-end">ยอดเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $tx)
                        <tr>
                            <td class="text-center" style="font-weight: 500; color: var(--apple-gray-4);">{{ $loop->iteration }}</td>
                            <td style="white-space:nowrap;">{{ \Carbon\Carbon::parse($tx->transaction_date)->toThaiShortDate() }}</td>
                            <td>{{ Str::limit($tx->project->project_name, 30) }}</td>
                            <td style="color: var(--apple-gray-3);">{{ Str::limit($tx->description, 35) }}</td>
                            <td class="text-end" style="color: var(--apple-red); font-weight: 600;">-{{ number_format($tx->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center" style="padding: 40px; color: var(--apple-gray-4);">ยังไม่มีรายการเบิกจ่าย</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4 animate-in">
        <div class="apple-card h-100 d-flex flex-column align-items-center justify-content-center text-center" style="padding: 40px 24px;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--apple-blue-light); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 700; color: var(--apple-blue); margin-bottom: 16px;">
                {{ $projectsCount }}
            </div>
            <div style="font-size: 1.1rem; font-weight: 600; color: var(--apple-gray-1); margin-bottom: 4px;">โครงการในระบบ</div>
            <div style="font-size: 0.85rem; color: var(--apple-gray-4); margin-bottom: 20px;">ปีงบประมาณ {{ date('Y') + 543 }}</div>
            <a href="{{ route('projects.create') }}" class="btn-apple btn-apple-primary">
                <i class="fas fa-plus"></i> สร้างโครงการ
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Shared chart options for Apple aesthetic
    Chart.defaults.font.family = "'Inter', 'Noto Sans Thai', sans-serif";
    Chart.defaults.color = "#86868b";
    
    // 1. Monthly Spending Line Chart
    const ctxMonthly = document.getElementById('monthlySpendingChart').getContext('2d');
    
    // Create gradient
    let gradientBlue = ctxMonthly.createLinearGradient(0, 0, 0, 400);
    gradientBlue.addColorStop(0, 'rgba(0, 113, 227, 0.2)');
    gradientBlue.addColorStop(1, 'rgba(0, 113, 227, 0)');

    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartMonths) !!},
            datasets: [{
                label: 'ยอดเบิกจ่าย (บาท)',
                data: {!! json_encode($chartSpending) !!},
                borderColor: '#0071e3',
                backgroundColor: gradientBlue,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#0071e3',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let value = context.parsed.y;
                            return ' ' + value.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' บาท';
                        }
                    }
                }
            },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { 
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    border: { display: false },
                    ticks: {
                        callback: function(value) { return (value/1000).toLocaleString() + 'k'; }
                    }
                }
            }
        }
    });

    // 2. Project Balances Bar Chart
    const ctxProjects = document.getElementById('projectBalancesChart').getContext('2d');
    new Chart(ctxProjects, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartProjectNames) !!},
            datasets: [
                {
                    label: 'ใช้ไปแล้ว',
                    data: {!! json_encode($chartProjectUsed) !!},
                    backgroundColor: '#ff3b30',
                    borderRadius: 4
                },
                {
                    label: 'คงเหลือ',
                    data: {!! json_encode($chartProjectRemaining) !!},
                    backgroundColor: '#34c759',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let value = context.parsed.y;
                            return ' ' + context.dataset.label + ': ' + value.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' บาท';
                        }
                    }
                }
            },
            scales: {
                x: { stacked: true, grid: { display: false }, border: { display: false } },
                y: { 
                    stacked: true, 
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    border: { display: false },
                    ticks: { display: false } // Hide Y axis text for cleaner look
                }
            }
        }
    });
});
</script>
@endsection
