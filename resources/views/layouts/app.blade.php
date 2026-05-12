<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบตัดยอดงบประมาณ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* ======================================
           APPLE-INSPIRED DESIGN SYSTEM
        ====================================== */
        :root {
            /* Apple-style color palette */
            --apple-blue: #0071e3;
            --apple-blue-hover: #0077ed;
            --apple-blue-light: rgba(0, 113, 227, 0.08);
            --apple-green: #34c759;
            --apple-red: #ff3b30;
            --apple-orange: #ff9500;
            --apple-purple: #af52de;
            --apple-gray-1: #1d1d1f;
            --apple-gray-2: #424245;
            --apple-gray-3: #6e6e73;
            --apple-gray-4: #86868b;
            --apple-gray-5: #d2d2d7;
            --apple-gray-6: #f5f5f7;
            --apple-white: #ffffff;
            --apple-bg: #fbfbfd;
            --sidebar-width: 240px;
            --topbar-height: 52px;
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 14px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.08);
            --transition: cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Noto Sans Thai', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--apple-bg);
            color: var(--apple-gray-1);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            letter-spacing: -0.01em;
            overflow-x: hidden;
        }

        /* ======================================
           SIDEBAR — Apple Finder-style
        ====================================== */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: rgba(255,255,255,0.72);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-right: 1px solid rgba(0,0,0,0.08);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: all 0.4s var(--transition);
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--apple-blue), #5856d6);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1rem;
            box-shadow: 0 4px 12px rgba(0,113,227,0.3);
        }

        .sidebar-brand span {
            font-weight: 700;
            font-size: 1.15rem;
            color: var(--apple-gray-1);
            letter-spacing: -0.02em;
        }

        .sidebar-section-label {
            padding: 20px 20px 6px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--apple-gray-4);
        }

        .sidebar-nav {
            padding: 0 10px;
            flex: 1;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            color: var(--apple-gray-2);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 0.95rem;
            color: var(--apple-gray-4);
            transition: color 0.2s ease;
        }

        .sidebar-nav a:hover {
            background: rgba(0,0,0,0.04);
            color: var(--apple-gray-1);
        }

        .sidebar-nav a.active {
            background: var(--apple-blue-light);
            color: var(--apple-blue);
        }

        .sidebar-nav a.active i {
            color: var(--apple-blue);
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(0,0,0,0.06);
            font-size: 0.72rem;
            color: var(--apple-gray-4);
            text-align: center;
        }

        /* ======================================
           MAIN CONTENT
        ====================================== */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin 0.4s var(--transition);
        }

        /* Top Bar — Apple style minimal */
        .topbar {
            position: sticky;
            top: 0;
            height: var(--topbar-height);
            background: rgba(251,251,253,0.72);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            z-index: 50;
        }

        .topbar-title {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--apple-gray-1);
        }

        .topbar-meta {
            font-size: 0.8rem;
            color: var(--apple-gray-4);
            font-weight: 500;
        }

        .page-container {
            padding: 28px 32px 60px;
            max-width: 1200px;
        }

        /* ======================================
           TYPOGRAPHY — Apple Large Title
        ====================================== */
        .page-hero-title {
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            color: var(--apple-gray-1);
            margin-bottom: 4px;
            line-height: 1.2;
        }

        .page-hero-subtitle {
            font-size: 1.05rem;
            color: var(--apple-gray-3);
            font-weight: 400;
            margin-bottom: 28px;
        }

        /* ======================================
           CARDS — Apple Frosted Glass
        ====================================== */
        .apple-card {
            background: var(--apple-white);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: box-shadow 0.3s ease, transform 0.3s var(--transition);
        }

        .apple-card:hover {
            box-shadow: var(--shadow-md);
        }

        .apple-card-header {
            padding: 18px 22px;
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--apple-gray-1);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .apple-card-body {
            padding: 22px;
        }

        /* ======================================
           STAT TILES — Apple Widget style
        ====================================== */
        .stat-tile {
            position: relative;
            padding: 22px 24px;
            border-radius: var(--radius-lg);
            overflow: hidden;
            color: white;
            transition: transform 0.35s var(--transition), box-shadow 0.35s ease;
        }

        .stat-tile:hover {
            transform: scale(1.02);
        }

        .stat-tile.blue { background: linear-gradient(145deg, #0071e3, #2997ff); box-shadow: 0 8px 24px rgba(0,113,227,0.25); }
        .stat-tile.green { background: linear-gradient(145deg, #28a745, #34c759); box-shadow: 0 8px 24px rgba(52,199,89,0.25); }
        .stat-tile.orange { background: linear-gradient(145deg, #e8860c, #ff9f0a); box-shadow: 0 8px 24px rgba(255,149,0,0.25); }

        .stat-tile .stat-label {
            font-size: 0.78rem;
            font-weight: 500;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 6px;
        }

        .stat-tile .stat-value {
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .stat-tile .stat-icon-bg {
            position: absolute;
            right: 18px; bottom: 16px;
            font-size: 2.8rem;
            opacity: 0.15;
        }

        /* ======================================
           TABLES — Apple clean
        ====================================== */
        .apple-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .apple-table thead th {
            padding: 12px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--apple-gray-4);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            background: var(--apple-gray-6);
            white-space: nowrap;
        }

        .apple-table tbody td {
            padding: 14px 16px;
            font-size: 0.88rem;
            color: var(--apple-gray-2);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            vertical-align: middle;
        }

        .apple-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .apple-table tbody tr:hover {
            background: rgba(0,113,227,0.03);
        }

        .apple-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* ======================================
           BUTTONS — Apple style
        ====================================== */
        .btn-apple {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border-radius: 980px;
            font-size: 0.88rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-apple-primary {
            background: var(--apple-blue);
            color: white;
            box-shadow: 0 2px 8px rgba(0,113,227,0.25);
        }
        .btn-apple-primary:hover {
            background: var(--apple-blue-hover);
            color: white;
            transform: scale(1.03);
            box-shadow: 0 4px 14px rgba(0,113,227,0.35);
        }

        .btn-apple-secondary {
            background: var(--apple-gray-6);
            color: var(--apple-gray-2);
        }
        .btn-apple-secondary:hover {
            background: var(--apple-gray-5);
            color: var(--apple-gray-1);
        }

        .btn-apple-danger {
            background: var(--apple-red);
            color: white;
            box-shadow: 0 2px 8px rgba(255,59,48,0.25);
        }
        .btn-apple-danger:hover {
            background: #e0342b;
            color: white;
            transform: scale(1.03);
        }

        .btn-apple-outline {
            background: transparent;
            border: 1.5px solid var(--apple-blue);
            color: var(--apple-blue);
        }
        .btn-apple-outline:hover {
            background: var(--apple-blue);
            color: white;
        }

        .btn-apple-sm {
            padding: 6px 14px;
            font-size: 0.82rem;
        }

        /* ======================================
           FORM CONTROLS — Apple style
        ====================================== */
        .apple-form .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--apple-gray-2);
            margin-bottom: 5px;
        }

        .apple-form .form-control,
        .apple-form .form-select {
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--apple-gray-5);
            font-size: 0.9rem;
            background: var(--apple-white);
            transition: all 0.2s ease;
            color: var(--apple-gray-1);
        }

        .apple-form .form-control:focus,
        .apple-form .form-select:focus {
            border-color: var(--apple-blue);
            box-shadow: 0 0 0 3px rgba(0,113,227,0.15);
            outline: none;
        }

        .apple-form .form-control-lg {
            padding: 14px 18px;
            font-size: 1.4rem;
        }

        /* ======================================
           BADGES
        ====================================== */
        .apple-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 980px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .apple-badge-blue { background: rgba(0,113,227,0.1); color: var(--apple-blue); }
        .apple-badge-green { background: rgba(52,199,89,0.1); color: var(--apple-green); }
        .apple-badge-red { background: rgba(255,59,48,0.1); color: var(--apple-red); }
        .apple-badge-gray { background: rgba(0,0,0,0.05); color: var(--apple-gray-3); }

        /* ======================================
           ACTION BUTTONS (Inline table actions)
        ====================================== */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--apple-gray-3);
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .action-btn:hover {
            background: rgba(0,0,0,0.05);
            color: var(--apple-gray-1);
        }
        .action-btn.edit:hover { background: rgba(0,113,227,0.1); color: var(--apple-blue); }
        .action-btn.delete:hover { background: rgba(255,59,48,0.1); color: var(--apple-red); }

        /* ======================================
           ALERTS — Apple notification style
        ====================================== */
        .apple-alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 0.88rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.4s var(--transition);
        }

        .apple-alert-success {
            background: rgba(52,199,89,0.08);
            color: #1a7a32;
            border: 1px solid rgba(52,199,89,0.2);
        }

        .apple-alert-error {
            background: rgba(255,59,48,0.08);
            color: #c0291f;
            border: 1px solid rgba(255,59,48,0.2);
        }

        .apple-alert .close-btn {
            margin-left: auto;
            background: none;
            border: none;
            font-size: 1.1rem;
            color: inherit;
            opacity: 0.5;
            cursor: pointer;
        }

        /* ======================================
           LEDGER HERO — Apple gradient header
        ====================================== */
        .ledger-hero {
            background: linear-gradient(135deg, var(--apple-gray-1) 0%, #2c2c2e 100%);
            color: white;
            padding: 36px 32px;
            border-radius: var(--radius-xl);
            position: relative;
            overflow: hidden;
        }

        .ledger-hero::after {
            content: '';
            position: absolute;
            top: -60%; right: -10%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .ledger-hero .balance-label {
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            opacity: 0.6;
        }

        .ledger-hero .balance-value {
            font-size: 3rem;
            font-weight: 700;
            letter-spacing: -0.03em;
            line-height: 1.15;
        }

        .ledger-hero .balance-currency {
            font-size: 1.2rem;
            font-weight: 500;
            opacity: 0.7;
            margin-left: 4px;
        }

        /* ======================================
           ANIMATIONS
        ====================================== */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeUp 0.5s var(--transition) both;
        }

        .animate-in:nth-child(1) { animation-delay: 0s; }
        .animate-in:nth-child(2) { animation-delay: 0.06s; }
        .animate-in:nth-child(3) { animation-delay: 0.12s; }
        .animate-in:nth-child(4) { animation-delay: 0.18s; }

        /* ======================================
           RESPONSIVE
        ====================================== */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
            .page-hero-title { font-size: 1.6rem; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--apple-gray-5); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--apple-gray-4); }
    </style>
</head>
<body>
    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-wallet"></i></div>
            <span>Budget Cut </span>
        </div>

        <div class="sidebar-section-label">เมนูหลัก</div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> แดชบอร์ด
            </a>
            <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i> บัญชีโครงการ
            </a>
            <a href="{{ route('transactions.index') }}" class="{{ request()->is('transactions*') ? 'active' : '' }}">
                <i class="fas fa-arrow-right-arrow-left"></i> การเบิกจ่าย
            </a>
        </nav>

        <div class="sidebar-section-label">ตั้งค่า</div>
        <nav class="sidebar-nav">
            <a href="{{ route('requesters.index') }}" class="{{ request()->is('requesters*') ? 'active' : '' }}">
                <i class="fas fa-user-group"></i> จัดการผู้ขอกัน
            </a>
        </nav>

        <div class="sidebar-footer">
            ระบบตัดยอดงบประมาณ v1.0<br>
            ปีงบประมาณ {{ date('Y') + 543 }}
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-title">@yield('topbar-title', 'BudgetCut')</div>
            <div class="topbar-meta"><i class="fas fa-circle" style="font-size: 6px; color: var(--apple-green); vertical-align: middle; margin-right: 4px;"></i> ออนไลน์</div>
        </header>

        <div class="page-container">
            @if(session('success'))
                <div class="apple-alert apple-alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="apple-alert apple-alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SweetAlert2 for delete confirmation
        function confirmDelete(event, formElement) {
            confirmSubmit(event, formElement, 'ยืนยันการยกเลิก?', 'รายการนี้จะถูกยกเลิก และยอดเงินจะคืนกลับโครงการ', 'var(--apple-red)', 'ยืนยัน', 'warning');
        }

        // Generic SweetAlert2 confirmation
        function confirmSubmit(event, formElement, title, text, confirmColor = 'var(--apple-blue)', confirmText = 'ยืนยัน', icon = 'warning') {
            event.preventDefault();
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: 'var(--apple-gray-4)',
                confirmButtonText: confirmText,
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    popup: 'apple-card',
                    confirmButton: `btn-apple ${confirmColor === 'var(--apple-red)' ? 'btn-apple-danger' : 'btn-apple-primary'}`,
                    cancelButton: 'btn-apple btn-apple-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
        }

        // Thai B.E. Year Support for Flatpickr
        function initDatePicker() {
            flatpickr(".datepicker", {
                locale: "th",
                dateFormat: "d/m/Y",
                allowInput: true,
                formatDate: function(date, format, locale) {
                    let d = date.getDate().toString().padStart(2, '0');
                    let m = (date.getMonth() + 1).toString().padStart(2, '0');
                    let y = date.getFullYear() + 543;
                    return `${d}/${m}/${y}`;
                },
                parseDate: function(datestr, format) {
                    let parts = datestr.split('/');
                    if (parts.length === 3) {
                        let day = parseInt(parts[0], 10);
                        let month = parseInt(parts[1], 10) - 1;
                        let year = parseInt(parts[2], 10);
                        if (year > 2400) { year -= 543; } // Convert BE to AD
                        return new Date(year, month, day);
                    }
                    return undefined;
                },
                onReady: function(selectedDates, dateStr, instance) {
                    const yearInputs = instance.calendarContainer.querySelectorAll('.cur-year');
                    yearInputs.forEach(input => {
                        input.value = parseInt(input.value) + 543;
                    });
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    setTimeout(() => {
                        const yearInputs = instance.calendarContainer.querySelectorAll('.cur-year');
                        yearInputs.forEach(input => {
                            input.value = parseInt(input.value) + 543;
                        });
                    }, 0);
                },
                onMonthChange: function(selectedDates, dateStr, instance) {
                    setTimeout(() => {
                        const yearInputs = instance.calendarContainer.querySelectorAll('.cur-year');
                        yearInputs.forEach(input => {
                            input.value = parseInt(input.value) + 543;
                        });
                    }, 0);
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', initDatePicker);

        // Currency Formatting Utility
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('currency-input')) {
                let cursorPosition = e.target.selectionStart;
                let originalLength = e.target.value.length;
                
                let value = e.target.value.replace(/,/g, '');
                if (value === '') return;
                
                let parts = value.split('.');
                // Remove non-digits from the integer part
                parts[0] = parts[0].replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                
                // Limit decimal to 2 digits if exists
                if (parts.length > 1) {
                    parts[1] = parts[1].replace(/\D/g, '').substring(0, 2);
                }
                
                e.target.value = parts.join('.');
                
                // Adjust cursor position
                let newLength = e.target.value.length;
                e.target.setSelectionRange(cursorPosition + (newLength - originalLength), cursorPosition + (newLength - originalLength));
            }
        });

        // Strip commas and convert Thai dates to Gregorian before form submission
        document.addEventListener('submit', function(e) {
            e.target.querySelectorAll('.currency-input').forEach(input => {
                input.value = input.value.replace(/,/g, '');
            });
            
            // Note: Flatpickr with dateFormat "d/m/Y" will send d/m/Y to server. 
            // We convert it to Y-m-d (Gregorian) for database ONLY for POST/PUT requests.
            // For GET requests (like search filters), we keep d/m/Y so the URL and input stay correct.
            if (e.target.method && e.target.method.toUpperCase() !== 'GET') {
                e.target.querySelectorAll('.datepicker').forEach(input => {
                    if (input.value) {
                        let parts = input.value.split('/');
                        if (parts.length === 3) {
                            let day = parts[0];
                            let month = parts[1];
                            let year = parseInt(parts[2]) - 543; // Convert B.E. to Gregorian
                            input.value = `${year}-${month}-${day}`;
                        }
                    }
                });
            }
        });

        // ======================================
        // REQUESTER AUTOCOMPLETE
        // ======================================
        (function() {
            let requesterNames = [];
            
            // Fetch requester names from API
            fetch('{{ route("api.requesters") }}')
                .then(res => res.json())
                .then(data => { requesterNames = data; })
                .catch(() => {});

            document.addEventListener('click', function(e) {
                // Close all dropdowns when clicking outside input or dropdown
                if (e.target.matches('input[name="requester"]')) return;
                if (e.target.closest('.requester-dropdown')) return;
                document.querySelectorAll('.requester-dropdown').forEach(d => d.remove());
            });

            document.addEventListener('input', function(e) {
                const input = e.target;
                if (!input.matches('input[name="requester"]')) return;
                
                // Remove existing dropdown
                const existingDd = input.parentElement.querySelector('.requester-dropdown');
                if (existingDd) existingDd.remove();

                const val = input.value.toLowerCase().trim();
                if (val.length === 0) {
                    showDropdown(input, requesterNames);
                    return;
                }

                const matches = requesterNames.filter(name => name.toLowerCase().includes(val));
                if (matches.length === 0) return;

                showDropdown(input, matches);
            });

            document.addEventListener('focus', function(e) {
                const input = e.target;
                if (!input.matches('input[name="requester"]')) return;
                
                // Make sure parent is relatively positioned
                if (getComputedStyle(input.parentElement).position === 'static') {
                    input.parentElement.style.position = 'relative';
                }
                
                if (requesterNames.length > 0) {
                    showDropdown(input, requesterNames);
                }
            }, true);

            function showDropdown(input, items) {
                // Remove existing
                const existingDd = input.parentElement.querySelector('.requester-dropdown');
                if (existingDd) existingDd.remove();

                if (items.length === 0) return;

                const dd = document.createElement('div');
                dd.className = 'requester-dropdown';
                dd.style.cssText = `
                    position: absolute; top: 100%; left: 0; right: 0; z-index: 999;
                    background: var(--apple-white); border: 1px solid var(--apple-gray-5);
                    border-radius: 10px; margin-top: 4px; max-height: 200px; overflow-y: auto;
                    box-shadow: var(--shadow-lg);
                `;

                items.forEach(name => {
                    const item = document.createElement('div');
                    item.textContent = name;
                    item.style.cssText = `
                        padding: 10px 14px; cursor: pointer; font-size: 0.9rem;
                        transition: background 0.15s ease;
                    `;
                    item.addEventListener('mouseenter', () => item.style.background = 'var(--apple-blue-light)');
                    item.addEventListener('mouseleave', () => item.style.background = 'transparent');
                    item.addEventListener('mousedown', function(ev) {
                        ev.preventDefault();
                        input.value = name;
                        dd.remove();
                    });
                    dd.appendChild(item);
                });

                input.parentElement.appendChild(dd);

                // Close when input loses focus
                input.addEventListener('blur', function handler() {
                    setTimeout(() => {
                        const d = input.parentElement.querySelector('.requester-dropdown');
                        if (d) d.remove();
                    }, 200);
                    input.removeEventListener('blur', handler);
                });
            }
        })();
    </script>
</body>
</html>
