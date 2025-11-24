<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
require_once __DIR__ . '/../functions/auth.php';
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Hệ thống Quản lý Bãi Gửi Xe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/btl_nhom-13-demo/css/main.css">
    <link rel="stylesheet" href="/btl_nhom-13-demo/css/header.css">
    <link rel="stylesheet" href="/btl_nhom-13-demo/css/theme-switcher.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Minimal inline reset only; full styles live in /css/main.css and /css/header.css */
        html, body { height: 100%; }
        img { max-width: 100%; display: block; }
        /* Force remove all overlays and backdrops that may be blocking interaction */
        .modal-backdrop { display: none !important; visibility: hidden !important; }
        .modal-open { overflow: auto !important; }
        .offcanvas-backdrop { display: none !important; visibility: hidden !important; }
        .fade { opacity: 1 !important; }
    </style>
</head>
<body>
    <header class="header">
        <div class="header__container">
            <div class="header__left">
                <button class="header__menu-btn" id="sidebarToggle" title="Menu" aria-label="Toggle menu">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="/btl_nhom-13-demo/<?php echo ($currentUser['role'] ?? '') === 'admin' ? 'views/area.php' : 'views/customer_home.php'; ?>" class="header__logo" aria-label="Hệ thống Quản lý Bãi Gửi Xe">
                    <div class="header__logo-img">
                        <img src="/btl_nhom-13-demo/images/guxe-logo.png" alt="GU-XE Logo">
                    </div>
                    <!-- visually hidden brand text kept for accessibility -->
                    <span class="visually-hidden">Hệ thống Quản lý Bãi Gửi Xe</span>
                </a>
            </div>
            
            <div class="header__actions">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle-btn" class="theme-toggle-btn" onclick="toggleTheme()" title="Chuyển sang chế độ tối">
                    <i class="fas fa-moon"></i>
                </button>
                
                <?php if ($currentUser): ?>
                <div class="header__user" title="<?php echo htmlspecialchars($currentUser['username']); ?>">
                    <a href="/btl_nhom-13-demo/views/customer/profile.php" class="header__avatar-link" aria-label="Profile">
                        <img src="<?php echo ($currentUser['role'] ?? '') === 'admin' ? '/btl_nhom-13-demo/images/admin-avatar.png' : '/btl_nhom-13-demo/images/guixe.jpg'; ?>" alt="Avatar" class="header__avatar">
                    </a>
                    <div class="header__user-info">
                        <span class="header__username"><?php echo htmlspecialchars(ucfirst($currentUser['username'])); ?></span>
                        <?php if (!empty($currentUser['role'])): ?>
                            <span class="header__role-badge"><?php echo htmlspecialchars(strtoupper($currentUser['role'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="/btl_nhom-13-demo/handle/logout_process.php" class="btn btn-sm btn-ghost" title="Đăng xuất">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Floating menu button (visible when sidebar is hidden) -->
    <button id="menuFloating" class="menu-floating" aria-label="Mở menu" title="Mở menu">
        <i class="fas fa-bars"></i>
    </button>

    <?php if ($currentUser): ?>
    
    <?php // If we're forcing the customer view (or the user is a customer), render a simpler top nav and hide the admin sidebar ?>
    <?php $isCustomerView = (!empty($forceCustomerView) || (($currentUser['role'] ?? '') === 'customer')); ?>
    <?php if ($isCustomerView): ?>
        <!-- Customer top navigation (matches customer_home.php) -->
        <nav>
            <div class="nav-container">
                <div class="logo-section">
                    <div class="logo-img">
                        <a href="/btl_nhom-13-demo/views/customer_home.php">
                            <img src="/btl_nhom-13-demo/images/GU-XE-29-10-2025 (1).png" alt="GU-XE">
                        </a>
                    </div>
                </div>
                <div class="nav-links">
                    <a href="/btl_nhom-13-demo/views/customer_home.php" class="<?php echo strpos($_SERVER['PHP_SELF'], 'customer_home.php') !== false ? 'active' : ''; ?>">Trang chủ</a>
                    <a href="/btl_nhom-13-demo/views/ticket/create_ticket_customer.php">Đặt vé</a>
                    <a href="/btl_nhom-13-demo/views/ticket.php">Lịch sử</a>
                    <a href="/btl_nhom-13-demo/views/customer/edit_profile.php">Hồ sơ</a>
                </div>
                <div class="user-section">
                    <span class="user-name">Xin chào, <?php echo htmlspecialchars($currentUser['username'] ?? ''); ?></span>
                    <div class="user-avatar"><?php echo strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)); ?></div>
                    <a href="/btl_nhom-13-demo/handle/logout_process.php" title="Đăng xuất" style="margin-left:8px;color:#374151;text-decoration:none;border:1px solid #e5e7eb;padding:8px 10px;border-radius:8px;">Đăng xuất</a>
                </div>
            </div>
        </nav>

        <style>
        /* Minimal subset of customer_home nav styles so header matches exactly */
        .nav-container{max-width:1400px;margin:0 auto;padding:0 2rem;display:flex;align-items:center;justify-content:space-between;height:80px}
        .logo-img{width:160px;height:60px}
        .logo-img img{width:100%;height:100%;object-fit:cover}
        .nav-links{display:flex;gap:2rem;align-items:center}
        .nav-links a{color:#374151;text-decoration:none;font-weight:500;font-size:15px;padding:8px 0;border-bottom:2px solid transparent}
        .nav-links a:hover,.nav-links a.active{color:#ff6b35;border-bottom-color:#ff6b35}
        .user-section{display:flex;align-items:center;gap:16px}
        .user-name{color:#6b7280;font-size:15px}
        .user-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#ff6b35 0%,#ff8c42 100%);display:flex;align-items:center;justify-content:center;color:white;font-weight:700}
        @media (max-width:768px){.nav-links{display:none}}
        </style>

        <main class="main" id="mainContent">
            <div class="container">
                <?php if (isset($pageTitle)): ?>
                <div class="breadcrumb">
                    <i class="fas fa-home"></i>
                    <a href="/btl_nhom-13-demo/views/<?php echo ($currentUser['role'] ?? '') === 'admin' ? 'area.php' : 'customer_home.php'; ?>">Trang chủ</a>
                    <span class="breadcrumb-separator">/</span>
                    <span><?php echo $pageTitle; ?></span>
                </div>
                <?php endif; ?>
    <?php else: ?>
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar__nav">
                <?php if ((($currentUser['role'] ?? '') === 'admin') && empty($forceCustomerView)): ?>
            <a href="/btl_nhom-13-demo/views/customer_home.php?view=customer" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'customer_home.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Trang chủ khách hàng</span>
            </a>
            <a href="/btl_nhom-13-demo/views/area.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'area.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-map-marker-alt"></i>
                <span>Quản lý khu vực</span>
            </a>
            <a href="/btl_nhom-13-demo/views/ticket.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'ticket.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i>
                <span>Quản lý vé</span>
            </a>
            <a href="/btl_nhom-13-demo/views/ticket/ticket_price_manage.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'ticket_price_manage.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-tag"></i>
                <span>Giá vé & khuyến mãi</span>
            </a>
            <a href="/btl_nhom-13-demo/views/customer.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'customer.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Quản lý khách hàng</span>
            </a>
            <a href="/btl_nhom-13-demo/views/transaction/manage_payments.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'manage_payments.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i>
                <span>Doanh thu</span>
            </a>
            <a href="/btl_nhom-13-demo/views/transaction/payment_settings.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'payment_settings.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Cấu hình thanh toán</span>
            </a>
            <a href="/btl_nhom-13-demo/views/notification.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'notification.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-bell"></i>
                <span>Quản lý thông báo</span>
            </a>
            <?php else: ?>
            <a href="/btl_nhom-13-demo/views/customer_home.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'customer_home.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Trang chủ</span>
            </a>
            <a href="/btl_nhom-13-demo/views/ticket/create_ticket_customer.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'create_ticket_customer.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i>
                <span>Đặt vé</span>
            </a>
            <a href="/btl_nhom-13-demo/views/customer/profile.php" class="sidebar__link <?php echo strpos($_SERVER['PHP_SELF'], 'profile.php') !== false ? 'active' : ''; ?>">
                <i class="fas fa-user"></i>
                <span>Thông tin cá nhân</span>
            </a>
            <?php endif; ?>
        </nav>
    </aside>

    <main class="main" id="mainContent">
        <div class="container">
            <?php if (isset($pageTitle)): ?>
            <div class="breadcrumb">
                <i class="fas fa-home"></i>
                <a href="/btl_nhom-13-demo/views/<?php echo ($currentUser['role'] ?? '') === 'admin' ? 'area.php' : 'customer_home.php'; ?>">Trang chủ</a>
                <span class="breadcrumb-separator">/</span>
                <span><?php echo $pageTitle; ?></span>
            </div>
            <?php endif; ?>
    <?php endif; ?>
    <?php endif; ?>

    <script>
        // Simple sidebar toggle - desktop only
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });
        }
        
        // Close sidebar on nav link click
        document.querySelectorAll('.sidebar__link').forEach(link => {
            link.addEventListener('click', function() {
                if (sidebar && window.innerWidth <= 1024) {
                    sidebar.classList.remove('active');
                }
            });
        });
    </script>

</body>
</html>