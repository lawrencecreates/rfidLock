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

require_once("functions.php");
?>

<html>

<head>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="js/jquery.js"></script>
	<script src="js/functions.js"></script>
	<title>Lab Access Control</title>
	<meta name="viewport" content="width=device-width">
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
</head>

<body>

<div align='center'>
<a href='logs.php'>View Logs</a>
<h1>1st floor lab Access control</h2>
<hr>
<input id='toggle' type='button' value='Unlock Door' onclick='openDoor()'/>
<hr>

<h2>Add new user:</h2>
<table>
<tr><td>First Name</td><td><input type='text' id='fn'></td></tr>
<tr><td>Last Name</td><td><input type='text' id='ln'></td></tr>
<tr><td>Code</td><td><input type='text' id='code'></td></tr>
</table>
<input id='add' type='button' value='Save' onclick='addUser()'>
<hr>
<h2>List of Authorized users:</h2>
<?
drawUsers();
?>

</div>

</body>
</html>
