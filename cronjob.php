<?php
// PHP file created, sourced and edited by Adam Mackay
// security implementations as recommentded by Saxon Partridge Smith
// to delete old inactive users
include ("settings.php");
include ($lib_dir . "db_class.php");
$db = new db();
$db->connect();
$db->delete("fh_users", "lastlogin_timestamp = " . time() - $expire_accounts . " AND active = 0");
$db->disconnect();

// Script to use backup: sourced from 000webhost community forums
// Credit: User: infinity, 
// URL https://www.000webhost.com/forum/t/quickly-backup-your-000webhost-site/111358
// date: 21/02/2018

define('VERSION', '0.0.1 Beta');

$timestart = microtime(TRUE);
$GLOBALS['status'] = array();

$zippath = '.';
// Resulting zipfile e.g. zipper--2016-07-23--11-55.zip.
$zipfile = 'server_backups/serverbackup-' . date("Y-m-d--H-i") . '.zip';
Zipper::zipDir($zippath, $zipfile);

$timeend = microtime(TRUE);
$time = round($timeend - $timestart, 4);

/**
 * Class Unzipper
 */

/**
 * Class Zipper
 *
 * Copied and slightly modified from http://at2.php.net/manual/en/class.ziparchive.php#110719
 * @author umbalaconmeogia
 */
class Zipper {
  /**
   * Add files and sub-directories in a folder to zip file.
   *
   * @param string $folder
   *   Path to folder that should be zipped.
   *
   * @param ZipArchive $zipFile
   *   Zipfile where files end up.
   *
   * @param int $exclusiveLength
   *   Number of text to be exclusived from the file path.
   */
  private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
    $handle = opendir($folder);

    while (FALSE !== $f = readdir($handle)) {
      // Check for local/parent path or zipping file itself and skip.
      if ($f != '.' && $f != '..' && $f != basename(__FILE__)) {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);

        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        }
        elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength);
        }
      }
    }
    closedir($handle);
  }

  /**
   * Zip a folder (including itself).
   *
   * Usage:
   *   Zipper::zipDir('path/to/sourceDir', 'path/to/out.zip');
   *
   * @param string $sourcePath
   *   Relative path of directory to be zipped.
   *
   * @param string $outZipPath
   *   Relative path of the resulting output zip file.
   */
  public static function zipDir($sourcePath, $outZipPath) {
    $pathInfo = pathinfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new ZipArchive();
    $z->open($outZipPath, ZipArchive::CREATE);
    $z->addEmptyDir($dirName);
    if ($sourcePath == $dirName) {
      self::folderToZip($sourcePath, $z, 0);
    }
    else {
      self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
    }
    $z->close();

    $GLOBALS['status'] = array('success' => 'Successfully created the backup file, download it by using the File Manager on 000webhost.com or by using a FTP client, else you can simply paste this into your browser http://YOURSITE.000webhostapp.com/
    
    
    ' . $outZipPath); 
  } 
}




?>



<!DOCTYPE html>
<html>
<head>
  <title>Backup</title>
  <head>
<body>
<p class="status status--<?php echo strtoupper(key($GLOBALS['status'])); ?>">
  Status: <?php echo reset($GLOBALS['status']); ?><br/>
</p>
</body>
</html>
