<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/customer_functions.php';
checkLogin(__DIR__ . '/../index.php');

$user = getCurrentUser();
if ((($user['role'] ?? '') === 'admin') && (($_GET['view'] ?? '') !== 'customer')) {
    header('Location: ../views/area.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trang chủ - GỬI XE</title>
    <link rel="stylesheet" href="/btl_nhom-13-demo/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #fff;
            overflow-x: hidden;
        }

        /* === NAVIGATION === */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: transparent;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        nav.scrolled {
            background: rgba(23, 40, 79, 0.95);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-img {
            width: 180px;
            height: 60px;
            transition: transform 0.3s;
        }

        .logo-img:hover {
            transform: scale(1.05);
        }

        .logo-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(255, 107, 53, 0.4));
        }

        .nav-links {
            display: flex;
            gap: 3rem;
            align-items: center;
        }

        .nav-links a {
            color: #cbd5e1;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 0.5rem 0;
            position: relative;
            transition: color 0.3s;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ff6b35, #ff8c42);
            transition: width 0.3s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #ff6b35;
        }

        .nav-links a:hover::after,
        .nav-links a.active::after {
            width: 100%;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: #e2e8f0;
            font-weight: 500;
            font-size: 14px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
            transition: transform 0.3s;
        }

        .user-avatar:hover {
            transform: scale(1.1);
        }

        .logout-btn {
            padding: 0.6rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: rgba(255, 107, 53, 0.2);
            border-color: #ff6b35;
            transform: translateY(-2px);
        }

        /* === MAIN CONTENT === */
        #mainContent {
            padding: 0;
            transition: opacity 0.3s;
            min-height: calc(100vh - 80px);
        }

        #mainContent._fading { opacity: 0.3; }
        #mainContent._visible { opacity: 1; }

        /* Fragment content styling */
        #mainContent .card,
        #mainContent .profile-card,
        #mainContent form.profile-form {
            max-width: 720px;
            margin: 120px auto 3rem;
            padding: 2.5rem;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        #mainContent .card h2,
        #mainContent .profile-card h2 {
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 2rem;
            text-align: center;
        }

        #mainContent .card label,
        #mainContent .profile-card label {
            display: block;
            margin-bottom: 0.5rem;
            color: #cbd5e1;
            font-weight: 600;
            font-size: 14px;
        }

        #mainContent .card input,
        #mainContent .card select,
        #mainContent .profile-card input,
        #mainContent .profile-card select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }

        #mainContent .card input:focus,
        #mainContent .card select:focus,
        #mainContent .profile-card input:focus,
        #mainContent .profile-card select:focus {
            outline: none;
            border-color: #ff6b35;
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        /* === TICKET CARDS GRID LAYOUT === */
        /* Tickets container - 3 columns grid */
        #mainContent > div:not(.hero):not(.features):not(.services):not(.stats):not(.cta):not(.card):not(.profile-card):not(.alert) {
            padding: 85px  !important;
            max-width: 1500px !important;
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 2rem !important;
        }

        /* Individual ticket card - HORIZONTAL layout (hình chữ nhật ngang) */
        #mainContent .ticket-card,
        #mainContent .modern-ticket-card {
            background: rgba(15, 23, 42, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            border: 2px solid rgba(255, 107, 53, 0.4) !important;
            border-radius: 16px !important;
            padding: 0 !important;
            margin: 0 !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            overflow: hidden !important;
            position: relative !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            min-height: 200px !important;
            height: auto !important;
            box-shadow: 0 10px 40px rgba(255, 107, 53, 0.2) !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        #mainContent .ticket-card:hover,
        #mainContent .modern-ticket-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 20px 60px rgba(255, 107, 53, 0.4) !important;
            border-color: rgba(255, 107, 53, 0.7) !important;
        }

        /* Top section - Orange header */
        .modern-ticket-logo,
        #mainContent .ticket-card > *:first-child {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%) !important;
            width: 100% !important;
            height: 100px !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 1rem 1.5rem !important;
            position: relative !important;
            flex-shrink: 0 !important;
            gap: 1rem !important;
        }

        /* Ticket perforation effect - horizontal */
        .modern-ticket-logo::after,
        #mainContent .ticket-card > *:first-child::after {
            content: '' !important;
            position: absolute !important;
            bottom: -1px !important;
            left: 0 !important;
            right: 0 !important;
            height: 15px !important;
            background-image: 
                radial-gradient(circle at 15px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 35px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 55px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 75px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 95px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 115px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 135px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 155px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 175px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 195px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 215px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 235px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 255px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 275px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 295px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 315px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 335px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 355px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 375px 8px, transparent 5px, #ff6b35 5px),
                radial-gradient(circle at 395px 8px, transparent 5px, #ff6b35 5px) !important;
            background-size: 20px 15px !important;
            background-repeat: repeat-x !important;
        }

        .modern-ticket-logo img,
        #mainContent .ticket-card > *:first-child img {
            width: 70px !important;
            height: auto !important;
            filter: brightness(0) invert(1) drop-shadow(0 4px 8px rgba(0,0,0,0.2)) !important;
            display: block !important;
        }

        .modern-ticket-badge {
            background: rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(10px) !important;
            padding: 0.5rem 1.2rem !important;
            border-radius: 20px !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            color: white !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            display: inline-block !important;
            text-align: center !important;
            white-space: nowrap !important;
        }

        /* Bottom section - Content area */
        .modern-ticket-content,
        #mainContent .ticket-card > *:nth-child(n+2) {
            flex: 1 !important;
            padding: 1.5rem !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            background: transparent !important;
            color: #fff !important;
            gap: 1rem !important;
        }

        .modern-ticket-id,
        #mainContent .ticket-card h3,
        #mainContent .ticket-card h2 {
            font-size: 16px !important;
            font-weight: 800 !important;
            color: #fff !important;
            letter-spacing: 2px !important;
            text-transform: uppercase !important;
            margin: 0 0 1rem 0 !important;
            line-height: 1.3 !important;
            text-align: center !important;
        }

        .modern-ticket-details {
            display: flex !important;
            flex-direction: column !important;
            gap: 0.6rem !important;
            flex: 1 !important;
        }

        .modern-ticket-item {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 1rem !important;
            padding: 0.4rem 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .modern-ticket-item:last-child {
            border-bottom: none !important;
        }

        .modern-ticket-label {
            font-size: 11px !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
            font-weight: 600 !important;
            letter-spacing: 0.5px !important;
        }

        .modern-ticket-value {
            font-size: 13px !important;
            color: #e2e8f0 !important;
            font-weight: 700 !important;
            text-align: right !important;
        }

        .modern-ticket-status {
            padding: 0.5rem 1.2rem !important;
            border-radius: 20px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            white-space: nowrap !important;
            display: block !important;
            width: 100% !important;
            text-align: center !important;
            margin-top: 0.5rem !important;
        }

        .modern-ticket-status.active {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #10b981 !important;
            border: 2px solid rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.2) !important;
        }

        .modern-ticket-status.expired {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #ef4444 !important;
            border: 2px solid rgba(239, 68, 68, 0.4) !important;
        }

        .modern-ticket-price {
            font-size: 28px !important;
            font-weight: 900 !important;
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            line-height: 1 !important;
            white-space: nowrap !important;
            margin-top: 0.8rem !important;
            text-align: center !important;
        }

        .modern-ticket-qr {
            font-size: 10px !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            font-weight: 600 !important;
            margin-top: 0.4rem !important;
            text-align: center !important;
        }

        /* Override text styles */
        #mainContent .ticket-card p,
        #mainContent .ticket-card div {
            color: #cbd5e1 !important;
            font-size: 13px !important;
            margin: 0 !important;
            line-height: 1.5 !important;
        }

        #mainContent .ticket-card strong {
            color: #fff !important;
            font-weight: 700 !important;
        }

        #mainContent #fragmentLoading {
            padding: 120px 2rem !important;
            text-align: center;
            color: #94a3b8;
            font-size: 18px;
            grid-column: 1 / -1 !important;
        }

        /* === HERO SECTION === */
        body .hero {
            min-height: 100vh;
            padding: 10rem 2rem 6rem;
            background: url('/btl_nhom-13-demo/images/nen1.png') no-repeat center center/cover !important;
            position: relative;
        }

        body .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15,23,42,0.65); /* màu tối, có thể chỉnh lại độ mờ */
            z-index: 0;
        }

        body .hero > .hero-container {
            position: relative;
            z-index: 1;
        }
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(255, 107, 53, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s infinite ease-in-out;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 30px) scale(1.1); }
        }

        .hero-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 64px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 0%, #ff6b35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: slideInLeft 1s ease-out;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-content p {
            font-size: 20px;
            line-height: 1.8;
            color: #cbd5e1;
            margin-bottom: 3rem;
            animation: slideInLeft 1s ease-out 0.2s both;
        }

        .hero-actions {
            display: flex;
            gap: 1.5rem;
            animation: slideInLeft 1s ease-out 0.4s both;
        }

        .btn {
            padding: 1rem 2.5rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
            position: relative;
            z-index: 1;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.6);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: #ff6b35;
            transform: translateY(-3px);
        }

        .hero-image {
            
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-image img {
            width: 390px;
            margin-bottom: -110px;
            margin-left: 170px;
        }

        .hero-image:hover img {
            transform: scale(1.02) rotateY(5deg);
        }

        /* === FEATURES SECTION === */
        .features {
            padding: 8rem 2rem;
            background: #1e293b;
            position: relative;
        }

        .features-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title h2 {
            font-size: 48px;
            font-weight: 900;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #fff 0%, #ff6b35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-title p {
            font-size: 20px;
            color: #94a3b8;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .feature-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            padding: 3rem;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            transform: translateY(40px);
        }

        .feature-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: rgba(255, 107, 53, 0.5);
            box-shadow: 0 20px 60px rgba(255, 107, 53, 0.2);
            background: rgba(255, 107, 53, 0.05);
        }

        .feature-number {
            font-size: 72px;
            font-weight: 900;
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            line-height: 1;
        }

        .feature-card h3 {
            font-size: 24px;
            margin-bottom: 1rem;
            color: #fff;
        }

        .feature-card p {
            color: #94a3b8;
            line-height: 1.7;
            font-size: 15px;
        }

        /* === SERVICES SECTION === */
        .services {
            padding: 8rem 2rem;
            background: #0f172a;
        }

        .services-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6rem;
            align-items: center;
        }

        .services-image {
            position: relative;
        }

        .services-image::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: 20px;
            bottom: 20px;
            border-radius: 24px;
            opacity: 0.3;
            z-index: 0;
        }

        .services-image img {
            width: 100%;
            border-radius: 24px;
            position: relative;
            z-index: 1;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        }

        .services-content h2 {
            font-size: 48px;
            font-weight: 900;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 0%, #ff6b35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .services-content p {
            font-size: 18px;
            color: #94a3b8;
            line-height: 1.8;
            margin-bottom: 3rem;
        }

        .services-list {
            list-style: none;
        }

        .services-list li {
            padding: 1.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 17px;
            color: #cbd5e1;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }

        .services-list li:hover {
            padding-left: 1rem;
            color: #ff6b35;
        }

        .services-list li::before {
            content: '';
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #ff6b35, #ff8c42);
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* === STATS SECTION === */
        .stats {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            position: relative;
            overflow: hidden;
        }

        .stats::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="40" stroke="white" stroke-width="1" fill="none" opacity="0.1"/></svg>');
            opacity: 0.3;
        }

        .stats-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .stat-item h3 {
            font-size: 56px;
            font-weight: 900;
            margin-bottom: 0.5rem;
            color: white;
        }

        .stat-item p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }

        /* === CTA SECTION === */
        .cta {
            padding: 8rem 2rem;
            background: #1e293b;
            text-align: center;
        }

        .cta-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .cta h2 {
            font-size: 48px;
            font-weight: 900;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, #fff 0%, #ff6b35 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta p {
            font-size: 20px;
            color: #94a3b8;
            margin-bottom: 3rem;
            line-height: 1.8;
        }

        /* === FOOTER === */
        footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 5rem 2rem 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 5rem;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-brand h3 {
            font-size: 28px;
            margin-bottom: 1rem;
            color: #fff;
        }

        .footer-brand p {
            line-height: 1.7;
        }

        .footer-section h4 {
            font-size: 18px;
            margin-bottom: 1.5rem;
            color: #ff8c42;
            font-weight: 700;
        }

        .footer-section a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .footer-section a:hover {
            color: #ff8c42;
            padding-left: 0.5rem;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* === MODAL === */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .modal {
            background: #1e293b;
            border-radius: 24px;
            max-width: 900px;
            width: 100%;
            margin: 20px;
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: scale(0.9);
            transition: transform 0.3s;
        }

        .modal-overlay.show .modal {
            transform: scale(1);
        }

        .modal-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
        }

        .modal-close {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 24px;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s;
        }

        .modal-close:hover {
            background: rgba(255, 107, 53, 0.3);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-body .card {
            background: transparent;
            box-shadow: none;
            border: none;
            max-width: 100%;
        }

        .form-row {
            margin-bottom: 1.5rem;
        }

        .form-row label {
            display: block;
            margin-bottom: 0.5rem;
            color: #fff;
            font-weight: 600;
        }

        .form-row input,
        .form-row select {
            width: 100%;
            padding: 0.875rem 1rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-row input,
        .form-row select,
        .form-row textarea {
            color: #fff !important;
        }
        }

        .form-row input:focus,
        .form-row select:focus {
            outline: none;
            border-color: #ff6b35;
            background: rgba(15, 23, 42, 0.8);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        #bookingMessage {
            padding: 1rem;
            border-radius: 12px;
            margin-top: 1rem;
            text-align: center;
            font-weight: 600;
        }

        @media (max-width: 1200px) {
            /* 2 tickets per row */
            #mainContent > div:not(.hero):not(.features):not(.services):not(.stats):not(.cta):not(.card):not(.profile-card) {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .hero-container,
            .services-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-container {
                grid-template-columns: 1fr 1fr;
            }

            .hero-content h1 {
                font-size: 48px;
            }
        }

        @media (max-width: 768px) {
            /* 1 ticket per row on mobile */
            #mainContent > div:not(.hero):not(.features):not(.services):not(.stats):not(.cta):not(.card):not(.profile-card) {
                grid-template-columns: 1fr !important;
                padding: 100px 1rem 2rem !important;
            }

            /* Stack logo on top for mobile */
            #mainContent .ticket-card,
            #mainContent .modern-ticket-card {
                flex-direction: column !important;
                min-height: auto !important;
            }

            .modern-ticket-logo,
            #mainContent .ticket-card > *:first-child {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                height: auto !important;
                padding: 1.5rem !important;
            }

            .modern-ticket-logo::after,
            #mainContent .ticket-card > *:first-child::after {
                display: none !important;
            }

            .nav-links {
                display: none;
            }

            .hero {
                padding: 8rem 1.5rem 4rem;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .footer-container {
                grid-template-columns: 1fr;
            }

            .section-title h2,
            .services-content h2,
            .cta h2 {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <!-- Hiển thị thông báo modal -->
    <?php include_once __DIR__ . '/../includes/notifications_display.php'; ?>
    
    <nav id="mainNav">
        <div class="nav-container">
            <div class="logo-section">
                <div class="logo-img">
                    <img src="/btl_nhom-13-demo/images/LOGOTRANG.png" alt="GỬI XE">
                </div>
            </div>
            <div class="nav-links">
                <a href="/btl_nhom-13-demo/views/customer_home.php" class="active">Trang chủ</a>
                <a href="/btl_nhom-13-demo/views/ticket/create_ticket.php">Đặt vé</a>
                <a href="/btl_nhom-13-demo/views/ticket.php">Lịch sử</a>
                <a href="/btl_nhom-13-demo/views/ticket/customer_tickets.php">Vé của tôi</a>
                <a href="/btl_nhom-13-demo/views/customer/edit_profile.php">Hồ sơ</a>
            </div>
            <div class="user-section">
                <span class="user-name">Xin chào, <?php echo htmlspecialchars($user['username'] ?? 'User'); ?></span>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['username'] ?? 'U', 0, 1)); ?>
                </div>
                <a href="/btl_nhom-13-demo/handle/logout_process.php" class="logout-btn">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <main id="mainContent">
        <section class="hero" id="home">
            <div class="hero-container">
                <div class="hero-content">
                    <h1>Dịch vụ gửi xe thông minh & an toàn</h1>
                    <p>Trải nghiệm công nghệ quản lý bãi xe hiện đại với hệ thống đặt vé trực tuyến. An toàn tuyệt đối, giá cả minh bạch, dịch vụ chuyên nghiệp 24/7.</p>
                    <div class="hero-actions">
                        <button id="openBookModal" class="btn btn-primary">Đặt vé ngay</button>
                        <a href="#services" class="btn btn-secondary">Khám phá dịch vụ</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="/btl_nhom-13-demo/images/phone.png" alt="Dịch vụ gửi xe">
                </div>
            </div>
        </section>

        <section class="features" id="features">
            <div class="features-container">
                <div class="section-title">
                    <h2>Tại sao chọn chúng tôi?</h2>
                    <p>Trải nghiệm dịch vụ gửi xe đẳng cấp với công nghệ hàng đầu</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-number">01</div>
                        <h3>Bảo mật tuyệt đối</h3>
                        <p>Hệ thống camera AI giám sát 24/7, bảo vệ chuyên nghiệp, đảm bảo an toàn tối đa cho xe của bạn.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-number">02</div>
                        <h3>Giá cả minh bạch</h3>
                        <p>Không phí ẩn, bảng giá rõ ràng, thanh toán linh hoạt với nhiều hình thức tiện lợi.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-number">03</div>
                        <h3>Đặt vé nhanh chóng</h3>
                        <p>Giao diện hiện đại, đặt vé online chỉ trong 30 giây, quản lý lịch sử dễ dàng.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-number">04</div>
                        <h3>Vị trí thuận lợi</h3>
                        <p>15 chi nhánh trải khắp thành phố, dễ dàng tiếp cận từ mọi khu vực.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-number">05</div>
                        <h3>Hỗ trợ 24/7</h3>
                        <p>Đội ngũ chuyên nghiệp luôn sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-number">06</div>
                        <h3>Ưu đãi hấp dẫn</h3>
                        <p>Chương trình khuyến mãi liên tục cho khách hàng thân thiết và gửi xe dài hạn.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="services" id="services">
            <div class="services-container">
                <div class="services-image">
                    <img src="/btl_nhom-13-demo/images/hehehe.png" alt="Dịch vụ gửi xe">
                </div>
                <div class="services-content">
                    <h2>Dịch vụ của chúng tôi</h2>
                    <p>Cung cấp giải pháp gửi xe toàn diện với nhiều hình thức linh hoạt, phù hợp với mọi nhu cầu của bạn.</p>
                    <ul class="services-list">
                        <li>Gửi xe theo giờ - Linh hoạt cho nhu cầu ngắn hạn</li>
                        <li>Gửi xe theo ngày - Phù hợp cho công tác, du lịch</li>
                        <li>Gửi xe theo tháng - Tiết kiệm cho nhu cầu dài hạn</li>
                        <li>Gửi xe theo năm - Ưu đãi đặc biệt cho khách thường xuyên</li>
                        <li>Dịch vụ rửa xe định kỳ - Giữ xe luôn sạch sẽ</li>
                        <li>Bảo dưỡng cơ bản - Chăm sóc xe toàn diện</li>
                    </ul>
                </div>
            </div>
        </section>

        <section class="stats" id="stats">
            <div class="stats-container">
                <div class="stat-item">
                    <h3 id="counter-customers">0</h3>
                    <p>Khách hàng tin tưởng</p>
                </div>
                <div class="stat-item">
                    <h3 id="counter-branches">0</h3>
                    <p>Chi nhánh toàn quốc</p>
                </div>
                <div class="stat-item">
                    <h3 id="counter-spaces">0</h3>
                    <p>Chỗ gửi xe</p>
                </div>
                <div class="stat-item">
                    <h3 id="counter-satisfaction">0%</h3>
                    <p>Độ hài lòng</p>
                </div>
            </div>
        </section>

        <section class="cta">
            <div class="cta-container">
                <h2>Bắt đầu ngay hôm nay</h2>
                <p>Đặt vé gửi xe chỉ trong vài phút. Trải nghiệm dịch vụ chuyên nghiệp và tiện lợi nhất.</p>
                <a href="/btl_nhom-13-demo/views/ticket/create_ticket.php" class="btn btn-primary">Đặt vé ngay</a>
            </div>
        </section>
    </main>

    <div id="bookingModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3>Đăng ký vé xe</h3>
                <button id="closeModal" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <?php
                $_backupGet = $_GET;
                $_GET['ajax'] = 1;
                $_GET['modal_full'] = 1;
                ob_start();
                include __DIR__ . '/ticket/create_ticket_customer.php';
                $frag = ob_get_clean();
                $_GET = $_backupGet;

                $cardHtml = '';
                if (preg_match('#<div\s+class="card"\s+style="max-width:\s*600px;">(.*?)</div>\s*</div>#si', $frag, $m)) {
                    $cardHtml = $m[0];
                } elseif (preg_match('#<form[^>]*id="bookingFormFragment"[^>]*>(.*?)</form>#si', $frag, $m2)) {
                    $cardHtml = '<div class="card"></div>';
                    $cardHtml = $m2[0];
                }

                if ($cardHtml) {
                    $cardHtml = str_replace('id="bookingFormFragment"', 'id="bookingForm"', $cardHtml);
                    $cardHtml = str_replace('id="fragmentCancel"', 'id="cancelBtn"', $cardHtml);
                    $cardHtml = preg_replace('#name="price"([^>]*?)>#', 'name="price"$1 id="ticketPrice">', $cardHtml, 1);
                    if (!preg_match('#id="ticketType"#i', $cardHtml)) {
                        $cardHtml = preg_replace('#(<select[^>]*name="ticket_type"[^>]*>)#i', '<select id="ticketType" name="ticket_type"$1', $cardHtml, 1);
                        if (!preg_match('#id="ticketType"#i', $cardHtml)) {
                            $cardHtml = preg_replace('#(<select[^>]*>)#i', '<select id="ticketType" $1', $cardHtml, 1);
                        }
                    }
                    $cardHtml = preg_replace('#\s*style="[^"]*"#i', '', $cardHtml);
                    $cardHtml = str_replace('style=""', '', $cardHtml);
                    echo $cardHtml;
                } else {
                    echo $frag;
                }
                ?>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <h3>GỬI XE</h3>
                <p>Giải pháp gửi xe thông minh, an toàn và tiện lợi hàng đầu Việt Nam. Chúng tôi cam kết mang đến dịch vụ tốt nhất cho khách hàng.</p>
            </div>
            <div class="footer-section">
                <h4>Dịch vụ</h4>
                <a href="#">Gửi xe theo giờ</a>
                <a href="#">Gửi xe theo ngày</a>
                <a href="#">Gửi xe theo tháng</a>
                <a href="#">Gửi xe theo năm</a>
            </div>
            <div class="footer-section">
                <h4>Hỗ trợ</h4>
                <a href="#">Hướng dẫn sử dụng</a>
                <a href="#">Câu hỏi thường gặp</a>
                <a href="#">Chính sách</a>
                <a href="#">Liên hệ</a>
            </div>
            <div class="footer-section">
                <h4>Tài khoản</h4>
                <a href="/btl_nhom-13-demo/views/customer/edit_profile.php">Hồ sơ cá nhân</a>
                <a href="/btl_nhom-13-demo/views/ticket/create_ticket.php">Đặt vé</a>
                <a href="/btl_nhom-13-demo/views/ticket.php">Lịch sử giao dịch</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 GỬI XE. All rights reserved.</p>
        </div>
    </footer>

    <script>
    (function() {
        // Scroll effect for nav
        const nav = document.getElementById('mainNav');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Feature cards reveal animation
        const observerOptions = {
            threshold: 0.2,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry, index) {
                if (entry.isIntersecting) {
                    setTimeout(function() {
                        entry.target.classList.add('visible');
                    }, index * 100);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(function(card) {
            observer.observe(card);
        });

        // Counter animation
        function animateCounter(id, target, suffix) {
            const element = document.getElementById(id);
            const duration = 2000;
            const steps = 60;
            const increment = target / steps;
            let current = 0;

            const timer = setInterval(function() {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current).toLocaleString('vi-VN') + (suffix || '');
            }, duration / steps);
        }

        // Start counter when stats section is visible
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateCounter('counter-customers', 10000);
                    animateCounter('counter-branches', 15);
                    animateCounter('counter-spaces', 5000);
                    animateCounter('counter-satisfaction', 99.9, '%');
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // Modal functionality
        const openBtn = document.getElementById('openBookModal');
        const overlay = document.getElementById('bookingModal');
        const closeBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        let msg = document.getElementById('bookingMessage');
        let form = null;

        function openModal() {
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(function() { setPriceFromType(); }, 10);
        }

        function closeModal() {
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            if (msg) {
                msg.style.display = 'none';
                msg.innerText = '';
            }
            try { 
                if (form) form.reset(); 
            } catch(e) {}
        }

        if (openBtn) openBtn.addEventListener('click', function() { 
            openModal(); 
            bindModalForm(); 
        });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
        });

        function bindModalForm() {
            form = overlay.querySelector('#bookingForm');
            if (!form || form._bound) return;
            form._bound = true;

            if (!msg) {
                msg = overlay.querySelector('#bookingMessage');
                if (!msg) {
                    msg = document.createElement('div');
                    msg.id = 'bookingMessage';
                    msg.style.marginTop = '12px';
                    msg.style.display = 'none';
                    const mb = overlay.querySelector('.modal-body');
                    if (mb) mb.appendChild(msg);
                }
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (msg) msg.style.display = 'none';

                const data = new FormData(form);
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/btl_nhom-13-demo/handle/ticket_process.php?action=create', true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                xhr.onload = function() {
                    try { 
                        var res = JSON.parse(xhr.responseText); 
                    } catch (err) { 
                        res = null; 
                    }
                    
                    if (xhr.status === 200 && res && res.success) {
                        if (msg) {
                            msg.style.display = 'block';
                            msg.style.background = 'rgba(16, 185, 129, 0.2)';
                            msg.style.color = '#10b981';
                            msg.style.border = '1px solid rgba(16, 185, 129, 0.3)';
                            msg.innerText = 'Yêu cầu đặt vé đã được tạo — Chờ thanh toán';
                        }
                        // After successful ticket creation (AJAX), create a payment record
                        // and show the QR page. Then redirect to tickets list.
                        try {
                            var ticket = res.ticket || {};
                            var ticketId = ticket.id || ticket.ticket_id || null;
                            var amount = ticket.price || ticket.amount || (document.getElementById('ticketPrice') && document.getElementById('ticketPrice').value) || 0;
                            if (ticketId) {
                                var f2 = new FormData();
                                f2.append('ticket_id', ticketId);
                                f2.append('amount', amount);
                                var xhr2 = new XMLHttpRequest();
                                xhr2.open('POST', '/btl_nhom-13-demo/handle/customer_payment.php', true);
                                xhr2.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                                xhr2.onload = function() {
                                    try { var d = JSON.parse(xhr2.responseText); } catch(e) { d = null; }
                                    if (xhr2.status === 200 && d && d.success && d.payment_id) {
                                        // Show QR inside modal
                                        showPaymentQRInModal(d);
                                        return; // do not immediately redirect
                                    }
                                    setTimeout(function() { window.location.href = '/btl_nhom-13-demo/views/ticket.php'; }, 800);
                                };
                                xhr2.onerror = function() { setTimeout(function() { window.location.href = '/btl_nhom-13-demo/views/ticket.php'; }, 800); };
                                xhr2.send(f2);
                                return;
                            }
                        } catch (e) {}
                        setTimeout(function() { 
                            window.location.href = '/btl_nhom-13-demo/views/ticket.php'; 
                        }, 1000);
                    } else {
                        if (msg) {
                            msg.style.display = 'block';
                            msg.style.background = 'rgba(239, 68, 68, 0.2)';
                            msg.style.color = '#ef4444';
                            msg.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                            const text = (res && res.message) ? res.message : 'Có lỗi xảy ra khi đăng ký vé.';
                            msg.innerText = text;
                        }
                    }
                };
                
                xhr.onerror = function() { 
                    if (msg) {
                        msg.style.display = 'block';
                        msg.style.background = 'rgba(239, 68, 68, 0.2)';
                        msg.style.color = '#ef4444';
                        msg.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                        msg.innerText = 'Lỗi kết nối';
                    }
                };
                
                xhr.send(data);
            });

            const ticketType = form.querySelector('#ticketType');
            if (ticketType) {
                ticketType.removeEventListener('change', setPriceFromType);
                ticketType.addEventListener('change', setPriceFromType);
            }
        }

        bindModalForm();

        function setPriceFromType() {
            const defaultPrices = { 'day': 10000, 'month': 200000, 'year': 2000000 };
            const t = document.getElementById('ticketType');
            const p = document.getElementById('ticketPrice');
            if (t && p) {
                const v = t.value || (t.options[t.selectedIndex] && t.options[t.selectedIndex].value);
                p.value = defaultPrices[v] || '';
            }
        }

        // Show payment QR inside the booking modal
        function showPaymentQRInModal(paymentData) {
            try {
                var modalBody = overlay.querySelector('.modal-body');
                if (!modalBody) return;
                // hide booking form if present
                var bookingForm = modalBody.querySelector('#bookingForm');
                if (bookingForm) bookingForm.style.display = 'none';

                var amountText = paymentData.amount ? (Number(paymentData.amount).toLocaleString() + ' đ') : '';
                var payloadText = paymentData.payload || ('THANHTOAN' + (paymentData.payment_id || ''));
                var qrUrl = paymentData.qr_url || '';

                var html = '\n<div id="paymentQrBox" style="text-align:center;color:#fff;padding:12px;">' +
                    '<h3 style="margin-bottom:8px">Quét mã để thanh toán</h3>' +
                    '<p>Số tiền: <strong>' + amountText + '</strong></p>' +
                    '<p>Ghi chú chuyển khoản: <strong>' + payloadText + '</strong></p>' +
                    '<img src="' + qrUrl + '" alt="QR" style="max-width:260px;margin:12px auto;display:block;border-radius:8px;" />' +
                    '<div style="margin-top:12px;display:flex;gap:8px;justify-content:center">' +
                        '<button id="paidDoneBtn" class="btn btn-primary">Tôi đã chuyển tiền</button>' +
                        '<button id="qrCloseBtn" class="btn btn-secondary">Đóng</button>' +
                    '</div>' +
                '</div>\n';

                var existing = modalBody.querySelector('#paymentQrBox');
                if (existing) existing.outerHTML = html; else modalBody.insertAdjacentHTML('beforeend', html);

                // bind buttons
                setTimeout(function() {
                    var done = modalBody.querySelector('#paidDoneBtn');
                    var close = modalBody.querySelector('#qrCloseBtn');
                    if (done) done.addEventListener('click', function() {
                        // notify server that user has transferred and await admin confirmation
                        try {
                            var pid = paymentData && paymentData.payment_id ? paymentData.payment_id : (modalBody.querySelector('input[name="payment_id"]') && modalBody.querySelector('input[name="payment_id"]').value);
                            if (!pid) {
                                alert('Không xác định giao dịch'); return;
                            }
                            var ax = new XMLHttpRequest();
                            var fd = new FormData();
                            fd.append('action', 'mark_user_transferred');
                            fd.append('payment_id', pid);
                            ax.open('POST', '/btl_nhom-13-demo/handle/payment_action.php', true);
                            ax.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                            ax.onload = function() {
                                try { var r = JSON.parse(ax.responseText); } catch(e) { r = null; }
                                if (ax.status === 200 && r && r.success) {
                                    alert('Thông báo đã được gửi. Vui lòng chờ admin xác nhận.');
                                    window.location.href = '/btl_nhom-13-demo/views/ticket.php';
                                } else {
                                    alert((r && r.message) ? r.message : 'Không thể gửi thông báo');
                                }
                            };
                            ax.onerror = function(){ alert('Lỗi kết nối'); };
                            ax.send(fd);
                        } catch(e){ alert('Lỗi'); }
                    });
                    if (close) close.addEventListener('click', function() {
                        var box = modalBody.querySelector('#paymentQrBox'); if (box) box.parentNode.removeChild(box);
                        var bookingForm = modalBody.querySelector('#bookingForm'); if (bookingForm) bookingForm.style.display = '';
                    });
                }, 50);
                // ensure modal is visible
                overlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            } catch (e) {}
        }

        // Intercept nav links
        document.querySelectorAll('.nav-links a').forEach(function(a) {
            const href = a.getAttribute('href') || '';
            if (href.indexOf('/views/ticket/create_ticket.php') !== -1 || 
                href.indexOf('/views/ticket/create_ticket_customer.php') !== -1) {
                a.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    if (openBtn) openBtn.click();
                });
            }
        });

        // Fragment loader
        const main = document.getElementById('mainContent');
        if (!main) return;

        function formatTicketCards() {
            const ticketCards = document.querySelectorAll('#mainContent .ticket-card, #mainContent .modern-ticket-card');
            
            ticketCards.forEach(function(card) {
                if (card.classList.contains('formatted')) return;
                
                let ticketType = 'Vé gửi xe';
                let ticketId = '';
                let price = '';
                let startDate = '';
                let endDate = '';
                let status = 'active';
                
                const h3 = card.querySelector('h3');
                if (h3) ticketType = h3.textContent.trim();
                
                const allText = card.textContent || card.innerText;
                
                const idMatch = allText.match(/(?:nv\d+|[A-Z0-9]{2,}-[A-Z0-9]+|\d{5,})/i);
                if (idMatch) ticketId = idMatch[0];
                
                const priceMatch = allText.match(/(\d+[.,\s]*\d*)\s*(?:đ|d|vnd)/i);
                if (priceMatch) price = priceMatch[1].replace(/[.,\s]/g, '');
                
                const startMatch = allText.match(/(?:Bắt đầu|Start|From)[:\s]*(\d{1,2}\/\d{1,2}\/\d{4})/i);
                if (startMatch) startDate = startMatch[1];
                
                const endMatch = allText.match(/(?:Kết thúc|End|To)[:\s]*(\d{1,2}\/\d{1,2}\/\d{4})/i);
                if (endMatch) endDate = endMatch[1];
                
                if (allText.match(/active|đang hoạt động|hoạt động/i)) status = 'active';
                else if (allText.match(/expired|hết hạn|inactive/i)) status = 'expired';
                
                if (ticketType === 'Vé gửi xe') {
                    if (allText.match(/tháng|month/i)) ticketType = 'Vé tháng';
                    else if (allText.match(/năm|year/i)) ticketType = 'Vé năm';
                    else if (allText.match(/ngày|day/i)) ticketType = 'Vé ngày';
                }
                
                const formattedPrice = price.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                
                if (!ticketId && !price) return;
                
                card.classList.add('formatted');
                
                const newCard = document.createElement('div');
                newCard.className = 'modern-ticket-card formatted';
                newCard.innerHTML = `
                    <div class="modern-ticket-logo">
                        <img src="/btl_nhom-13-demo/images/guxe-logo.png" alt="GỬI XE" onerror="this.src='/btl_nhom-13-demo/images/GU-XE-29-10-2025 (1).png'">
                        <div class="modern-ticket-badge">${ticketType}</div>
                    </div>
                    <div class="modern-ticket-content">
                        <div class="modern-ticket-id">${ticketId || 'N/A'}</div>
                        <div class="modern-ticket-details">
                            <div class="modern-ticket-item">
                                <span class="modern-ticket-label">Bắt đầu</span>
                                <span class="modern-ticket-value">${startDate || 'N/A'}</span>
                            </div>
                            <div class="modern-ticket-item">
                                <span class="modern-ticket-label">Kết thúc</span>
                                <span class="modern-ticket-value">${endDate || 'N/A'}</span>
                            </div>
                            <div class="modern-ticket-item">
                                <span class="modern-ticket-label">Loại vé</span>
                                <span class="modern-ticket-value">${ticketType}</span>
                            </div>
                        </div>
                        <div class="modern-ticket-status ${status === 'active' ? 'active' : 'expired'}">
                            ${status === 'active' ? '● Đang hoạt động' : '● Hết hạn'}
                        </div>
                        <div class="modern-ticket-price">${formattedPrice || '0'} đ</div>
                        <div class="modern-ticket-qr">Mã QR: ${ticketId || 'N/A'}</div>
                    </div>
                `;
                
                card.parentNode.replaceChild(newCard, card);
            });
        }

        function setActiveLinkByHref(href) {
            const links = document.querySelectorAll('.nav-links a');
            links.forEach(function(a) { a.classList.remove('active'); });
            links.forEach(function(a) { 
                if (a.getAttribute('href') && a.getAttribute('href').indexOf(href) !== -1) {
                    a.classList.add('active'); 
                }
            });
        }

        function runInlineScriptsFromHtml(html) {
            try {
                const tmp = document.createElement('div');
                tmp.innerHTML = html;
                const scripts = tmp.querySelectorAll('script');
                scripts.forEach(function(s) {
                    const ns = document.createElement('script');
                    if (s.src) ns.src = s.src;
                    else ns.text = s.textContent;
                    document.body.appendChild(ns);
                    document.body.removeChild(ns);
                });
            } catch(e) {}
        }

        async function loadFragment(url, push) {
            const fetchUrl = url.indexOf('?') === -1 ? (url + '?ajax=1') : (url + '&ajax=1');
            try {
                main.classList.remove('_visible');
                main.classList.add('_fading');

                const loading = document.createElement('div');
                loading.id = 'fragmentLoading';
                loading.innerText = 'Đang tải...';
                loading.style.padding = '24px';
                loading.style.textAlign = 'center';
                loading.style.color = '#94a3b8';
                main.innerHTML = '';
                main.appendChild(loading);

                const res = await fetch(fetchUrl, { 
                    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const text = await res.text();

                main.innerHTML = text;
                runInlineScriptsFromHtml(text);

                setTimeout(formatTicketCards, 200);
                setTimeout(formatTicketCards, 500);

                setTimeout(function() { 
                    main.classList.remove('_fading'); 
                    main.classList.add('_visible'); 
                }, 30);

                if (push) history.pushState({ajax: true, url: url}, '', url);
                setActiveLinkByHref(url);
            } catch(err) {
                console.error('Failed to load fragment', err);
                window.location.href = url;
            }
        }

        document.addEventListener('click', function(ev) {
            const a = ev.target && ev.target.closest && ev.target.closest('a');
            if (!a || !a.closest || !a.closest('.nav-container')) return;
            
            const href = a.getAttribute('href') || '';
            if (href.indexOf('edit_profile.php') !== -1) {
                ev.preventDefault();
                ev.stopPropagation();
                loadFragment(href, true);
                return;
            }
            if (href.indexOf('customer_tickets.php') !== -1) {
                ev.preventDefault();
                ev.stopPropagation();
                loadFragment(href, true);
                return;
            }
            if (href.indexOf('/views/ticket/create_ticket.php') !== -1 || 
                href.indexOf('/views/ticket/create_ticket_customer.php') !== -1) {
                ev.preventDefault();
                ev.stopPropagation();
                if (openBtn) openBtn.click();
                return;
            }
        }, true);

        window.addEventListener('popstate', function(ev) {
            if (ev.state && ev.state.url) {
                loadFragment(ev.state.url, false);
            } else {
                location.reload();
            }
        });

        window.__customer_loadFragment = loadFragment;
        setTimeout(function() { main.classList.add('_visible'); }, 120);

        const params = new URLSearchParams(window.location.search);
        const target = params.get('load');
        if (target) {
            const url = decodeURIComponent(target);
            if (window.__customer_loadFragment) {
                setTimeout(function() { 
                    window.__customer_loadFragment(url, true); 
                }, 50);
            } else {
                window.location.href = url;
            }
        }
    })();
    </script>
</body>
</html>