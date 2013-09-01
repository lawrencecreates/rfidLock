<?
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
