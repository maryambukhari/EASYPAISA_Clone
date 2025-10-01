<?php
// account.php - Account Management Page

session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href = 'login.php';</script>";
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pin = password_hash($_POST['new_pin'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET pin_hash = ? WHERE id = ?");
    $stmt->execute([$new_pin, $user_id]);
    echo "<script>alert('PIN updated!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - Easypaisa Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #00b140, #005f20); color: #fff; margin: 0; padding: 0; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        form { max-width: 400px; margin: 50px auto; background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; color: #005f20; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        input { width: 100%; padding: 15px; margin: 10px 0; border: 1px solid #00b140; border-radius: 10px; }
        button { background: #00b140; color: #fff; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #00802b; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Account Settings</h2>
        <input type="password" name="new_pin" placeholder="New PIN" required>
        <button type="submit">Update PIN</button>
    </form>
    <div style="text-align: center;"><button onclick="redirectToDashboard()">Back to Dashboard</button></div>
    <script>
        function redirectToDashboard() {
            location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
