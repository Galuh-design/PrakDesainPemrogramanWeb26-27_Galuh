<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user'])) {
    require __DIR__ . '/../includes/koneksi.php'; 

   
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_COOKIE['remember_user']]);
    $userCookie = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userCookie) {
    
        $_SESSION['user_id'] = $userCookie['id'];
        $_SESSION['nama'] = $userCookie['nama'];
        $_SESSION['role'] = $userCookie['role'];
    }
    header('Location: ../index.php');
    exit;
}


if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}


$page_title = "Login";
include __DIR__ . '/../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
        <section>
            <h2>Login Petugas</h2>

            <?php if ($flash): ?>
                <p class="flash flash-<?php echo $flash['type']; ?>"><?php echo $flash['pesan']; ?></p>
            <?php endif; ?>

            <form method="post" action="proses_login.php">
                <p>
                    <label for="username">Username</label><br>
                    <input type="text" id="username" name="username" required>
                </p>
                <p>
                    <label for="password">Password</label><br>
                    <input type="password" id="password" name="password" required>
                    <br>
                     <input type="checkbox" name="remember" class="checkboxMember" id="remember"> 
                     <label for="remember">Remember me</label>
                     <br>
                </p>
                <p>
                    <button type="submit">Masuk</button>
                </p>
            </form>
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
