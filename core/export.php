<?php
define('DS', DIRECTORY_SEPARATOR);
/**
 * export the value as output string
 */
function export_var_export($var, $prefix = '', $count = 0){
  if (is_array($var)) {
    if (empty($var)) {
      $output = 'array()';
    }
    else {
      $output = "array(\n";
      foreach ($var as $key => $value) {
        // Using normal var_export on the key to ensure correct quoting.
        $output .= "  " . var_export($key, TRUE) . " => " . export_var_export($value, '  ', FALSE, $count+1) . ",\n";
      }
      $output .= ')';
    }
  }
  else if (is_bool($var)) {
    $output = $var ? 'TRUE' : 'FALSE';
  }
  else if (is_int($var)) {
    $output = intval($var);
  }
  else if (is_numeric($var)) {
    $floatval = floatval($var);
    if (is_string($var) && ((string) $floatval !== $var)) {
      // Do not convert a string to a number if the string
      // representation of that number is not identical to the
      // original value.
      $output = var_export($var, TRUE);
    }
    else {
      $output = $floatval;
    }
  }
  else if (is_string($var) && strpos($var, "\n") !== FALSE) {
    // Replace line breaks in strings with a token for replacement
    // at the very end. This protects whitespace in strings from
    // unintentional indentation.
    $var = str_replace("\n", "***BREAK***", $var);
    $output = var_export($var, TRUE);
  }
  else {
    $output = var_export($var, TRUE);
  }

  if ($prefix) {
    $output = str_replace("\n", "\n$prefix", $output);
  }
  return $output;
}


/**
 * copy folder
 */
function export_copy_r( $path, $dest ,$chmod = 0777){
  
  if( is_dir($path) ){
    $currentFolder=''; 
    $dirs=explode("/",$dest);
    for ($x=0; $x<count($dirs); $x++){ 
      $currentFolder.=$dirs[$x].DS;
      if(!is_dir($currentFolder)){ 
        if(!mkdir($currentFolder,$chmod,true) 
        && chmod($currentFolder, $chmod)){ die("Could not make ".$currentFolder); } 
      } 
    } 
    $objects = scandir($path);
    if( sizeof($objects) > 0 ){
        foreach( $objects as $file ){
            if( $file == "." || $file == ".." )
                continue;
            // go on
            if( is_dir( $path.DS.$file ) ){
                export_copy_r( $path.DS.$file, $dest.DS.$file ,$chmod);
            }else{
                copy( $path.DS.$file, $dest.DS.$file );
            }
        }
    }
    return true;
  }
  elseif( is_file($path)){
    $currentFolder=''; 
    $dirs=explode("/",$dest);
    for ($x=0; $x<count($dirs)-1; $x++){ 
      $currentFolder.=$dirs[$x].DS;
      if(!is_dir($currentFolder)){ 
        if(!mkdir($currentFolder,$chmod,true)){ die("Could not make ".$currentFolder); } 
      } 
    }
    return copy($path, $dest);
  }else{
    return false;
  }
}


// When the directory is not empty:
function export_rrmdir($dir) {
   $files = array_diff(scandir($dir), array('.','..')); 
    foreach ($files as $file) { 
      (is_dir("$dir/$file")) ? export_rrmdir("$dir/$file") : unlink("$dir/$file"); 
    } 
    return rmdir($dir); 
}

function export_compress_zip( $path ,$zip_file_basename = 'backup' ){
  if (is_dir($path) && $handle = opendir($path)){
    $zip = new ZipArchive();
    $zip_file_name = $zip_file_basename.'.zip';
    $zip_file = TEMPPATH.DS.$zip_file_name;
    if ($zip->open($zip_file, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE)!==TRUE) 
    {
        exit("cannot open <$filename>\n");
    }

    $folders = array();
    $folders[] = array('path'=>$path,'zip_path' => $zip_file_basename);
    
    for($i = 0; $i < sizeof($folders) ; $i++){
      $sub_path = $folders[$i]['path'];
      if(is_dir($sub_path)){
        foreach(array_slice(scandir($sub_path), 2) as $file_name){            
            $zip_path = $folders[$i]['zip_path'];
            $file = $sub_path.DS.$file_name;
            if(is_file($file)){
              $zip->addFromString($zip_path.DS.$file_name,  file_get_contents($file));
            }else if(is_dir($file)){
              $zip->addEmptyDir($zip_path.DS.$file_name);
              $folders[] = array('path'=>$file,'zip_path' => $zip_path.DS.$file_name);
            }
        }
      }
    }
    closedir($handle);
    export_rrmdir($path);
    $zip->close();

    // http headers for zip downloads
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"".$zip_file_name."\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($zip_file));
    ob_clean();
    flush();
    @readfile($zip_file);
    unlink($zip_file);
    exit;
  }
}