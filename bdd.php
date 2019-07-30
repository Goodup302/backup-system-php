<?php
require_once(__DIR__.'/'.'config.php');


// Ensemble des tables éventuelles à exclure complètement
$excludedTables = array('');
// Ensemble des tables éventuelles dont on ne veut sauvegarder que la structure
$onlyStructureTables = array('');
// On spécifie le chemin absolu où le script stockera les sorties .gz et .html
$path = $zipDirectory;

// On démarre la connexion
try {
	$conn = new PDO("mysql:host=$MYSQLhost;dbname=$MYSQLdb", $MYSQLuid, $MYSQLpwd, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
	// On liste d’abord l’ensemble des tables
	$result = $conn->query("SHOW TABLES");
	$tables = array();
	// On exclut éventuellement les tables indiquées
	while ($row = $result->fetch()) {
		if (!in_array($row[0], $excludedTables)) {
			$tables[] = $row[0];
		}
	}
	// La variable $return contiendra le script de sauvegarde.
	// On englobe le script de backup dans une transaction
	// et on désactive les contraintes de clés étrangères
	$return = '--
	-- Hôte : ' . $MYSQLhost . '
	-- Date et heure : ' . date('d/m/Y à H:i:s') . '
	-- Base de données : `' . $MYSQLdb . '`
	--
	SET FOREIGN_KEY_CHECKS=0;
	SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
	SET AUTOCOMMIT=0;
	START TRANSACTION;
	';
	// On boucle sur l’ensemble des tables à sauvegarder
	foreach ($tables as $table) {
	// On ajoute une instruction pour supprimer la table si elle existe déjà
	$return .= "DROP TABLE IF EXISTS `$table`;\n";
	// On génère ensuite la structure de la table
	$result = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
		$return .= $result['Create Table'] . ";\n\n";
		// Si la table n’est pas marquée à sauver en tant que "structure seule"
		if (!in_array($table, $onlyStructureTables)) {
			$result = $conn->query("SELECT * FROM `$table`");
			// On boucle sur l’ensemble des enregistrements de la table
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$return .= "INSERT INTO `$table` VALUES(";
				// On boucle sur l’ensemble des champs de l’enregistrement
				foreach ($row as $fieldValue) {
					// On purifie la valeur du champ
					$fieldValue = addslashes($fieldValue);
					$fieldValue = preg_replace("/\r\n/", "\\r\\n", $fieldValue);
					$return .= '"' . $fieldValue . '", ' ;
				}
				// On supprime la virgule à la fin de la requête INSERT
				$return = mb_substr($return, 0, -2) . ");\n";
			}
			$return .= "\n";
		}
	}
	// On valide la transaction
	// et on résactive les contraintes de clés étrangères
	$return .= 'SET FOREIGN_KEY_CHECKS=1;COMMIT;';
	$conn = null;
	// On enregistre maintenant le script SQL dans un fichier au format gzip
	// if (!file_exists($path)) {
	// 	mkdir($path);
	// }
	$savedFile = 'BDD-' . date('d-m-Y_H-i-s') . '.sql.gz';
	$gz = gzopen($path . $savedFile, 'w9');
	gzwrite($gz, $return);
	gzclose($gz);
	// On envoie maintenant le fichier gzip au serveur FTP indiqué
	$connection = ftp_connect($ftpServer);
	$login = ftp_login($connection, $ftpUser, $ftpPwd);
	ftp_pasv($connection, true);
	// try {
	// 	ftp_mkdir($connection, $ftpTarget);
	// } catch (Exception $e) {}
	
	if ($connection && $login) {
		$upload = ftp_put($connection, $ftpTarget . $savedFile, $path . $savedFile, FTP_BINARY);
		if ($upload) {
			echo '<p class="valide">La base de données a bien été sauvegardée :)</p>';
		} else {
			echo '<p class="error">Le transfert de la base de données a échouée :(</p>';
		}
	} else {
		echo '<p class="error">Connection au serveur ftp impossible :(</p>';
	}
	ftp_close($connection);
	if (!$localsave) {
		unlink($path . $savedFile);
	}
} catch(PDOException $e) {
	echo '<p class="error">Une erreur SQL c\'est produite  :(</p>';
	exit();
}