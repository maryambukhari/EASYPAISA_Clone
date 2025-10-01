<?php
// transfer.php - Money Transfer Page

session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href = 'login.php';</script>";
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$sender_wallet_id = $stmt->fetch()['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_phone = $_POST['receiver_phone'];
    $amount = $_POST['amount'];
    $scheduled = isset($_POST['scheduled']) ? $_POST['scheduled'] : null;

    // Find receiver
    $stmt = $conn->prepare("SELECT u.id AS user_id, w.id AS wallet_id FROM users u JOIN wallets w ON u.id = w.user_id WHERE u.phone = ?");
    $stmt->execute([$receiver_phone]);
    $receiver = $stmt->fetch();

    if ($receiver) {
        // Check balance
        $stmt = $conn->prepare("SELECT balance FROM wallets WHERE id = ?");
        $stmt->execute([$sender_wallet_id]);
        $balance = $stmt->fetch()['balance'];

        if ($amount <= $balance) {
            // Update sender
            $new_sender_balance = $balance - $amount;
            $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
            $stmt->execute([$new_sender_balance, $sender_wallet_id]);

            // Update receiver
            $stmt = $conn->prepare("SELECT balance FROM wallets WHERE id = ?");
            $stmt->execute([$receiver['wallet_id']]);
            $receiver_balance = $stmt->fetch()['balance'];
            $new_receiver_balance = $receiver_balance + $amount;
            $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
            $stmt->execute([$new_receiver_balance, $receiver['wallet_id']]);

            // Log transaction
            $stmt = $conn->prepare("INSERT INTO transactions (sender_wallet_id, receiver_wallet_id, amount, type, status, scheduled_at) VALUES (?, ?, ?, 'transfer', 'completed', ?)");
            $stmt->execute([$sender_wallet_id, $receiver['wallet_id'], $amount, $scheduled]);

            // Simple fraud detection: if amount > 10000, set score
            $fraud_score = $amount > 10000 ? 50.00 : 0.00;
            $stmt = $conn->prepare("UPDATE transactions SET fraud_score = ? WHERE id = LAST_INSERT_ID()");
            $stmt->execute([$fraud_score]);

            echo "<script>alert('Transfer successful!'); location.href = 'dashboard.php';</script>";
        } else {
            echo "<script>alert('Insufficient balance!');</script>";
        }
    } else {
        echo "<script>alert('Receiver not found!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Transfer - Easypaisa Clone</title>
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
        <h2>Money Transfer</h2>
        <input type="tel" name="receiver_phone" placeholder="Receiver Phone" required>
        <input type="number" name="amount" placeholder="Amount" required>
        <input type="datetime-local" name="scheduled" placeholder="Schedule (optional)">
        <button type="submit">Transfer</button>
    </form>
    <div style="text-align: center;"><button onclick="redirectToDashboard()">Back to Dashboard</button></div>
    <script>
        function redirectToDashboard() {
            location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
