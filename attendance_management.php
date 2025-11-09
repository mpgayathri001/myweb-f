<?php
include 'db.php';

// Default employees
$employees = [
    ["name" => "Karthick Raja", "role" => "Manager"],
    ["name" => "Subahashini", "role" => "Sales"],
    ["name" => "Bala", "role" => "Marketing"]
];

// Handle submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = date('Y-m-d');
    foreach ($_POST['attendance'] as $index => $status) {
        $name = mysqli_real_escape_string($conn, $_POST['name'][$index]);
        $role = mysqli_real_escape_string($conn, $_POST['role'][$index]);
        $permission_from = !empty($_POST['permission_from'][$index]) ? "'" . $_POST['permission_from'][$index] . "'" : "NULL";
        $permission_to = !empty($_POST['permission_to'][$index]) ? "'" . $_POST['permission_to'][$index] . "'" : "NULL";

        $query = "INSERT INTO attendance (name, role, status, permission_from, permission_to, date)
                  VALUES ('$name', '$role', '$status', $permission_from, $permission_to, '$date')";
        mysqli_query($conn, $query);
    }
    echo "<script>alert('✅ Attendance submitted successfully!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Attendance Management | BMMS Motors</title>
<style>
  body { font-family:'Poppins',sans-serif; background:#000; color:#fff; margin:0; padding:0; }
  .container { width:80%; margin:40px auto; background:#111; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(255,255,255,0.1); }
  h2 { text-align:center; color:#ffcc00; margin-bottom:20px; }
  table { width:100%; border-collapse:collapse; margin-top:20px; }
  th, td { padding:12px; border-bottom:1px solid #333; text-align:center; }
  th { color:#ffcc00; }
  select, input[type="time"] { background:#222; color:#fff; border:none; padding:6px; border-radius:6px; width:80%; }
  .btn { background:#ff9800; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-size:16px; margin-top:20px; }
  .btn:hover { background:#e68900; }
</style>
</head>
<body>
<div class="container">
  <h2>🕒 Attendance Management - BMMS Motors</h2>
  <form method="POST">
    <table>
      <tr>
        <th>Employee Name</th>
        <th>Role</th>
        <th>Status</th>
        <th>Permission From</th>
        <th>Permission To</th>
      </tr>
      <?php foreach ($employees as $index => $emp): ?>
      <tr>
        <td>
          <?= $emp['name']; ?>
          <input type="hidden" name="name[<?= $index; ?>]" value="<?= $emp['name']; ?>">
        </td>
        <td>
          <?= $emp['role']; ?>
          <input type="hidden" name="role[<?= $index; ?>]" value="<?= $emp['role']; ?>">
        </td>
        <td>
          <select name="attendance[<?= $index; ?>]" onchange="togglePermission(this, <?= $index; ?>)">
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
            <option value="Permission">Permission</option>
          </select>
        </td>
        <td>
          <input type="time" name="permission_from[<?= $index; ?>]" id="perm_from_<?= $index; ?>" disabled>
        </td>
        <td>
          <input type="time" name="permission_to[<?= $index; ?>]" id="perm_to_<?= $index; ?>" disabled>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <div style="text-align:center;">
      <button class="btn" type="submit">💾 Save Attendance</button>
    </div>
  </form>
</div>

<script>
function togglePermission(select, id) {
  const from = document.getElementById('perm_from_' + id);
  const to = document.getElementById('perm_to_' + id);
  const disabled = select.value !== 'Permission';
  from.disabled = disabled;
  to.disabled = disabled;
}
</script>
</body>
</html>
