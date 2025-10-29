<?php
require_once __DIR__ . '/../../functions/transaction_functions.php';
require_once __DIR__ . '/../../functions/service_functions.php';
require_once __DIR__ . '/../../functions/vehicle_functions.php';
require_once __DIR__ . '/../../functions/auth.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$currentUser = getCurrentUser();

$plate = isset($_GET['plate']) ? trim($_GET['plate']) : '';
$results = [];
if ($plate !== '') {
    $results = findTransactionsByPlate($plate);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tra cứu gửi xe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background:#f7fafc; margin:0; padding:0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 0 16px; }
        .card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:24px; }
        h1 { margin:0 0 6px; font-size:24px; }
        p.lead { margin:0 0 20px; color:#4a5568; }
        form { display:flex; gap:12px; flex-wrap:wrap; margin: 16px 0 8px; }
        input[type="text"] { flex:1; min-width:240px; padding:12px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:15px; background:#f9fbfd; }
        input[type="text"]:focus { outline:none; border-color:#667eea; background:#fff; box-shadow:0 0 0 3px rgba(102,126,234,.12); }
        button { padding:12px 18px; border:none; background:#2b6cb0; color:#fff; font-weight:600; border-radius:12px; cursor:pointer; }
        .muted { color:#718096; font-size:13px; }
        table { width:100%; border-collapse:collapse; margin-top:12px; }
        th, td { text-align:left; padding:12px; border-bottom:1px solid #edf2f7; }
        th { background:#f1f5f9; color:#2d3748; font-weight:600; }
        .empty { text-align:center; color:#718096; padding:16px 8px; }
        .topnav { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .link { text-decoration:none; color:#2b6cb0; font-weight:600; }
        .links { display:flex; gap:12px; align-items:center; }
    </style>
    <link rel="icon" href="/images/guixe.jpg" />
    <meta name="robots" content="noindex" />
    <meta name="description" content="Tra cứu tình trạng gửi xe theo biển số." />
    <base href="/btlnhom13/" />
    <!-- Adjust base if app is in subfolder -->
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="topnav">
                <div>
                    <h1>Tra cứu gửi xe</h1>
                    <p class="lead">Nhập biển số để xem lịch sử/phiên gửi xe.</p>
                </div>
                <div class="links">
                    <?php if ($currentUser): ?>
                        <span class="muted">Xin chào, <?php echo htmlspecialchars($currentUser['username']); ?></span>
                        <a class="link" href="handle/logout_process.php">Đăng xuất</a>
                    <?php else: ?>
                        <a class="link" href="views/customer/login.php">Đăng nhập KH</a>
                        <a class="link" href="views/customer/register.php">Đăng ký KH</a>
                    <?php endif; ?>
                    <a class="link" href="index.php">Trang quản trị</a>
                </div>
            </div>

            <form method="GET" action="views/customer/index.php">
                <input type="text" name="plate" placeholder="VD: 43A-123.45" value="<?php echo htmlspecialchars($plate); ?>" />
                <button type="submit">Tra cứu</button>
            </form>
            <div class="muted">Dữ liệu tham chiếu từ hệ thống hiện tại.</div>

            <?php if ($plate === ''): ?>
                <div class="empty">Nhập biển số để bắt đầu tra cứu.</div>
            <?php else: ?>
                <?php if (empty($results)): ?>
                    <div class="empty">Không tìm thấy giao dịch cho biển số "<?php echo htmlspecialchars($plate); ?>".</div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Biển số</th>
                                <th>Chủ xe</th>
                                <th>Dịch vụ</th>
                                <th>Mã DV</th>
                                <th>Số tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <td><?php echo (int)$row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['vehicle_plate']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vehicle_owner']); ?></td>
                                    <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['service_code']); ?></td>
                                    <td><?php echo number_format((float)$row['amount'], 0, ',', '.'); ?> đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>


