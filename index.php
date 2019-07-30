
<?php
if (!empty($_GET['savetype'])) {

	if (count($_GET) == 8) {
		// Données de connexion à la base de données
		$MYSQLhost = $_GET['bddhost'];
		$MYSQLdb = $_GET['bddname'];
		$MYSQLuid = $_GET['bdduser'];
		$MYSQLpwd = $_GET['bddpass'];
		// Données de connexion au serveur FTP
		$ftpServer = $_GET['ftphost'];
		$ftpUser = $_GET['ftpuser'];
		$ftpPwd = $_GET['ftppass'];
		$is_congig = true;
	}
	if (empty($_GET['localsave'])) {
		$localsave = false;
	} else {
		$localsave = true;
	}

	if ($_GET['savetype'] === "ftp") {
		require_once('ftp.php');
	} else if ($_GET['savetype'] === "bdd") {
		require_once('bdd.php');
	} else if ($_GET['savetype'] === "all") {
		require_once('ftp.php');
		require_once('bdd.php');
	}
}

require_once('config.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>Backup WEB (BDD+FTP</title>
</head>
<body>

	<style type="text/css" media="screen">
		html {
			font-family: sans-serif;
		}

		div:first-of-type {
			display: flex;
			align-items: flex-start;
			margin-bottom: 5px;
		}

		label {
			margin-right: 15px;
			line-height: 32px;
		}

		input[type="radio"] {
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;

			border-radius: 50%;
			width: 16px;
			height: 16px;
			border: 2px solid red;
			transition: 0.2s all linear;
			outline: none;
			margin-right: 5px;

			position: relative;
			top: 4px;
		}

		input[type="text"]{
			-webkit-appearance: none;
			-moz-appearance: none;
			appearance: none;

			width: auto;
			height: auto;

			border: 2px solid #999;
			transition: 0.2s all linear;
			outline: none;
			margin-right: 5px;

			position: relative;
		}

		 input[type="checkbox"]{
		 	-webkit-appearance: none;

			width: auto;
			height: auto;

			border: 6px solid red;
			outline: none;
			padding: 5px 5px 5px 5px;
			transition: 0.2s all linear;
			position: relative;
		 }

		input:checked {
			border: 6px solid green;
		}

		button, legend {
			color: white;
			background-color: black;
			padding: 5px 10px;
			border-radius: 0;
			transition: 0.2s all linear;
			border: 0;
			font-size: 14px;
			margin-top: 20px;
			margin-bottom: 20px;
		}

		button:hover, button:focus {
			color: yellow;
		}

		button:over {
			background-color: white;
			color: #999;
			outline: 1px solid yellow;
		}
		.error {
			color: red;
			font-weight: bold;
			border: 4px solid #999;
			padding: 10px 10px 10px 10px;
		}
		.valide {
			color: green;
			font-weight: bold;
			border: 4px solid #999;
			padding: 10px 10px 10px 10px;
		}
	</style>

	<?php
	echo '
	<form method="get">
		<fieldset>
			<legend>Veuillez choisir le type de sauvegarde :</legend>
			<div>
				<input type="radio" id="savetype1" name="savetype" value="ftp" required>
				<label for="savetype1">FTP</label>

				<input type="radio" id="savetype2" name="savetype" value="bdd" required>
				<label for="savetype2">BDD</label>

				<input type="radio" id="savetype3" name="savetype" value="all" required>
				<label for="savetype3">FTP + BDD</label>

				<input id="checkBox" type="checkbox" name="localsave" checked>
				<label for="checkBox">Sauvegarder sur le ftp</label>
			</div>
			<div>
			</div>
			<legend>Veuillez rentrer les identifiants FTP :</legend>
			<div>
				<label>Host: <input type="text" name="ftphost" value="'. $ftpServer .'"></label>
				<label>User: <input type="text" name="ftpuser" value="'. $ftpUser .'"></label>
				<label>Password: <input type="text" name="ftppass" value="'. $ftpPwd .'"></label>
			</div>
			<legend>Veuillez rentrer les identifiants BDD :</legend>
			<div>
				<label>Host: <input type="text" name="bddhost" value="'. $MYSQLhost .'"></label>
				<label>DBname: <input type="text" name="bddname" value="'. $MYSQLdb .'"></label>
				<label>User: <input type="text" name="bdduser" value="'. $MYSQLuid .'"></label>
				<label>Password: <input type="text" name="bddpass" value="'. $MYSQLpwd .'"></label>
			</div>
			<div>
				<button id="submit" type="submit">Lancer la sauvegarde</button>
			</div>
		</fieldset>
	</form>
	';
	?>


</body>
</html>