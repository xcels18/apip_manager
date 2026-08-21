<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - APIP Inspektorat Puncak Jaya</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f7fafc;
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: white;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo-icon {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-logo-icon svg {
            width: 28px;
            height: 28px;
            fill: #1e40af;
        }

        .sidebar-logo-text {
            color: white;
        }

        .sidebar-logo-title {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }

        .sidebar-logo-subtitle {
            font-size: 11px;
            opacity: 0.9;
            margin-top: 2px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-section {
            margin-bottom: 25px;
        }

        .menu-section-title {
            padding: 0 20px;
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #475569;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background: #f1f5f9;
            color: #1e40af;
            border-left-color: #1e40af;
        }

        .menu-item.active {
            background: #eff6ff;
            color: #1e40af;
            border-left-color: #1e40af;
            font-weight: 600;
        }

        .menu-item svg {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            fill: currentColor;
        }

        /* Main Content */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 25px;
            height: 25px;
            fill: white;
        }

        .logo-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .logo-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
        }

        .user-email {
            font-size: 12px;
            color: #718096;
        }

        .btn-logout {
            padding: 10px 20px;
            background: #e53e3e;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-logout:hover {
            background: #c53030;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(229, 62, 62, 0.4);
        }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        .welcome-section {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(30, 64, 175, 0.3);
        }

        .welcome-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .welcome-subtitle {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .stat-icon svg {
            width: 24px;
            height: 24px;
            fill: white;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
        }

        .stat-icon.red {
            background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
        }

        .content-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .content-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .content-text {
            color: #4a5568;
            line-height: 1.6;
        }

        /* Kalender Styles */
        .calendar-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .calendar-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }

        .calendar-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .calendar-nav button {
            background: #1e40af;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .calendar-nav button:hover {
            background: #1e3a8a;
        }

        .calendar-month {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            min-width: 150px;
            text-align: center;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #4a5568;
            padding: 12px;
            font-size: 14px;
        }

        .calendar-day {
            aspect-ratio: 1;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 80px;
            padding: 8px;
        }

        .calendar-day:hover {
            border-color: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
        }

        .calendar-day.other-month {
            opacity: 0.3;
        }

        .calendar-day.today {
            border-color: #1e40af;
            background: #eff6ff;
        }

        .calendar-day-number {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 4px;
        }

        .calendar-day-events {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
            justify-content: center;
            width: 100%;
        }

        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .event-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
        }

        /* Legend */
        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .legend-label {
            font-size: 13px;
            color: #4a5568;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d3748;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #718096;
            cursor: pointer;
            line-height: 1;
            padding: 0;
            width: 30px;
            height: 30px;
        }

        .modal-close:hover {
            color: #2d3748;
        }

        .penugasan-item {
            padding: 15px;
            border-left: 4px solid;
            background: #f7fafc;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .penugasan-jenis {
            font-weight: 700;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .penugasan-judul {
            font-size: 15px;
            color: #2d3748;
            margin-bottom: 6px;
        }

        .penugasan-detail {
            font-size: 13px;
            color: #718096;
            margin-top: 8px;
        }

        .penugasan-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <div class="sidebar-logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                    </svg>
                </div>
                <div class="sidebar-logo-text">
                    <div class="sidebar-logo-title">APIP Inspektorat</div>
                    <div class="sidebar-logo-subtitle">Puncak Jaya</div>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Menu Utama</div>
                <a href="{{ route('dashboard') }}" class="menu-item active">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    Dashboard
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Master Data</div>
                <a href="{{ route('pegawai.index') }}" class="menu-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                    </svg>
                    Data Pegawai
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Penugasan</div>
                <a href="{{ route('pengawasan.index') }}" class="menu-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    Pengawasan
                </a>
                <a href="{{ route('penugasan.index') }}" class="menu-item">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 6h-3V4c0-1.11-.89-2-2-2H9c-1.11 0-2 .89-2 2v2H4c-1.11 0-2 .89-2 2v11c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM9 4h6v2H9V4zm11 15H4v-2h16v2zm0-5H4V8h3v2h2V8h6v2h2V8h3v6z"/>
                    </svg>
                    Penugasan
                </a>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="logo-title">Manajemen Penugasan APIP</div>
                        <div class="logo-subtitle">Inspektorat Kabupaten Puncak Jaya</div>
                    </div>
                </div>
                <div class="user-section">
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-email">{{ Auth::user()->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-title">Selamat Datang, {{ Auth::user()->name }}! 👋</div>
                <div class="welcome-subtitle">Sistem Manajemen Penugasan APIP - Inspektorat Kabupaten Puncak Jaya</div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Total Pegawai</div>
                    <div class="stat-value">{{ \App\Models\Pegawai::count() }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Pengawasan Selesai</div>
                    <div class="stat-value">0</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Pengawasan Berjalan</div>
                    <div class="stat-value">0</div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon red">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path d="M20 6h-3V4c0-1.11-.89-2-2-2H9c-1.11 0-2 .89-2 2v2H4c-1.11 0-2 .89-2 2v11c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zM9 4h6v2H9V4zm11 15H4v-2h16v2zm0-5H4V8h3v2h2V8h6v2h2V8h3v6z"/>
                        </svg>
                    </div>
                    <div class="stat-label">Perjalanan Dinas</div>
                    <div class="stat-value">0</div>
                </div>
            </div>

            <!-- Kalender Penugasan -->
            <div class="calendar-container">
                <div class="calendar-header">
                    <h2 class="calendar-title">Kalender Penugasan</h2>
                    <div class="calendar-nav">
                        <button onclick="previousMonth()">‹ Prev</button>
                        <div class="calendar-month" id="currentMonth"></div>
                        <button onclick="nextMonth()">Next ›</button>
                    </div>
                </div>

                <div class="calendar-grid" id="calendarGrid">
                    <!-- Calendar will be generated by JavaScript -->
                </div>

                <div class="calendar-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ef4444;"></div>
                        <div class="legend-label">Audit</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #3b82f6;"></div>
                        <div class="legend-label">Reviu</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #10b981;"></div>
                        <div class="legend-label">Monitoring</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #f59e0b;"></div>
                        <div class="legend-label">Evaluasi</div>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #8b5cf6;"></div>
                        <div class="legend-label">Perjalanan Dinas Luar Daerah</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk detail penugasan -->
    <div id="penugasanModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Penugasan</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modalBody">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let penugasanData = [];

        const warnaJenis = {
            'Audit': '#ef4444',
            'Reviu': '#3b82f6',
            'Monitoring': '#10b981',
            'Evaluasi': '#f59e0b',
            'Perjalanan Dinas Luar Daerah': '#8b5cf6'
        };

        const statusColors = {
            'Direncanakan': '#3b82f6',
            'Berlangsung': '#f59e0b',
            'Selesai': '#10b981',
            'Dibatalkan': '#ef4444'
        };

        async function loadPenugasanData() {
            const bulan = currentDate.getMonth() + 1;
            const tahun = currentDate.getFullYear();

            try {
                const response = await fetch(`/api/kalender-data?bulan=${bulan}&tahun=${tahun}`);
                penugasanData = await response.json();
                renderCalendar();
            } catch (error) {
                console.error('Error loading penugasan data:', error);
                renderCalendar();
            }
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            // Update month display
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;

            // Clear calendar
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';

            // Add day headers
            const dayHeaders = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            dayHeaders.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.textContent = day;
                grid.appendChild(header);
            });

            // Get first day of month and number of days
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();

            // Add previous month's days
            for (let i = firstDay - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                const dayEl = createDayElement(day, month - 1, year, true);
                grid.appendChild(dayEl);
            }

            // Add current month's days
            for (let day = 1; day <= daysInMonth; day++) {
                const dayEl = createDayElement(day, month, year, false);
                grid.appendChild(dayEl);
            }

            // Add next month's days to fill grid
            const totalCells = grid.children.length - 7; // Minus headers
            const remainingCells = 42 - totalCells; // 6 rows * 7 days
            for (let day = 1; day <= remainingCells; day++) {
                const dayEl = createDayElement(day, month + 1, year, true);
                grid.appendChild(dayEl);
            }
        }

        function createDayElement(day, month, year, isOtherMonth) {
            const dayEl = document.createElement('div');
            dayEl.className = 'calendar-day';
            if (isOtherMonth) dayEl.classList.add('other-month');

            // Check if today
            const today = new Date();
            if (day === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                dayEl.classList.add('today');
            }

            const dayNumber = document.createElement('div');
            dayNumber.className = 'calendar-day-number';
            dayNumber.textContent = day;
            dayEl.appendChild(dayNumber);

            // Get penugasan for this day
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const dayPenugasan = getPenugasanForDate(dateStr);

            if (dayPenugasan.length > 0) {
                const eventsContainer = document.createElement('div');
                eventsContainer.className = 'calendar-day-events';

                // Group by jenis and show dots
                const jenisCounts = {};
                dayPenugasan.forEach(p => {
                    jenisCounts[p.jenis] = (jenisCounts[p.jenis] || 0) + 1;
                });

                Object.keys(jenisCounts).forEach(jenis => {
                    const dot = document.createElement('div');
                    dot.className = 'event-dot';
                    dot.style.backgroundColor = warnaJenis[jenis];
                    dot.title = `${jenis}: ${jenisCounts[jenis]}`;
                    eventsContainer.appendChild(dot);
                });

                dayEl.appendChild(eventsContainer);
                dayEl.onclick = () => showPenugasanModal(dateStr, dayPenugasan);
            }

            return dayEl;
        }

        function getPenugasanForDate(dateStr) {
            return penugasanData.filter(p => {
                const mulai = p.tanggal_mulai;
                const selesai = p.tanggal_selesai;
                return dateStr >= mulai && dateStr <= selesai;
            });
        }

        function showPenugasanModal(dateStr, penugasan) {
            const date = new Date(dateStr);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = date.toLocaleDateString('id-ID', options);

            document.getElementById('modalTitle').textContent = `Penugasan - ${formattedDate}`;

            let html = '';
            penugasan.forEach(p => {
                const warna = warnaJenis[p.jenis];
                const statusColor = statusColors[p.status];

                html += `
                    <div class="penugasan-item" style="border-left-color: ${warna};">
                        <div class="penugasan-jenis" style="color: ${warna};">${p.jenis}</div>
                        <div class="penugasan-judul">${p.judul}</div>
                        ${p.deskripsi ? `<div class="penugasan-detail">${p.deskripsi}</div>` : ''}
                        <div class="penugasan-detail">
                            📅 ${formatDate(p.tanggal_mulai)} - ${formatDate(p.tanggal_selesai)}
                        </div>
                        ${p.lokasi ? `<div class="penugasan-detail">📍 ${p.lokasi}</div>` : ''}
                        ${p.pegawai ? `<div class="penugasan-detail">👤 ${p.pegawai.nama}</div>` : ''}
                        <span class="penugasan-status" style="background: ${statusColor}; color: white;">
                            ${p.status}
                        </span>
                    </div>
                `;
            });

            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('penugasanModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('penugasanModal').classList.remove('active');
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadPenugasanData();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadPenugasanData();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('penugasanModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Initialize calendar
        loadPenugasanData();
    </script>
</body>
</html>

