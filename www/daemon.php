<?php
    # RFID Lock Project 
    # Copyright (C) 2013  Ben Barker

    # This program is free software: you can redistribute it and/or modify
    # it under the terms of the GNU Affero General Public License as published by
    # the Free Software Foundation, either version 3 of the License, or
    # (at your option) any later version.

    # This program is distributed in the hope that it will be useful,
    # but WITHOUT ANY WARRANTY; without even the implied warranty of
    # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    # GNU Affero General Public License for more details.

    # You should have received a copy of the GNU Affero General Public License
    # along with this program.  If not, see <http://www.gnu.org/licenses/>.

require_once("/var/www/functions.php");
require_once("/var/www/php_serial.class.php");
require_once("/var/www/GPIO.php");

####################################
# Define the GPIO pins we are using
####################################
define("RED", 7);
define("GREEN", 8);
define("BUZZER", 25);
define("DOOR", 24);

define("BUTTON", 18);
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
$serial->confBaudRate(19200);
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
	if($code==-1)
		$wallet = array(); #Empty our "wallet" list
	
	if($code !="-1" && !array_key_exists($code,$wallet)){
		$wallet[$code]="";
		$con = mysql_connect("localhost","root","raspberry");
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
