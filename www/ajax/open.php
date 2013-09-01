<?
require_once("../functions.php");
audit("","Door opened via web interface");


$file = "lock.txt";
$h = fopen($file, 'w') or die("can't open file");
fclose($h);


#Every time the maglock power up it momentarily registers "door open" even if it is not
#We create a lock file to prevent this event being logged until 2 seconds after power-up
exec('sudo /var/www/ajax/openShell.sh');
sleep(2);
unlink($file);
clearstatcache();
?>
