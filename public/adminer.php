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
	require __DIR__."/../config/adminer_credential.php";
	if (isset(ADMINER_CREDENTIALS[$_POST["username"]])) {
		if (password_verify($_POST["password"], ADMINER_CREDENTIALS[$_POST["username"]])) {
			$_SESSION["adminer_login"] = true;
			header("Location: ?");
			exit(0);
		}
	}
	$alert = "Invalid username or password!";
}

?><!DOCTYPE html>
<html>
<head>
	<title>Login Adminer</title>
	<style type="text/css">
		* {
			font-family: Arial, Helvetica;
		}
	</style>
	<?php if (isset($alert)): ?><script type="text/javascript">alert('<?php print $alert; ?>')</script><?php endif ?>
</head>
<body>
	<center>
		<h2>Login</h2>
		<form method="POST" action="?login_action=1">
			<p>Username:</p>
			<input type="text" name="username"/>
			<p>Password:</p>
			<input type="password" name="password"/>
			<div style="margin-top: 30px;">
				<button type="submit" name="login">Login</button>
			</div>
		</form>
	</center>
</body>
</html>