<?
require_once("../functions.php");

$fn=$_REQUEST['fn'];
$ln=$_REQUEST['ln'];
$code=$_REQUEST['code'];

$con = mysql_connect("localhost","root","raspberry");
mysql_select_db("lab_access",$con);

$fn=mysql_real_escape_string($fn);
$ln=mysql_real_escape_string($ln);
$code=mysql_real_escape_string($code);

$qry="INSERT INTO users(firstName,lastName,code) VALUES('$fn','$ln','$code')";
$result=mysql_query($qry);
mysql_close($con);

audit($code,"User ".$fn." ".$lastname." added to system");
?>
