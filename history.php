<?php
// history.php - Transaction History Page

session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href = 'login.php';</script>";
    exit;
}
include 'db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallet_id = $stmt->fetch()['id'];

$stmt = $conn->prepare("SELECT * FROM transactions WHERE sender_wallet_id = ? OR receiver_wallet_id = ? ORDER BY created_at DESC");
$stmt->execute([$wallet_id, $wallet_id]);
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History - Easypaisa Clone</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #00b140, #005f20); color: #fff; margin: 0; padding: 0; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        table { width: 80%; margin: 50px auto; background: rgba(255,255,255,0.9); border-collapse: collapse; color: #005f20; box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #00b140; }
        th { background: #00b140; color: #fff; }
        tr:hover { background: #f0f0f0; transition: background 0.3s; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Transaction History</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Amount</th>
            <th>Type</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <?php foreach ($transactions as $tx): ?>
        <tr>
            <td><?php echo $tx['id']; ?></td>
            <td><?php echo $tx['amount']; ?></td>
            <td><?php echo $tx['type']; ?></td>
            <td><?php echo $tx['status']; ?></td>
            <td><?php echo $tx['created_at']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div style="text-align: center;"><button onclick="redirectToDashboard()">Back to Dashboard</button></div>
    <script>
        function redirectToDashboard() {
            location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
