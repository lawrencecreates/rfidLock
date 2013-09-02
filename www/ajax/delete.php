<?
require_once("../functions.php");

$id=$_REQUEST['id'];

$con = mysql_connect("localhost","root","raspberry");
mysql_select_db("lab_access",$con);

if(is_numeric($id)){
	$qry="SELECT firstName,lastName,code FROM users WHERE id=".$id;           
	$result=mysql_query($qry);
	$row=mysql_fetch_assoc($result);


	$qry="DELETE FROM users WHERE id=".$id;           
	$result=mysql_query($qry);
	mysql_close($con);

	audit($row["code"],"User ".$row['firstName']." ".$row['lastName']." deleted");
}
mysql_close($con);
?>
