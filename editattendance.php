<?php
include("db.php");
date_default_timezone_set("Asia/Kolkata");

// ✅ Search filters
$search_name = $_GET['name'] ?? '';
$search_month = $_GET['month'] ?? date('Y-m');

// ✅ Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_attendance'])) {
    foreach ($_POST['id'] as $id) {
        $status = $_POST['status'][$id];
        $permission_from = $_POST['permission_from'][$id];
        $permission_to = $_POST['permission_to'][$id];
        $conn->query("UPDATE attendance 
                      SET status='$status', permission_from='$permission_from', permission_to='$permission_to'
                      WHERE id='$id'");
    }
    echo "<script>alert('Attendance updated successfully!');window.location.href='editattendance.php?month=$search_month&name=$search_name';</script>";
    exit;
}

// ✅ Get records based on search
$query = "SELECT * FROM attendance WHERE 1=1";
if ($search_name) $query .= " AND name LIKE '%$search_name%'";
if ($search_month) $query .= " AND DATE_FORMAT(date, '%Y-%m') = '$search_month'";
$query .= " ORDER BY date DESC";
$result = $conn->query($query);

// ✅ Monthly summary calculation
function calculateAbsents($conn, $month, $name) {
    $daysInMonth = date('t', strtotime($month . '-01'));
    $sundays = 0;
    for ($i = 1; $i <= $daysInMonth; $i++) {
        if (date('N', strtotime("$month-$i")) == 7) $sundays++;
    }
    $present = $conn->query("SELECT COUNT(*) AS total FROM attendance 
                             WHERE name='$name' AND status='Present' 
                             AND DATE_FORMAT(date, '%Y-%m')='$month'")
                             ->fetch_assoc()['total'];
    $absent = $daysInMonth - $sundays - $present;
    return max($absent, 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View & Edit Attendance</title>
<style>
body {font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa;}
h2 {color: #333;}
table {width: 100%; border-collapse: collapse; margin-top: 15px;}
th, td {border: 1px solid #ccc; padding: 8px; text-align: center;}
th {background-color: #007bff; color: white;}
button {padding: 6px 15px; background: #007bff; color: #fff; border: none; cursor: pointer;}
button:hover {background: #0056b3;}
input[type="time"], select {padding: 5px;}
.search-box {margin-bottom: 20px;}
.present {color: green; font-weight: bold;}
.absent {color: red; font-weight: bold;}
.permission {color: orange; font-weight: bold;}
</style>
</head>
<body>

<h2>View & Edit Attendance</h2>

<!-- 🔍 Search Filters -->
<form method="GET" class="search-box">
    <input type="text" name="name" placeholder="Search by name" value="<?= htmlspecialchars($search_name) ?>">
    <input type="month" name="month" value="<?= htmlspecialchars($search_month) ?>">
    <button type="submit">Search</button>
</form>

<!-- 📋 Attendance Table -->
<form method="POST">
    <table>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Status</th>
            <th>Permission From</th>
            <th>Permission To</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['date'] ?></td>
                <td><?= $row['name'] ?></td>
                <td>
                    <select name="status[<?= $row['id'] ?>]">
                        <option value="Present" <?= ($row['status'] == 'Present') ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= ($row['status'] == 'Absent') ? 'selected' : '' ?>>Absent</option>
                        <option value="Permission" <?= ($row['status'] == 'Permission') ? 'selected' : '' ?>>Permission</option>
                    </select>
                </td>
                <td><input type="time" name="permission_from[<?= $row['id'] ?>]" value="<?= $row['permission_from'] ?>"></td>
                <td><input type="time" name="permission_to[<?= $row['id'] ?>]" value="<?= $row['permission_to'] ?>"></td>
                <input type="hidden" name="id[]" value="<?= $row['id'] ?>">
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No records found for this filter</td></tr>
        <?php endif; ?>
    </table>
    <br>
    <?php if ($result->num_rows > 0): ?>
        <button type="submit" name="update_attendance">Update Attendance</button>
    <?php endif; ?>
</form>

<hr>

<!-- 📊 Monthly Summary -->
<h3>Monthly Summary (<?= htmlspecialchars($search_month) ?>)</h3>
<table>
    <tr>
        <th>Name</th>
        <th>Absent Days (Excluding Sundays)</th>
    </tr>
    <?php
    $names = $conn->query("SELECT DISTINCT name FROM attendance WHERE DATE_FORMAT(date, '%Y-%m')='$search_month'");
    while ($emp = $names->fetch_assoc()):
    ?>
    <tr>
        <td><?= $emp['name'] ?></td>
        <td><?= calculateAbsents($conn, $search_month, $emp['name']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
