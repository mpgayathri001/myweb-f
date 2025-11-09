<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | BMMS Motors</title>

  <!-- ✅ Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    * {margin: 0; padding: 0; box-sizing: border-box;}

    body {
      font-family: 'Poppins', sans-serif;
      background: url('admindashboardimage.jpg') no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      color: white;
    }

    header {
      width: 100%;
      padding: 15px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1000;
    }

    .logo {
      font-size: 28px;
      font-weight: bold;
      letter-spacing: 2px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo img {
      height: 40px;
      width: 40px;
      border-radius: 50%;
    }

    .nav-buttons {
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .nav-buttons a,
    .nav-buttons button,
    .dropbtn {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: 1px solid white;
      padding: 10px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
      text-decoration: none;
    }

    .nav-buttons a i,
    .dropdown-content a i {
      margin-right: 8px;
    }

    .nav-buttons a:hover,
    .nav-buttons button:hover,
    .dropbtn:hover {
      background: #ff9800;
      border-color: #ff9800;
      transform: scale(1.05);
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background: rgba(0, 0, 0, 0.85);
      min-width: 200px;
      border-radius: 8px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      z-index: 1;
      animation: fadeIn 0.3s ease;
    }

    .dropdown-content a {
      color: white;
      padding: 10px 14px;
      text-decoration: none;
      display: block;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .dropdown-content a:hover {
      background-color: #ff9800;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(5px); }
      to { opacity: 1; transform: translateY(0); }
    }

    main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 100px 20px 80px;
    }

    main h1 {
      font-size: 42px;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
      color: #ffeb3b;
    }

    footer {
      text-align: center;
      padding: 15px;
      background: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(6px);
      color: #ccc;
      font-size: 14px;
    }

    footer img {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      vertical-align: middle;
      margin-right: 10px;
    }

    @media (max-width: 768px) {
      .logo { font-size: 20px; }
      .nav-buttons { flex-direction: column; gap: 10px; }
      .nav-buttons a, .dropbtn { width: 100%; font-size: 14px; padding: 8px 12px; }
      main h1 { font-size: 28px; }

      .dropdown-content { position: static; display: none; background: rgba(0, 0, 0, 0.7); }
      .dropdown.active .dropdown-content { display: block; }
    }
  </style>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const dropdowns = document.querySelectorAll(".dropdown");
      dropdowns.forEach(drop => {
        drop.addEventListener("click", (e) => {
          e.stopPropagation();
          drop.classList.toggle("active");
        });
      });
      document.addEventListener("click", () => {
        dropdowns.forEach(d => d.classList.remove("active"));
      });
    });
  </script>
</head>
<body>

  <header>
    <div class="logo">
      <img src="bmmslogo.png" alt="BMMS Logo">
      BMMS MOTORS
    </div>

    <div class="nav-buttons">

     

      <!-- 💰 Daily Expense -->
      <div class="dropdown">
        <button class="dropbtn"><i class="fa-solid fa-wallet"></i> Daily Expense ▼</button>
        <div class="dropdown-content">
          <a href="dailyexpense.php"><i class="fa-solid fa-plus"></i> Add Expense</a>
          <a href="viewexpense.php"><i class="fa-solid fa-list"></i> View Expense</a>
        </div>
      </div>

      <!-- 📞 Follow-up -->
      <div class="dropdown">
        <button class="dropbtn"><i class="fa-solid fa-phone"></i> Follow Up ▼</button>
        <div class="dropdown-content">
          <a href="followup.php"><i class="fa-solid fa-user-plus"></i> Add Follow-up</a>
          <a href="editfollowup.php"><i class="fa-solid fa-pen-to-square"></i> Edit Follow-up</a>
          <a href="viewfollowup.php"><i class="fa-solid fa-eye"></i> View Follow-up</a>
        </div>
      </div>

      <!-- 🚗 Vehicle Expense -->
      <div class="dropdown">
        <button class="dropbtn"><i class="fa-solid fa-truck-fast"></i> Vehicle Expense ▼</button>
        <div class="dropdown-content">
          <a href="vehicleexpense.php"><i class="fa-solid fa-circle-plus"></i> Add Vehicle Expense</a>
          <a href="editvehicleexpense.php"><i class="fa-solid fa-pen-to-square"></i> Edit Vehicle Expense</a>
          <a href="viewvehicelexpense.php"><i class="fa-solid fa-eye"></i> View Vehicle Expense</a>
        </div>
      </div>

      <!-- 🧑‍💼 Attendance -->
      <div class="dropdown">
        <button class="dropbtn"><i class="fa-solid fa-user-check"></i> Attendance ▼</button>
        <div class="dropdown-content">
          <a href="attendance_management.php"><i class="fa-solid fa-clipboard-check"></i> Mark Attendance</a>
          <a href="editattendance.php"><i class="fa-solid fa-edit"></i> Edit Attendance</a>
        </div>
      </div>

      <!-- 📊 Report -->
      <a href="report.php"><i class="fa-solid fa-chart-column"></i> Report</a>
      <a href="followupnotification.php"><i class="fa-solid fa-chart-column"></i> Notification</a>
    </div>
  </header>

  <main>
    <h1>Welcome Admin — Manage BMMS MOTORS Operations</h1>
  </main>

  <footer>
    <img src="bmmslogo.png" alt="BMMS Logo"> © 2025 BMMS Motors. All Rights Reserved.
  </footer>

</body>
</html>
