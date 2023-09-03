<?php
$connect=mysqli_connect("localhost","root","","log");
if(!empty($_POST['SUBMIT']))
{
   $usernme=$_POST['U'];
$psswod=$_POST['V'];

$query="SELECT * FROM lo WHERE username='$usernme' and password='$psswod';
$result=mysqli_query($connect,$query);
$count=mysqli_num_rows($result);
if($count>0)
{
 header("Location: https://mpgayathri001.github.io/myweb-f/hom.html");
}
else
{
echo"LOGIN NOT SUCCESSFUL";
}
}
?>