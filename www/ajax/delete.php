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
