<?php
if (empty($is_congig)) {
// Données de connexion à la base de données
$MYSQLhost = 'urbpfscbsql.mysql.db';
$MYSQLdb = 'urbpfscbsql';
$MYSQLuid = 'urbpfscbsql';
$MYSQLpwd = 'Megaee455';
// Données de connexion au serveur FTP
$ftpServer = 'ftp.local.net';
$ftpUser = 'Sefhty';
$ftpPwd = '7duu-è_4Z';
// Save sur les 2 FTP (Local + Externe)
$localsave = true;
}
// Répertoire de stockage de l'archive en local
$zipDirectory = __DIR__.'/';

// Répertoire a sauvegarder
$saveDirectory = __DIR__.'/'.'../site/';

// Répertoire de sauvegarde du serveur ftp externe
$ftpTarget = '/site/';


echo __DIR__;