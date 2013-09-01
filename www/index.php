<?
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
