<?php
/**
 * The implemention Class for Mysql from interface database
 */

require_once(ROOTPATH.'/core/database/_database_.php');

class DB implements database{
  protected $connect;
  protected $database;
  
  public function __construct($hostname,$username,$password,$database){
    $this->database = $database;
    $this->db_connect($hostname,$username,$password);
    $this->db_set_database($database);
  }
  
  public function db_connect($hostname,$username,$password){
    $this->connect = mysqli_connect($hostname,$username,$password,$this->database)or trigger_error(mysql_error() , E_USER_ERROR);
    mysqli_set_charset($this->connect,"utf8");
  }
  public function db_set_database($database){
    $this->database = mysqli_real_escape_string($this->connect , $database);
    mysqli_select_db($this->connect,$database)or trigger_error(mysql_error() , E_USER_ERROR);
  }
  public function db_get_tables(){
    $result = mysqli_query($this->connect,'SHOW TABLES');
    $tables = array();
    if(!empty($result)){
      while($tablerow = mysqli_fetch_row($result)){
        $tables[] = $tablerow[0];
      }
    }
    return $tables;
  }
  public function db_get_table_columns($tablename){
    $tablename = mysqli_real_escape_string($this->connect,$tablename);
    $base_fields = array();
    $base_sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='INFORMATION_SCHEMA'  AND `TABLE_NAME`='COLUMNS';";
    $base_query_res = mysqli_query($this->connect,$base_sql);
    if(!empty($base_query_res)){
      while($row = mysqli_fetch_row($base_query_res)){
        $base_fields[] = $row[0];
      }
    }
    $columns = array();
    $sql = "SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS` 
          WHERE `TABLE_SCHEMA`='$this->database' 
            AND `TABLE_NAME`='$tablename';";
    $query_res = mysqli_query($this->connect,$sql);
    
    if(!empty($query_res)){
      while($row = mysqli_fetch_row($query_res)){
        $column = array();
        for($i = 0; $i < sizeof($row); $i++){
          $column[$base_fields[$i]] = $row[$i];
        }
        unset($column['TABLE_SCHEMA']);
        $columns[$column["COLUMN_NAME"]] = $column;
      }
    }
    return $columns;
    
  }
  public function db_get_table_columns_name($tablename){
    $tablename = mysqli_real_escape_string($this->connect,$tablename);
    //get columns
    $columns = array();
    $sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` 
          WHERE `TABLE_SCHEMA`='$this->database' 
            AND `TABLE_NAME`='$tablename';";
    $query_res = mysqli_query($this->connect , $sql);
    if(!empty($query_res)){
      while($row = mysqli_fetch_row($query_res)){
        $columns[$row[0]] = $row[0];
      }
    }
    return $columns;
  }
  public function db_get_table_values($tablename,$keyname='',$condition=''){
    $tablename = mysqli_real_escape_string($this->connect,$tablename);
    $base_fields = array();
    $base_sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='$this->database'  AND `TABLE_NAME`='$tablename';";
    $base_query_res = mysqli_query($this->connect,$base_sql);
    if(!empty($base_query_res)){
      while($row = mysqli_fetch_row($base_query_res)){
        $base_fields[] = $row[0];
      }
    }
    $rows = array();
    $sql = "SELECT * FROM $tablename ";
    if($condition){
      $sql.= " WHERE ".$condition;
    }
    $query_res = mysqli_query($this->connect,$sql);
    
    if(!empty($query_res)){
      while($row = mysqli_fetch_row($query_res)){
        $values= array();
       
        for($i = 0; $i < sizeof($row); $i++){
          $values[$base_fields[$i]] = $row[$i];
        }
        if(isset($values[$keyname])){
          $rows[$values[$keyname]] = $values;
        }else{
          $rows[] = $values;
        }
      }
    }
    return $rows;
  }
  
  //export the datas of table
  function db_export_datas($tablename){
    $tablename = mysqli_real_escape_string($this->connect,$tablename);
    $result = mysqli_query($this->connect,'SELECT * FROM '.$tablename);
    $num_fields = mysqli_num_fields($result);
    $querys = array();
    
    for ($i = 0; $i < $num_fields; $i++) {
      while($row = mysqli_fetch_row($result)){
        $query = 'INSERT INTO '.$tablename.' VALUES(';
          for($j=0; $j<$num_fields; $j++) {
            $row[$j] = addslashes($row[$j]);
            $row[$j] = mysqli_real_escape_string($this->connect,$row[$j]);
            if (isset($row[$j])) { $query.= '"'.$row[$j].'"' ; } else { $query.= '""'; }
            if ($j<($num_fields-1)) { $query.= ','; }
          }
          $query.= ");";
          $querys[] = $query;
      }
    }
    return $querys;
  }
  
  //export the table
  function db_export_table($tablename){
    $tablename = mysqli_real_escape_string($this->connect,$tablename);
    $query = 'DROP TABLE IF EXISTS `'.$tablename.'`;';
    $result = mysqli_query($this->connect,'SHOW CREATE TABLE `'.$tablename.'`');
    if($result){
      $create_table_query = mysqli_fetch_row($result);
      $create_table_query = $create_table_query[1];
    }

    $query .= "\n\n".$create_table_query.";";
    return $query;
  }
  public function db_query($query,$args=array()){
    foreach($args as $find => $replace){
      if(preg_match('/^![_a-zA-Z][_a-zA-Z0-9]*/',$find)){
        $replace = mysqli_real_escape_string($this->connect,$replace);
        $query = str_replace($find, $replace, $query);
      }
    }
    return mysqli_query($this->connect,$query);
  }
}
?> 

