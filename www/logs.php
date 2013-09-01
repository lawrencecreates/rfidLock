<!--
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

-->
<head>
        <link rel="stylesheet" type="text/css" href="style.css">
        <title>Lab Access Control</title>
	<meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
</head>
<div align='center'>
<?
$con = mysql_connect("localhost","root","raspberry");
mysql_select_db("lab_access",$con);

$qry="SELECT time,userid,firstname,lastname,message FROM audit LEFT JOIN users ON userid=code ORDER BY time DESC LIMIT 100";
$result=mysql_query($qry);
mysql_close();
?>
<a href='index.php'>Home</a>
<a href='logs.php'>Refresh</a>
<h1>Last 100 log entries:</h1>
<table width=100%>
<th bgcolor=#000000><font color='white'>Time</font></th>
<th bgcolor=#000000><font color='white'>Code</font></th>
<th bgcolor=#000000><font color='white'>Name</font></th>
<th bgcolor=#000000><font color='white'>Message</font></th>
<?
        while($row=mysql_fetch_assoc($result)){
                echo "<tr>";
                echo "<td bgcolor=#c0c0c0>".$row['time']."</td>";
		echo "<td bgcolor=#cccccc>".$row['userid']."</td>";
		echo "<td bgcolor=#c0c0c0>".$row['firstname']." ".$row['lastname']."</td>";
		echo "<td bgcolor=#cccccc>".$row['message']."</td>";
                echo "</tr>";
        }
        echo "</table>";

?>
</div>
