<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Allow direct admin login (no DB)
    if (($email === 'admin' || $email === 'admin@bmms.com') && $password === 'Admin@21524') {
        $_SESSION['email'] = 'admin';
        $_SESSION['role'] = 'admin';
        header("Location: admindashboard.php");
        exit();
    }

    // ✅ Normal user login (from database)
    $stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $password) { // change to password_verify() if using hashed passwords
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'] ?? 'user';
            header("Location: dashboard.php");
            exit();
        }
    }

    echo "<script>alert('Invalid email or password');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | BMMS Motors</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: url('loginimage.jpeg') no-repeat center center fixed;
      background-size: cover;
      font-family: Arial, sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      width: 320px;
      text-align: center;
      color: white;
    }

    h2 {
      margin-bottom: 25px;
      font-size: 26px;
      text-shadow: 2px 2px 5px #000;
    }

    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    label {
      display: block;
      font-size: 16px;
      margin-bottom: 6px;
      color: #ffeb3b;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      font-size: 16px;
      outline: none;
    }

    input::placeholder {
      color: #1a1919ff;
    }

    .btn {
      background-color: #ff9800;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-size: 18px;
      cursor: pointer;
      transition: 0.3s;
      width: 100%;
    }

    .btn:hover {
      background-color: #e68900;
      transform: scale(1.05);
    }

    .back-link {
      margin-top: 15px;
      display: block;
      color: #ffeb3b;
      text-decoration: none;
      font-size: 14px;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login</h2>
    <form action="" method="POST">
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>

      <button class="btn" type="submit">Login</button>
    </form>
    <a href="index.php" class="back-link">← Back to Home</a>
  </div>
</body>
</html>
