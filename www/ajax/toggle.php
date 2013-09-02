<?
require_once("../functions.php");

$id=$_REQUEST['id'];
$state=$_REQUEST['state'];

$con = mysql_connect("localhost","root","raspberry");
mysql_select_db("lab_access",$con);

if(is_numeric($id)){
	$qry="SELECT firstName,lastName,code FROM users WHERE id=".$id;           
	$result=mysql_query($qry);
	$row=mysql_fetch_assoc($result);

	if($state==1){
		$qry="UPDATE users SET active=TRUE WHERE id=".$id;           
		$result=mysql_query($qry);
		mysql_close($con);

		audit($row["code"],"User ".$row['firstName']." ".$row['lastName']." suspended");
	}else{
		$qry="UPDATE users SET active=FALSE WHERE id=".$id;           
                $result=mysql_query($qry);
                mysql_close($con);

                audit($row["code"],"User ".$row['firstName']." ".$row['lastName']." reactivated");
	}
}
mysql_close($con);
?>
