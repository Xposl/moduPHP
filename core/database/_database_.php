<?php

interface database {
  //connect
  public function db_connect($server,$name,$password);
  public function db_set_database($database);
  public function db_get_tables();
  public function db_get_table_columns($tablename);
  public function db_get_table_columns_name($tablename);
  public function db_get_table_values($tablename,$keyname,$condition);
  public function db_query($query,$args=array());
}
