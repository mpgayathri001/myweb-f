<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Job Card | BMMS Motors</title>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  margin: 30px;
}
.container {
  width: 95%;
  margin: auto;
  border: 2px solid #000;
  padding: 20px;
}
table {
  width: 100%;
  border-collapse: collapse;
}
td, th {
  border: 1px solid #000;
  padding: 6px;
  vertical-align: top;
}
input, textarea, select {
  width: 100%;
  border: none;
  outline: none;
  font-size: 14px;
}
h2 {
  text-align: center;
  text-transform: uppercase;
  margin-bottom: 15px;
}
.btn {
  background: #000;
  color: #fff;
  border: none;
  padding: 10px 25px;
  margin-top: 15px;
  cursor: pointer;
  border-radius: 5px;
  display: block;
  margin-left: auto;
  margin-right: auto;
}
</style>

<script>
function fetchCustomerData(vehicleNo) {
  if (vehicleNo === "") return;

  const xhr = new XMLHttpRequest();
  xhr.open("GET", "get_customer.php?vehicle_no=" + vehicleNo, true);
  xhr.onload = function() {
    if (this.status === 200) {
      const data = JSON.parse(this.responseText);
      if (data) {
        document.getElementById("customer_name").value = data.name;
        document.getElementById("address").value = data.address;
        document.getElementById("phone").value = data.phone;
        document.getElementById("model").value = data.Vehicle_Type;
      }
    }
  };
  xhr.send();
}
</script>
</head>
<body>
<div class="container">
  <h2>JOB CARD</h2>

  <form action="save_jobcard.php" method="POST">
    <table>
      <tr>
        <td>Date: <input type="date" name="date"></td>
        <td>Time: <input type="time" name="time"></td>
        <td>Job Card Sr. No.: <input type="text" name="job_no"></td>
      </tr>
      <tr>
        <td>Customer Name: <input type="text" id="customer_name" name="customer_name" readonly></td>
        <td>Model: <input type="text" id="model" name="model" readonly></td>
        <td>Regn No:
          <select name="vehicle_no" onchange="fetchCustomerData(this.value)">
            <option value="">-- Select Vehicle No --</option>
            <?php
            $result = mysqli_query($conn, "SELECT vehicle_no FROM customer");
            while ($row = mysqli_fetch_assoc($result)) {
              echo "<option value='{$row['vehicle_no']}'>{$row['vehicle_no']}</option>";
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">Customer Address: <input type="text" id="address" name="address" readonly></td>
        <td>Chassis No.: <input type="text" name="chassis_no"></td>
      </tr>
      <tr>
        <td>Phone No.: <input type="text" id="phone" name="phone" readonly></td>
        <td>Motor No.: <input type="text" name="motor_no"></td>
        <td>Km Reading: <input type="text" name="km_reading"></td>
      </tr>
      <tr>
        <td colspan="2">E-Mail Id: <input type="email" name="email"></td>
        <td>Road Test Done Alone With Customer:
          <label><input type="radio" name="road_test" value="Yes">Yes</label>
          <label><input type="radio" name="road_test" value="No">No</label>
        </td>
      </tr>

      <tr>
        <td rowspan="2">
          Type Of Service:<br>
          <label><input type="checkbox" name="service_type" value="F1"> F1</label>
          <label><input type="checkbox" name="service_type" value="F2"> F2</label><br>
          <label><input type="checkbox" name="service_type" value="F3"> F3</label>
          <label><input type="checkbox" name="service_type" value="F4"> F4</label><br>
          <label><input type="checkbox" name="service_type" value="Repairs"> Repairs</label>
          <label><input type="checkbox" name="service_type" value="Paid"> Paid</label>
        </td>
        <td>
          Tyre Pressure:<br>
          Front <input type="text" name="tyre_front" style="width:40px"> Rear <input type="text" name="tyre_rear" style="width:40px"><br>
          LH <input type="text" name="tyre_lh" style="width:40px"> RH <input type="text" name="tyre_rh" style="width:40px">
        </td>
        <td>
          Last Service Date: <input type="text" name="last_service"><br>
          Average Km/Day: <input type="text" name="avg_km"><br>
          Last Service Work Done: <input type="text" name="last_work">
        </td>
      </tr>

      <tr>
        <td>Customer Complaint:<br><textarea name="complaint" rows="3"></textarea></td>
        <td>Jobs To Be Performed (By Advisor):<br><textarea name="jobs" rows="3"></textarea></td>
      </tr>

      <tr>
        <th colspan="3" style="background:#eee;">Additional Jobs Observed / Carried During The Service</th>
      </tr>
      <tr>
        <td colspan="3">
          Approval Taken From Customer:
          <label><input type="radio" name="approval" value="Yes">Yes</label>
          <label><input type="radio" name="approval" value="No">No</label>
        </td>
      </tr>
      <tr>
        <td>Observation:<br><textarea name="observation" rows="3"></textarea></td>
        <td colspan="2">Action Taken:<br><textarea name="action_taken" rows="3"></textarea></td>
      </tr>
      <tr>
        <td>Payment Mode:
          <label><input type="radio" name="payment_mode" value="Online">Online</label>
          <label><input type="radio" name="payment_mode" value="Cash">Cash</label>
        </td>
        <td colspan="2">
          Estimate Cost:<br>
          Parts <input type="text" name="cost_parts" style="width:80px">
          Labour <input type="text" name="cost_labour" style="width:80px">
          Consumables <input type="text" name="cost_consumables" style="width:80px">
        </td>
      </tr>
      <tr>
        <td colspan="3">Estimated Date & Time Of Delivery: <input type="datetime-local" name="delivery_est"></td>
      </tr>
      <tr>
        <td colspan="2">
          <p style="font-size:13px;">I hereby authorise the above mentioned jobs to be executed using the required material. My vehicle will be stored, driven and repaired at my risk.</p>
        </td>
        <td>Customer Signature</td>
      </tr>
      <tr>
        <td colspan="2">
          Completion Status:<br>
          1. All customer’s requests entered on job card <input type="checkbox" name="request_done" value="Yes">Yes <input type="checkbox" name="request_done" value="No">No<br>
          2. Action taken on previous visit feedback <input type="checkbox" name="feedback_done" value="Yes">Yes <input type="checkbox" name="feedback_done" value="No">No
        </td>
        <td>
          Name and Signature<br>of Service Advisor
        </td>
      </tr>
    </table>
    <button type="submit" class="btn">Save Job Card</button>
  </form>
</div>
</body>
</html>
