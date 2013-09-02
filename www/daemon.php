<?php
require_once("functions.php");
require_once("php_serial.class.php");
require_once("GPIO.php");

####################################
# Define the GPIO pins we are using
####################################
define("RED", 7); // CE1
define("GREEN", 8); // CE0
define("BUZZER", 25); // #25
define("DOOR", 24); // #24

define("BUTTON", 18); //
define("DOOR_STATE", 22);
define("ALARM", 15);
####################################
# Initialize GPIO pins
####################################

define("ON", 1);
define("OFF", 0);

$gpio = new GPIO();

$gpio->setup(RED,  "out");
$gpio->setup(GREEN, "out");
$gpio->setup(BUZZER, "out");
$gpio->setup(DOOR, "out");

$gpio->setup(BUTTON, "in");
$gpio->setup(DOOR_STATE, "in");

####################################
# Initialize UART
####################################

$serial = new phpSerial;
$serial->deviceSet("/dev/ttyAMA0");
//$serial->confBaudRate(19200);
$serial->confBaudRate(115200);
$serial->confFlowControl("none");
$serial->confCharacterLength(8);
$serial->confParity("none");
$serial->confStopBits(1);

####################################
# Startup self-test
####################################

$gpio->output(RED, ON);
$gpio->output(GREEN, ON); 
$gpio->output(BUZZER, ON); 
usleep(100000);
$gpio->output(GREEN, OFF);
$gpio->output(BUZZER, OFF); 
$code=getID($serial);

####################################
# Initialize state variables
####################################
$lastButton=$gpio->input(BUTTON);
$lastDoor=$gpio->input(DOOR_STATE);

#If the door is open at startup, then create the lock file...
if($lastDoor==0){
	echo "Door is OPEN".PHP_EOL;
	$h = fopen("door.lock", 'w') or die("can't open file");
	fclose($h);
}
$code="";
$wallet=array();

####################################
# Start main loop...
####################################
audit("","System starting up...");
echo "Starting up...".PHP_EOL;

while(1){
	#After a code has been received, we don't want any more codes until the card has been removed at least once...
	#BUT....we do want to read all UIDs currently in the rf field...
	$code=getID($serial);
	$cnt_loop++;
	echo $cnt_loop."> '";
	print_r($code);
	echo "'\n";
	if($code==-1)
		$wallet = array(); #Empty our "wallet" list
	
	if($code !="-1" && !array_key_exists($code,$wallet)){
		$wallet[$code]="";
		$con = mysql_connect("localhost","root","raspberrypi");
		mysql_select_db("lab_access",$con);
		
		$qry="SELECT COUNT(*) FROM users WHERE active=TRUE AND code='".$code."'";           		
	        $result=mysql_query($qry);
		mysql_close($con);
		$row=mysql_fetch_row($result);
		
	        if($row[0]){
			echo $code." Access Granted".PHP_EOL;
			audit($code,"Access Granted");
	        	openDoor($gpio);
		}else{
			echo $code." Access Denied".PHP_EOL;
                        audit($code,"Access Denied");  
			alarm($gpio);	
		}
	}
	
        $button=$gpio->input(BUTTON);
       
	#Hold door open with relay so long as it is held down...
	if($button==1){
		openDoor_hold($gpio);
		$gpio->output(RED, OFF);
		$gpio->output(GREEN, ON); 
	}

	#We want to capture a button release event, and keep door open for a few seconds...
	if($button!=$lastButton){
		if($button==1){
                        echo "Button PRESSED".PHP_EOL;
			$gpio->output(BUZZER, ON); 
			usleep(100000);
			$gpio->output(BUZZER, OFF); 
		}
		if($button==0){
			echo "Button RELEASED".PHP_EOL;
			openDoor_quiet($gpio);
			audit("","Door opened via button");
		}
		$lastButton=$button;
	}
	#No point in reading from the door sensor if the button is pressed, since the door has no power
	if($button==0)
		$door=$gpio->input(DOOR_STATE);

	if($door!=$lastDoor){
		if($door==0){
			echo "Door OPEN".PHP_EOL;
                        audit("","Door OPEN");
			$h = fopen("door.lock", 'w') or die("can't open file");
		        fclose($h);
		}else{
			echo "Door CLOSED".PHP_EOL;
                        audit("","Door CLOSED");
			unlink("door.lock");
		}
                $lastDoor=$door;
        }
}
$gpio->unexportAll();
?>
