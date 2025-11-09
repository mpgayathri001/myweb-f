<?php
include 'db.php';

$vehicle_no = $_GET['vehicle_no'] ?? '';
$query = "SELECT name, address, phone, Vehicle_Type FROM customer WHERE vehicle_no='$vehicle_no'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    echo json_encode(mysqli_fetch_assoc($result));
} else {
    echo json_encode(null);
}
?>
