<?php
// billpay.php - Bill Payment Page

session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href = 'login.php';</script>";
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, balance FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet = $stmt->fetch();
$wallet_id = $wallet['id'];
$balance = $wallet['balance'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $provider = $_POST['provider'];
    $bill_reference = $_POST['bill_reference'];
    $amount = $_POST['amount'];

    if ($amount <= $balance) {
        $new_balance = $balance - $amount;
        $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $wallet_id]);

        // Log bill
        $stmt = $conn->prepare("INSERT INTO bills (user_id, provider, bill_reference, amount, paid_at, status) VALUES (?, ?, ?, ?, NOW(), 'paid')");
        $stmt->execute([$user_id, $provider, $bill_reference, $amount]);

        // Log transaction
        $stmt = $conn->prepare("INSERT INTO transactions (sender_wallet_id, amount, type, status) VALUES (?, ?, 'bill_payment', 'completed')");
        $stmt->execute([$wallet_id, $amount]);

        echo "<script>alert('Bill paid successfully!'); location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Insufficient balance!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill Payment - Easypaisa Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #00b140, #005f20); color: #fff; margin: 0; padding: 0; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        form { max-width: 400px; margin: 50px auto; background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; color: #005f20; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        select, input { width: 100%; padding: 15px; margin: 10px 0; border: 1px solid #00b140; border-radius: 10px; }
        button { background: #00b140; color: #fff; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #00802b; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Bill Payment</h2>
        <select name="provider" required>
            <option value="">Select Provider</option>
            <option value="electricity">Electricity</option>
            <option value="gas">Gas</option>
            <option value="water">Water</option>
            <option value="mobile_recharge">Mobile Recharge</option>
            <option value="subscription">Subscription</option>
        </select>
        <input type="text" name="bill_reference" placeholder="Bill Reference" required>
        <input type="number" name="amount" placeholder="Amount" required>
        <button type="submit">Pay Bill</button>
    </form>
    <div style="text-align: center;"><button onclick="redirectToDashboard()">Back to Dashboard</button></div>
    <script>
        function redirectToDashboard() {
            location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
