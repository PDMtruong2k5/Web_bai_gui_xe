<?php
/**
 * Hiển thị thông báo cho người dùng dạng modal
 * Include file này trong trang customer_home.php
 */

require_once __DIR__ . '/../functions/notification_functions.php';

$activeNotifications = getActiveNotifications();

// Kiểm tra xem người dùng đã tắt thông báo hay chưa (lưu trong localStorage)
?>

<?php if (!empty($activeNotifications)): ?>
<div id="notificationModal" class="notification-modal">
    <div class="notification-modal-backdrop"></div>
    <div class="notification-modal-content">
        <?php $notif = $activeNotifications[0]; // Hiển thị thông báo đầu tiên ?>
        <div class="notification-modal-header">
            <h2>
                <?php
                    $icons = [
                        'info' => '<i class="fas fa-info-circle"></i>',
                        'success' => '<i class="fas fa-check-circle"></i>',
                        'warning' => '<i class="fas fa-exclamation-circle"></i>',
                        'error' => '<i class="fas fa-times-circle"></i>'
                    ];
                    echo $icons[$notif['type']] ?? '';
                ?>
                <span><?php echo htmlspecialchars($notif['title']); ?></span>
            </h2>
            <button class="notification-modal-close" onclick="closeNotification()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="notification-modal-body">
            <div class="notification-type-badge notification-type-<?php echo $notif['type']; ?>">
                <?php 
                    $types = ['info' => 'Thông tin', 'success' => 'Thành công', 'warning' => 'Cảnh báo', 'error' => 'Lỗi'];
                    echo $types[$notif['type']] ?? $notif['type'];
                ?>
            </div>
            <p class="notification-modal-message">
                <?php echo nl2br(htmlspecialchars($notif['message'])); ?>
            </p>
        </div>
        
        <div class="notification-modal-footer">
            <label class="notification-checkbox-label">
                <input type="checkbox" id="notificationHideHour" onchange="setHideTime()">
                <span>Tắt trong 1 giờ</span>
            </label>
            <button class="notification-btn-close" onclick="closeNotification()">Đóng</button>
        </div>
    </div>
</div>

<style>
.notification-modal {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.notification-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(10px);
}

.notification-modal-content {
    position: relative;
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
    width: 90%;
    max-width: 520px;
    animation: modalPopIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

@keyframes modalPopIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.notification-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 28px 32px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 107, 53, 0.1);
}

.notification-modal-header h2 {
    display: flex;
    align-items: center;
    gap: 14px;
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    margin: 0;
}

.notification-modal-header i {
    font-size: 28px;
    color: #ff6b35;
}

.notification-modal-close {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #cbd5e1;
    font-size: 24px;
    cursor: pointer;
    padding: 0;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.3s;
}

.notification-modal-close:hover {
    background: rgba(255, 107, 53, 0.2);
    color: #ff6b35;
    transform: rotate(90deg);
}

.notification-modal-body {
    padding: 32px;
}

.notification-type-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.notification-type-info {
    background: rgba(59, 130, 246, 0.2);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, 0.3);
}

.notification-type-success {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.notification-type-warning {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.notification-type-error {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.notification-modal-message {
    font-size: 16px;
    line-height: 1.7;
    color: #cbd5e1;
    margin: 0;
    white-space: pre-wrap;
    word-break: break-word;
}

.notification-modal-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 32px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}

.notification-checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    color: #94a3b8;
    user-select: none;
    transition: color 0.3s;
}

.notification-checkbox-label:hover {
    color: #cbd5e1;
}

.notification-checkbox-label input {
    cursor: pointer;
    width: 20px;
    height: 20px;
    accent-color: #ff6b35;
}

.notification-btn-close {
    padding: 12px 28px;
    background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 8px 24px rgba(255, 107, 53, 0.3);
}

.notification-btn-close:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(255, 107, 53, 0.4);
}

.notification-modal.hidden {
    display: none;
}

@media (max-width: 640px) {
    .notification-modal-content {
        width: 95%;
        margin: 16px;
    }

    .notification-modal-header {
        padding: 24px 20px;
    }

    .notification-modal-header h2 {
        font-size: 18px;
    }

    .notification-modal-body {
        padding: 24px;
    }

    .notification-modal-footer {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }

    .notification-checkbox-label {
        order: 2;
        justify-content: center;
    }

    .notification-btn-close {
        width: 100%;
    }
}
</style>

<script>
// Kiểm tra xem thông báo có bị tắt trong 1 giờ không
function checkNotificationHidden() {
    const hideTime = localStorage.getItem('notificationHideTime');
    const notificationId = '<?php echo $notif['id']; ?>';
    
    if (hideTime) {
        const currentTime = new Date().getTime();
        const hideUntil = parseInt(hideTime);
        
        if (currentTime < hideUntil) {
            document.getElementById('notificationModal').classList.add('hidden');
            return true;
        } else {
            localStorage.removeItem('notificationHideTime');
        }
    }
    return false;
}

// Tắt thông báo
function closeNotification() {
    const modal = document.getElementById('notificationModal');
    modal.style.animation = 'modalPopOut 0.3s ease forwards';
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Tắt trong 1 giờ
function setHideTime() {
    const checkbox = document.getElementById('notificationHideHour');
    if (checkbox.checked) {
        // Tắt trong 1 giờ = 3600000 milliseconds
        const hideUntil = new Date().getTime() + (60 * 60 * 1000);
        localStorage.setItem('notificationHideTime', hideUntil.toString());
    } else {
        localStorage.removeItem('notificationHideTime');
    }
}

// Tắt backdrop
document.addEventListener('DOMContentLoaded', function() {
    const backdrop = document.querySelector('.notification-modal-backdrop');
    if (backdrop) {
        backdrop.addEventListener('click', closeNotification);
    }
    
    // Kiểm tra xem có bị tắt trong 1 giờ không
    checkNotificationHidden();
});

// Thêm keyframe animation
const style = document.createElement('style');
style.textContent = `
    @keyframes modalPopOut {
        to {
            opacity: 0;
            transform: scale(0.8);
        }
    }
`;
document.head.appendChild(style);
</script>

<?php endif; ?>
