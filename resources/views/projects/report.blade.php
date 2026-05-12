<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานโครงการ — {{ $project->project_name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 20mm 15mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Noto Sans Thai', sans-serif;
            color: #1d1d1f;
            background: #f0f0f0;
            -webkit-font-smoothing: antialiased;
        }

        /* Print toolbar — hidden when printing */
        .print-toolbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: rgba(30,30,30,0.95);
            backdrop-filter: blur(20px);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            z-index: 9999;
        }

        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 980px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .toolbar-btn-primary {
            background: #0071e3;
            color: white;
            box-shadow: 0 2px 10px rgba(0,113,227,0.3);
        }
        .toolbar-btn-primary:hover {
            background: #0077ed;
            transform: scale(1.03);
        }

        .toolbar-btn-secondary {
            background: #ff3b30;
            color: white;
            box-shadow: 0 2px 10px rgba(255,59,48,0.3);
        }
        .toolbar-btn-secondary:hover {
            background: #e0342b;
            transform: scale(1.03);
        }

        .toolbar-btn-outline {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .toolbar-btn-outline:hover {
            background: rgba(255,255,255,0.2);
        }

        /* A4 Paper */
        .paper {
            width: 210mm;
            margin: 70px auto 40px;
            background: white;
            padding: 20mm 18mm;
            box-shadow: 0 10px 40px rgba(0,0,0,0.12);
            border-radius: 4px;
        }

        /* Header */
        .report-header {
            text-align: center;
            padding-bottom: 16px;
            border-bottom: 3px solid #1d1d1f;
            margin-bottom: 20px;
        }

        .report-header h1 {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .report-header h2 {
            font-size: 1rem;
            font-weight: 500;
            color: #6e6e73;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 40px;
            margin-bottom: 24px;
            padding: 16px 20px;
            background: #f9f9fb;
            border-radius: 10px;
            border: 1px solid #e5e5ea;
        }

        .info-item {
            display: flex;
            font-size: 0.85rem;
            padding: 4px 0;
        }

        .info-label {
            font-weight: 600;
            color: #6e6e73;
            min-width: 130px;
            flex-shrink: 0;
        }

        .info-value {
            color: #1d1d1f;
            font-weight: 500;
        }

        /* Section title */
        .section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #0071e3;
            display: inline-block;
        }

        /* Tables */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 0.82rem;
        }

        .report-table thead th {
            background: #1d1d1f;
            color: white;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 0.78rem;
            text-align: center;
            white-space: nowrap;
        }

        .report-table tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #e5e5ea;
            vertical-align: middle;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fafafc;
        }

        .report-table tfoot td {
            padding: 10px 12px;
            font-weight: 700;
            border-top: 2px solid #1d1d1f;
            background: #f5f5f7;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-blue { color: #0071e3; }
        .text-red { color: #ff3b30; }
        .text-green { color: #34c759; }
        .fw-bold { font-weight: 700; }

        /* Summary table */
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 0.85rem;
        }

        .summary-table th,
        .summary-table td {
            padding: 12px 16px;
            border: 1px solid #d2d2d7;
            text-align: center;
        }

        .summary-table thead th {
            background: #1d1d1f;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .summary-table tbody td {
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Footer */
        .report-footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 1px solid #e5e5ea;
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            color: #86868b;
        }

        /* Print styles */
        @media print {
            @page {
                size: A4;
                margin: 15mm 12mm;
            }

            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-toolbar { display: none !important; }

            .paper {
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                min-height: auto;
            }

            .info-grid {
                page-break-inside: avoid;
            }

            .report-table tr,
            .summary-table tr {
                page-break-inside: avoid;
            }

            .report-footer {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<!-- Toolbar -->
<div class="print-toolbar" id="toolbar">
    <button class="toolbar-btn toolbar-btn-primary" onclick="window.print()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        พิมพ์เอกสาร
    </button>
    <button class="toolbar-btn toolbar-btn-secondary" onclick="savePDF()">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
        บันทึก PDF
    </button>
</div>

<!-- A4 Paper -->
<div class="paper" id="report-content">

    <!-- Report Header -->
    <div class="report-header">
        <h1>รายงานสรุปผลการเบิกจ่ายงบประมาณ</h1>
        <h2>{{ $project->project_name }}</h2>
    </div>

    <!-- Project Info -->
    <div class="info-grid">
        <div class="info-item">
            <span class="info-label">หมายเลขโครงการ</span>
            <span class="info-value">{{ $project->project_no }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">ปีงบประมาณ</span>
            <span class="info-value">{{ $project->fiscal_year }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">ชื่อโครงการ</span>
            <span class="info-value">{{ $project->project_name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">ประเภทงบ</span>
            <span class="info-value">{{ $project->budget_type }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">ผู้รับผิดชอบ</span>
            <span class="info-value">{{ $project->responsible_person ?? '-' }}</span>
        </div>

        <div class="info-item">
            <span class="info-label">วันเริ่มโครงการ</span>
            <span class="info-value">
                @if($project->start_date)
                    {{ \Carbon\Carbon::parse($project->start_date)->toThaiShortDate() }}
                @else - @endif
            </span>
        </div>
    </div>
    <!-- Budget Summary Table -->
    <div class="section-title">สรุปยอดการเบิกจ่าย</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>งบประมาณ (บาท)</th>
                <th>ยอดกัน (บาท)</th>
                <th>ยอดเบิก (บาท)</th>
                <th>คงเหลือ (บาท)</th>
                <th>ร้อยละการเบิกจ่าย</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-blue fw-bold">{{ number_format($project->total_budget, 2) }}</td>
                <td class="fw-bold">{{ number_format($usedBudget, 2) }}</td>
                <td class="text-red fw-bold">{{ number_format($usedBudget, 2) }}</td>
                <td class="text-green fw-bold">{{ number_format($remainingBudget, 2) }}</td>
                <td class="fw-bold">{{ number_format($percentUsed, 2) }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- Transaction Detail Table -->
    <div class="section-title">รายละเอียดการเบิกจ่ายงบประมาณ</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 50px;">ลำดับ</th>
                <th>วันที่ทำรายการ</th>
                <th>รายการ</th>
                <th>ผู้ขอกัน</th>
                <th class="text-end">ยอดกัน (บาท)</th>
            </tr>
        </thead>
        <tbody>
            @php $runningTotal = 0; @endphp
            @forelse($transactions as $index => $tx)
            @php $runningTotal += $tx->amount; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center" style="white-space:nowrap;">{{ \Carbon\Carbon::parse($tx->transaction_date)->toThaiShortDate() }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->requester ?? '-' }}</td>
                <td class="text-end">{{ number_format($tx->amount, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #86868b;">ยังไม่มีรายการเบิกจ่าย</td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot>
            <tr>
                <td colspan="4" class="text-end">ผลรวมยอดกัน (บาท)</td>
                <td class="text-end text-red">{{ number_format($runningTotal, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Footer -->
    <div class="report-footer">
        <span>พิมพ์เมื่อ: {{ now()->toThaiShortDate() }} เวลา {{ date('H:i') }} น.</span>
        <span>ระบบตัดยอดงบประมาณ — Budget Cut</span>
    </div>

</div>

<script>
    function savePDF() {
        const toolbar = document.getElementById('toolbar');
        toolbar.style.display = 'none';

        const element = document.getElementById('report-content');
        const opt = {
            margin:       0,
            filename:     'รายงาน_{{ $project->project_no }}_{{ $project->project_name }}.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true, scrollY: 0 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' },
            pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            toolbar.style.display = 'flex';
        });
    }
</script>

</body>
</html>
