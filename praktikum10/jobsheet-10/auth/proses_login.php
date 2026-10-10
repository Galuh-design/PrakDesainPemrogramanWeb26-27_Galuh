<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/koneksi.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['role'] = $user['role'];
    if ($remember) {
        setcookie('remember_user', $user['id'], time() + (86400 * 30), "/", "", false, true);
    }
    header('Location: ../index.php');
    exit;
}

$_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Username atau password salah.'];
header('Location: login.php');

if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = [];
}

$max_attempts = 3; 


if (isset($_SESSION['failed_attempts'][$username]) && $_SESSION['failed_attempts'][$username] >= $max_attempts) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => 'Terlalu banyak percobaan login yang gagal. Akun Anda diblokir sementara.'
    ];
    header('Location: login.php');
    exit;
}

$login_gagal = true; 

if ($login_gagal) {
    if (!isset($_SESSION['failed_attempts'][$username])) {
        $_SESSION['failed_attempts'][$username] = 0;
    }
    $_SESSION['failed_attempts'][$username]++;

    $sisa_percobaan = $max_attempts - $_SESSION['failed_attempts'][$username];

    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => "Username atau password salah! Sisa percobaan: {$sisa_percobaan}"
    ];
    header('Location: login.php');
    exit;
} else {
    unset($_SESSION['failed_attempts'][$username]);
    header('Location: ../index.php');
    exit;
}

