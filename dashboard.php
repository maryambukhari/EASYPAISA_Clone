<?php
// dashboard.php - User Dashboard with Wallet Management

session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href = 'login.php';</script>";
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT balance FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet = $stmt->fetch();

$balance = $wallet['balance'] ?? 0.00;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deposit'])) {
        $amount = $_POST['amount'];
        $new_balance = $balance + $amount;
        $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
        $stmt->execute([$new_balance, $user_id]);

        // Log transaction
        $stmt = $conn->prepare("INSERT INTO transactions (sender_wallet_id, amount, type, status) VALUES ((SELECT id FROM wallets WHERE user_id = ?), ?, 'deposit', 'completed')");
        $stmt->execute([$user_id, $amount]);

        echo "<script>alert('Deposit successful!'); location.reload();</script>";
    } elseif (isset($_POST['withdraw'])) {
        $amount = $_POST['amount'];
        if ($amount <= $balance) {
            $new_balance = $balance - $amount;
            $stmt = $conn->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
            $stmt->execute([$new_balance, $user_id]);

            // Log transaction
            $stmt = $conn->prepare("INSERT INTO transactions (sender_wallet_id, amount, type, status) VALUES ((SELECT id FROM wallets WHERE user_id = ?), ?, 'withdraw', 'completed')");
            $stmt->execute([$user_id, $amount]);

            echo "<script>alert('Withdrawal successful!'); location.reload();</script>";
        } else {
            echo "<script>alert('Insufficient balance!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Easypaisa Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #00b140, #005f20); color: #fff; margin: 0; padding: 0; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .dashboard { max-width: 800px; margin: 50px auto; background: rgba(255,255,255,0.9); padding: 30px; border-radius: 15px; color: #005f20; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        h2 { text-align: center; animation: bounce 1s; }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% { transform: translateY(0); } 40% { transform: translateY(-10px); } 60% { transform: translateY(-5px); } }
        .balance { font-size: 2em; text-align: center; margin: 20px 0; color: #00b140; }
        form { margin: 20px 0; }
        input { width: calc(100% - 30px); padding: 15px; margin: 10px 0; border: 1px solid #00b140; border-radius: 10px; }
        button { background: #00b140; color: #fff; border: none; padding: 15px; width: 100%; border-radius: 10px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #00802b; }
        .nav { display: flex; justify-content: space-around; margin-top: 30px; }
        .nav button { width: auto; padding: 10px 20px; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Your Dashboard</h2>
        <div class="balance">Balance: PKR <?php echo $balance; ?></div>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" required>
            <button type="submit" name="deposit">Deposit</button>
        </form>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" required>
            <button type="submit" name="withdraw">Withdraw</button>
        </form>
        <div class="nav">
            <button onclick="redirectToTransfer()">Money Transfer</button>
            <button onclick="redirectToBillPay()">Bill Payment</button>
            <button onclick="redirectToHistory()">Transaction History</button>
            <button onclick="redirectToAccount()">Account Settings</button>
            <button onclick="logout()">Logout</button>
        </div>
    </div>
    <script>
        function redirectToTransfer() { location.href = 'transfer.php'; }
        function redirectToBillPay() { location.href = 'billpay.php'; }
        function redirectToHistory() { location.href = 'history.php'; }
        function redirectToAccount() { location.href = 'account.php'; }
        function logout() { location.href = 'logout.php'; }
    </script>
</body>
</html>
