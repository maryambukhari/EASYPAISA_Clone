<?php
// login.php - User Login Page with Simplified 2FA

session_start();
include 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $two_fa_code = filter_input(INPUT_POST, 'two_fa_code', FILTER_SANITIZE_STRING);

    try {
        if (empty($email) || empty($password) || empty($two_fa_code)) {
            throw new Exception('All fields are required.');
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Verify 2FA code
            if ($two_fa_code === $user['two_fa_secret']) {
                $_SESSION['user_id'] = $user['id'];
                echo "<script>location.href = 'dashboard.php';</script>";
                exit;
            } else {
                $error_message = 'Invalid 2FA code. Please check the code you copied during signup.';
            }
        } else {
            $error_message = 'Invalid email or password.';
        }
    } catch (Exception $e) {
        $error_message = 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Easypaisa Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #00b140, #005f20); color: #fff; margin: 0; padding: 0; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        form { max-width: 400px; margin: 50px auto; background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; color: #005f20; box-shadow: 0 4px 8px rgba(0,0,0,0.2); animation: slideUp 1s ease-out; }
        @keyframes slideUp { from { transform: translateY(50px); } to { transform: translateY(0); } }
        input { width: 100%; padding: 15px; margin: 10px 0; border: 1px solid #00b140; border-radius: 10px; font-size: 1em; transition: border 0.3s; }
        input:focus { border: 1px solid #00802b; outline: none; }
        button { background: #00b140; color: #fff; border: none; padding: 15px; width: 100%; font-size: 1.2em; border-radius: 10px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #00802b; }
        .error { color: #d32f2f; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Login</h2>
        <?php if ($error_message): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="two_fa_code" placeholder="2FA Code (from Signup)" required>
        <button type="submit">Login</button>
    </form>
    <div style="text-align: center;"><button onclick="redirectToHome()">Back to Home</button></div>
    <script>
        function redirectToHome() {
            location.href = 'index.php';
        }
    </script>
</body>
</html>
