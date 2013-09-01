<?
error_reporting(E_ALL);

function getID(&$serial){ 
        $bin = pack('H*', "AABB022022");
	$serial->deviceOpen();
	usleep(45000);
        $serial->sendMessage($bin);
        usleep(45000);
        $resp = $serial->readPort(); 
	//echo "~".$resp;
	$serial->deviceClose();
        $hex="";
        foreach(str_split($resp) as $b)
                $hex.=strtoupper(bin2hex($b));


        if($hex=="AABB02DFDD")
		$retVal=-1;

        else
		$retVal=substr($hex,8,-2);

return $retVal;
}

function openDoor_quiet(&$gpio){
        global $lastButton;
        $gpio->output(DOOR, ON);;
        usleep(100000); 
        sleep(2);
        $gpio->output(DOOR, OFF);
	$gpio->output(RED, ON);
        $gpio->output(GREEN, OFF);
	sleep(2);
}

function openDoor_hold(&$gpio){
        global $lastButton;
        $gpio->output(DOOR, ON);;
}


function openDoor(&$gpio){
        global $lastButton;

        $gpio->output(DOOR, ON);
        $gpio->output(RED, OFF);
        $gpio->output(GREEN, ON);
        $gpio->output(BUZZER, ON);
        usleep(100000); 
        $gpio->output(BUZZER, OFF);
        sleep(2);

        $gpio->output(RED, ON);
        $gpio->output(GREEN, OFF);
        $gpio->output(DOOR, OFF);
        $last=0;
	sleep(2);
}

function alarm(&$gpio){
        for($i=0;$i<5;$i++){
         	$gpio->output(RED, OFF);
	        $gpio->output(GREEN, ON);

	        $gpio->output(BUZZER, ON);
                usleep(30000);
                $gpio->output(BUZZER, OFF);
		//usleep(30000);

		$gpio->output(RED, ON);
                $gpio->output(GREEN, OFF);

		usleep(30000);
        }
}

function audit($code,$message){
        $con = mysql_connect("localhost","root","raspberry");
        mysql_select_db("lab_access",$con);

	$message=mysql_real_escape_string($message);
        $code=mysql_real_escape_string($code);

        $qry="INSERT INTO audit(userid,message) VALUES('".$code."','".$message."')";
        
	clearstatcache();
	if(file_exists('/var/www/ajax/lock.txt')===false){
		mysql_query($qry);
		echo "Audit record written".PHP_EOL;
        }else{
		echo "Lock file....no audit record written".PHP_EOL;
	}
	mysql_close($con);
}

function drawUsers(){
        $con = mysql_connect("localhost","root","raspberry");
        mysql_select_db("lab_access",$con);
        $qry="SELECT id,firstName,lastName,active FROM users ORDER BY lastName ASC";           
        $result=mysql_query($qry);
	//echo $qry;
        echo "<table>";
	echo "<th></th>";
	echo "<th></th>";
        echo "<th></th>";

	while($row=mysql_fetch_assoc($result)){
	        echo "<tr>";
                echo "<td><input type='button' value='Detete' onclick='deleteUser(\"".$row['id']."\")'</td>";
                echo "<td style='text-align:left'>&nbsp;&nbsp;".$row['firstName']."</td><td style='text-align:left'>&nbsp;&nbsp;".$row['lastName']."</td><td>";
		if($row['active']==0)
	        	echo "<td><input type='button' value='Enable' style='width:80px' onclick='toggleUser(\"".$row['id']."\",1)'</td>";
		else
			echo "<td><input type='button' value='Suspend' style='width:80px' onclick='toggleUser(\"".$row['id']."\",0)'</td>";
		echo "</tr>";
        }
        echo "</table>";
	mysql_close($con);
}

?>
