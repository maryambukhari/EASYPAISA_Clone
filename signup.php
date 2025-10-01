<?php
// signup.php - User Signup Page with Simplified 2FA

session_start();
include 'db.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$signup_success = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $pin = $_POST['pin'] ?? '';
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);

    try {
        // Validate inputs
        if (empty($username) || empty($email) || empty($password) || empty($pin) || empty($phone)) {
            throw new Exception('All fields are required.');
        }
        if (!preg_match('/^\d{4}$/', $pin)) {
            throw new Exception('PIN must be exactly 4 digits.');
        }

        // Generate random 2FA secret (16 characters)
        $two_fa_secret = bin2hex(random_bytes(8)); // Generates a 16-character random string
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $pin_hash = password_hash($pin, PASSWORD_BCRYPT);

        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, pin_hash, phone, two_fa_secret) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password_hash, $pin_hash, $phone, $two_fa_secret]);

        // Create wallet
        $user_id = $conn->lastInsertId();
        $stmt = $conn->prepare("INSERT INTO wallets (user_id) VALUES (?)");
        $stmt->execute([$user_id]);

        $signup_success = [
            'message' => 'Signup successful! Copy the 2FA secret code below and save it securely.',
            'secret' => $two_fa_secret
        ];
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
    <title>Signup - Easypaisa Clone</title>
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
        .success { text-align: center; margin: 20px auto; max-width: 400px; background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; color: #005f20; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .secret { font-family: monospace; background: #f0f0f0; padding: 10px; border-radius: 5px; color: #333; }
        .error { color: #d32f2f; text-align: center; margin-bottom: 10px; }
    </style>
</head>
<body>
    <?php if ($signup_success): ?>
        <div class="success">
            <h2><?php echo htmlspecialchars($signup_success['message']); ?></h2>
            <p>2FA Secret Code: <span class="secret"><?php echo htmlspecialchars($signup_success['secret']); ?></span></p>
            <p>Copy this code and save it securely. You will need it to log in.</p>
            <button onclick="redirectToLogin()">Proceed to Login</button>
        </div>
    <?php elseif ($error_message): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <form method="POST">
            <h2>Signup</h2>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="pin" placeholder="PIN (4 digits)" required pattern="\d{4}" title="PIN must be 4 digits">
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <button type="submit">Signup</button>
        </form>
    <?php else: ?>
        <form method="POST">
            <h2>Signup</h2>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="pin" placeholder="PIN (4 digits)" required pattern="\d{4}" title="PIN must be 4 digits">
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <button type="submit">Signup</button>
        </form>
    <?php endif; ?>
    <div style="text-align: center;"><button onclick="redirectToHome()">Back to Home</button></div>
    <script>
        function redirectToHome() {
            location.href = 'index.php';
        }
        function redirectToLogin() {
            location.href = 'login.php';
        }
    </script>
</body>
</html>
