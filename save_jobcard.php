<?php
include 'db.php'; // Make sure this connects properly

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Safely get all fields using null coalescing (prevents undefined key warnings)
    $job_date = $_POST['date'] ?? '';
    $job_time = $_POST['time'] ?? '';
    $job_card_no = $_POST['job_card_no'] ?? '';
    $vehicle_no = $_POST['vehicle_no'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_address = $_POST['address'] ?? '';
    $phone_no = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $model = $_POST['model'] ?? '';
    $chassis_no = $_POST['chassis_no'] ?? '';
    $motor_no = $_POST['motor_no'] ?? '';
    $km_reading = $_POST['km_reading'] ?? '';
    $road_test = $_POST['road_test'] ?? '';
    $type_of_service = $_POST['service_type'] ?? '';
    $tyre_pressure_front = $_POST['tyre_front'] ?? '';
    $tyre_pressure_rear = $_POST['tyre_pressure_rear'] ?? '';
    $last_service_date = $_POST['last_service'] ?? '';
    $average_km = $_POST['avg_km'] ?? '';
    $last_service_work = $_POST['last_work'] ?? '';
    $customer_complaint = $_POST['complaint'] ?? '';
    $jobs_to_perform = $_POST['jobs'] ?? '';
    $approval_taken = $_POST['approval'] ?? '';
    $observation = $_POST['observation'] ?? '';
    $action_taken = $_POST['action_taken'] ?? '';
    $payment_mode = $_POST['payment_mode'] ?? '';
    $estimate_parts = $_POST['cost_parts'] ?? '';
    $estimate_labour = $_POST['cost_labour'] ?? '';
    $estimate_consumables = $_POST['cost_consumables'] ?? '';
    $delivery_datetime = $_POST['delivery_est'] ?? '';
    $completion_status = $_POST['request_done'] ?? '';
    $service_advisor = $_POST['service_advisor'] ?? '';

    $query = "INSERT INTO job_card (
        job_date, job_time, job_card_no, vehicle_no, customer_name, customer_address, phone_no, email, model,
        chassis_no, motor_no, km_reading, road_test, type_of_service, tyre_pressure_front, tyre_pressure_rear,
        last_service_date, average_km, last_service_work, customer_complaint, jobs_to_perform, approval_taken,
        observation, action_taken, payment_mode, estimate_parts, estimate_labour, estimate_consumables,
        delivery_datetime, completion_status, service_advisor
    ) VALUES (
        '$job_date', '$job_time', '$job_card_no', '$vehicle_no', '$customer_name', '$customer_address', '$phone_no', '$email', '$model',
        '$chassis_no', '$motor_no', '$km_reading', '$road_test', '$type_of_service', '$tyre_pressure_front', '$tyre_pressure_rear',
        '$last_service_date', '$average_km', '$last_service_work', '$customer_complaint', '$jobs_to_perform', '$approval_taken',
        '$observation', '$action_taken', '$payment_mode', '$estimate_parts', '$estimate_labour', '$estimate_consumables',
        '$delivery_datetime', '$completion_status', '$service_advisor'
    )";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('✅ Job Card Saved Successfully!'); window.location='job_card.php';</script>";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
?>
