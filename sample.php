<?php
error_reporting(1);
$name=$_POST['u'];
$rno=$_POST['v'];
$con=new mysqli('localhost','root','','log');
if(isset($_POST['SUBMIT']))
{
$sql="INSERT INTO lo(username,password)VALUES('$name','$rno')";
$con->query($sql);
header("Location: https://mpgayathri001.github.io/myweb-f/login.html");
}




?>
