<?php
require_once(__DIR__.'/'.'config.php');

function zipper_repertoire_recursif($nom_archive, $adr_dossier, $dossier_destination = '', $zip=null, $dossier_base = '') {
  if($zip===null) {
    // Si l'archive n'existe toujours pas (1er passage dans la fonction, on la crée)
    $zip = new ZipArchive();
    if($zip->open($nom_archive, ZipArchive::CREATE) !== TRUE) {
      // La création de l'archive a échouée
      return false;
    }
  }
  
  if(substr($adr_dossier, -1)!='/') {
    // Si l'adresse du dossier ne se termine pas par '/', on le rajoute
    $adr_dossier .= '/';
  }
  
  if($dossier_base=="") {
    // Si $dossier_base est vide ça veut dire que l'on rentre
    // dans la fonction pour la première fois. Donc on retient 
    // le tout premier dossier (le dossier racine) dans $dossier_base
    $dossier_base=$adr_dossier;
  }
  
  if(file_exists($adr_dossier)) {
    if(@$dossier = opendir($adr_dossier)) {
      while(false !== ($fichier = readdir($dossier))) {
        if($fichier != '.' && $fichier != '..') {
          if(is_dir($adr_dossier.$fichier)) {
            $zip->addEmptyDir($adr_dossier.$fichier);
            zipper_repertoire_recursif($nom_archive, $adr_dossier.$fichier, $dossier_destination, $zip, $dossier_base);
          }
          else {
            $zip->addFile($adr_dossier.$fichier);
          }
        }
      }
    }
  }
  
  if($dossier_base==$adr_dossier) {
    // On ferme la zip
    $zip->close();
    
    if($dossier_destination!='') {
      if(substr($dossier_destination, -1)!='/') {
        // Si l'adresse du dossier ne se termine pas par '/', on le rajoute
        $dossier_destination .= '/';
      }
      
      // On déplace l'archive dans le dossier voulu
      if(rename($nom_archive, $dossier_destination.$nom_archive)) {
        return true;
      }
      else {
        return false;
      }
    }
    else {
      return true;
    }
  }
}


$savedFile = 'FTP-'. date('d-m-Y_H-i-s').'.zip';
if(zipper_repertoire_recursif($savedFile, $saveDirectory, $zipDirectory))
{
  echo "<h1 style='color:green;'>L'archive a bien &eacute;t&eacute; cr&eacute;&eacute;e</h1>";
} else {
  echo "<h1 style='color:red;'>L'archive n'a pas &eacute;t&eacute; cr&eacute;&eacute;e</h1>";
}

$connection = ftp_connect($ftpServer);
$login = ftp_login($connection, $ftpUser, $ftpPwd);
ftp_pasv($connection, true);

// try {
//  ftp_mkdir($connection, $ftpTarget);
// } catch (Exception $e) {}


if ($connection && $login) {
  $upload = ftp_put($connection, $ftpTarget . $savedFile, $zipDirectory . $savedFile, FTP_BINARY);
  if ($upload) {
    echo '<p class="valide">Les fichiers on bien été sauvegardée :)</p>';
  } else {
    echo '<p class="error">Le transfert des fichiers a échouée :(</p>';
  }
} else {
  echo '<p class="error">Connection au serveur ftp impossible :(</p>';
}
ftp_close($connection);
if (!$localsave) {
  unlink($zipDirectory . $savedFile);
}