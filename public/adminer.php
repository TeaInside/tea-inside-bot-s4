<?php

session_start();

if (isset($_SESSION["adminer_login"])) {
	if (isset($_GET["adminer_logout"])) {
		session_destroy();
		header("Location: ?");
		exit(0);
	}
	require __DIR__."/../adminer.php";
	exit(0);
}

if (isset($_POST["login"], $_GET["login_action"])) {
	
}

?><!DOCTYPE html>
<html>
<head>
	<title>Login Adminer</title>
</head>
<body>
	<center>
		<h2>Login</h2>
		<form method="POST" action="?login_action=1">
			<h4>Username:</h4>
			<input type="text" name="username"/>
			<h4>Password:</h4>
			<input type="text" name="password"/>
			<div>
				<button type="submit" name="login">Login</button>
			</div>
		</form>
	</center>
</body>
</html>