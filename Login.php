<?php
declare(strict_types=1);

require_once __DIR__ . '/security.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === 1) {
    header('Location: Main.php');
    exit;
}

$error = '';

try {
    $pdo = db();

    // เก็บ Hash ของ IP แทน IP จริง
    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $ipHash = hash_hmac('sha256', $ip, DB_PASS);

    // ล้างประวัติที่เก่ากว่า 2 วัน
    $pdo->exec(
        'DELETE FROM login_attempts
         WHERE attempted_at < (UTC_TIMESTAMP() - INTERVAL 2 DAY)'
    );

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            http_response_code(400);
            $error = 'คำขอไม่ถูกต้อง กรุณาโหลดหน้าใหม่';
        } else {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*)
                 FROM login_attempts
                 WHERE ip_hash = ?
                 AND attempted_at >=
                     (UTC_TIMESTAMP() - INTERVAL 15 MINUTE)'
            );

            $stmt->execute([$ipHash]);
            $attempts = (int) $stmt->fetchColumn();

            if ($attempts >= 5) {
                http_response_code(429);
                $error = 'ลองเข้าสู่ระบบเกินกำหนด กรุณารอ 15 นาที';
            } else {
                $username = trim(
                    (string) ($_POST['username'] ?? '')
                );

                $password = (string) ($_POST['password'] ?? '');

                $stmt = $pdo->prepare(
                    'SELECT id, username, password_hash
                     FROM users
                     WHERE id = 1 AND username = ?
                     LIMIT 1'
                );

                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if (
                    $user &&
                    password_verify(
                        $password,
                        $user['password_hash']
                    )
                ) {
                    // ล้างจำนวนครั้งที่ผิดหลัง Login สำเร็จ
                    $stmt = $pdo->prepare(
                        'DELETE FROM login_attempts WHERE ip_hash = ?'
                    );
                    $stmt->execute([$ipHash]);

                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int) $user['id'];
                    $_SESSION['username'] = (string) $user['username'];
                    $_SESSION['last_activity'] = time();
                    $_SESSION['csrf'] = bin2hex(random_bytes(32));

                    header('Location: Main.php');
                    exit;
                }

                // บันทึกเมื่อชื่อผู้ใช้หรือรหัสผ่านผิด
                $stmt = $pdo->prepare(
                    'INSERT INTO login_attempts
                     (ip_hash, attempted_at)
                     VALUES (?, UTC_TIMESTAMP())'
                );
                $stmt->execute([$ipHash]);

                usleep(random_int(200000, 500000));

                $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
            }
        }
    }
} catch (Throwable $e) {
    error_log('Login error: ' . $e->getMessage());
    http_response_code(503);
    $error = 'ระบบไม่พร้อมใช้งาน กรุณาตรวจสอบการตั้งค่า';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>เข้าสู่ระบบ</title>
<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    min-height: 100vh;
    display: grid;
    place-items: center;
    background: #101010;
    color: #eee;
    font-family: Arial, Tahoma, sans-serif;
}

.login-box {
    width: min(92%, 390px);
    padding: 30px;
    background: #1b1b1b;
    border: 1px solid #c9a43b;
    border-radius: 10px;
    box-shadow: 0 0 20px #000;
}

h1 {
    text-align: center;
    color: #d8b64c;
    font-size: 25px;
    margin-top: 0;
}

label {
    display: block;
    margin: 15px 0 7px;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #555;
    border-radius: 5px;
    background: #101010;
    color: white;
    font-size: 16px;
}

button {
    width: 100%;
    margin-top: 22px;
    padding: 12px;
    border: 0;
    border-radius: 5px;
    background: #c9a43b;
    color: #111;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #e0bd51;
}

.error {
    padding: 10px;
    border: 1px solid #8c3333;
    border-radius: 5px;
    background: #351717;
    line-height: 1.5;
}

.note {
    color: #aaa;
    font-size: 12px;
    text-align: center;
    margin-bottom: 0;
}
</style>
</head>
<body>
<main class="login-box">
    <h1>เข้าสู่ระบบ</h1>

    <?php if ($error !== ''): ?>
        <p class="error" role="alert">
            <?= htmlspecialchars(
                $error,
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            ) ?>
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['expired'])): ?>
        <p class="error">Session หมดอายุ กรุณาเข้าสู่ระบบใหม่</p>
    <?php endif; ?>

    <form method="post" action="Login.php">
        <input
            type="hidden"
            name="csrf"
            value="<?= htmlspecialchars(
                csrf_token(),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <label for="username">ชื่อผู้ใช้</label>
        <input
            id="username"
            name="username"
            type="text"
            maxlength="64"
            autocomplete="username"
            required
        >

        <label for="password">รหัสผ่าน</label>
        <input
            id="password"
            name="password"
            type="password"
            maxlength="1024"
            autocomplete="current-password"
            required
        >

        <button type="submit">เข้าสู่ระบบ</button>
    </form>

    <p class="note">Private Access • ไม่มีระบบสมัครสมาชิก</p>
</main>
</body>
</html>
