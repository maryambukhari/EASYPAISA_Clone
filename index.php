<?php
// index.php - Homepage

session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Easypaisa Clone - Homepage</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #00b140, #005f20); color: #fff; margin: 0; padding: 0; animation: fadeIn 1s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        header { text-align: center; padding: 50px; background: rgba(0,0,0,0.3); animation: slideDown 1s ease-out; }
        @keyframes slideDown { from { transform: translateY(-50px); } to { transform: translateY(0); } }
        h1 { font-size: 3em; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .services { display: flex; justify-content: space-around; flex-wrap: wrap; padding: 20px; }
        .service { background: #fff; color: #00b140; padding: 20px; margin: 10px; border-radius: 15px; width: 300px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: transform 0.3s, box-shadow 0.3s; }
        .service:hover { transform: scale(1.05); box-shadow: 0 8px 16px rgba(0,0,0,0.3); animation: pulse 1s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.02); } 100% { transform: scale(1); } }
        button { background: #00b140; color: #fff; border: none; padding: 15px 30px; font-size: 1.2em; border-radius: 30px; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #00802b; }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to EasyPaisa Clone</h1>
        <p>Your secure digital payment platform</p>
    </header>
    <section class="services">
        <div class="service">
            <h2>Money Transfer</h2>
            <p>Send funds instantly to anyone.</p>
        </div>
        <div class="service">
            <h2>Bill Payments</h2>
            <p>Pay utilities, recharges, and more.</p>
        </div>
        <div class="service">
            <h2>Digital Wallet</h2>
            <p>Manage your balance securely.</p>
        </div>
    </section>
    <div style="text-align: center; padding: 20px;">
        <button onclick="redirectToLogin()">Login</button>
        <button onclick="redirectToSignup()">Signup</button>
    </div>
    <script>
        function redirectToLogin() {
            location.href = 'login.php';
        }
        function redirectToSignup() {
            location.href = 'signup.php';
        }
    </script>
</body>
</html>
