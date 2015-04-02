<?php
/*****************************************
 * Load Modules Of Your Sys
 *******************************************/

class Modules{
  private $root_path;
  private $name;
  private $sysname;
  protected $modules;
  protected $use_modules;
  public $db;
  public $configs;
  
  public function __construct($sysname='',$root_path='',$use_modules=array()){
    $this->root_path = $root_path;
    $this->modules = array(
      'modules'=>array(),
      'related' => array(),
    );
    $this->get_sys($sysname,$root_path,$use_modules);
  }
  
  public function get_sys($sysname,$root_path,$use_modules){
    if($sysname && $root_path){
      $this->root_path = $root_path;
      $analysis = array(
        'results' => array(),
        'info'=>array(),
        'waits'=>array(),
        'needs'=>array(),
        'no_exist' => array(),
      );
      //get all modules
      if(file_exists($this->root_path)){
        $this->name = $sysname;
        $this->sysname = $sysname;
        if(file_exists($this->root_path.'/'.$sysname.'.info')){
          $configs = config_get_config($this->root_path.'/'.$sysname.'.info');
          $this->name = isset($configs['name']) ? $configs['name'] : $this->sysname;
          $this->sysname = isset($configs['sysname']) ? $configs['sysname'] : $this->sysname;
          
          //TODO: check use which type of databas
          $hostname = isset($configs['hostname']);
          $database = isset($configs['database']);
          $username = isset($configs['username']);
          $password = isset($configs['password']) ? $configs['password'] : '';
         
          if($hostname && $database && $username && $password) { 
              $this->db = new DB($hostname,$username,$password,$database); 
          }
	  unset($configs['hostname']);
          unset($configs['database']);
          unset($configs['username']);
          unset($configs['password']);
          $this->configs = $configs;
        }
        //load base file of this sys
        if(file_exists($this->root_path.'/'.$sysname.'.base')){
          require_once($this->root_path.'/'.$sysname.'.base');
        }
        
        $this->refer_sys_modules_lists($this->root_path);
      }
      
      
      $this->use_modules = array();
      $custom_modules = $this->get_sys_custom_modules();
      foreach($use_modules as $module){
        $this->use_modules[$module] = true;
      }
      
      foreach($this->modules['modules'] as $module => $value){
        if(isset($custom_modules[$module])){
          $this->unset_not_use_modules($module);
        }
      }
      
      //arrange modules
      $order = array();
      foreach($this->modules['related'] as $key=>$requires){
          if(!isset($analysis['info'][$key])){
              $this->analysis_modules($analysis,$this->modules['related'],$key);
          }
      }
    
      foreach($analysis['results'] as $values){
            foreach($values as $value){
                $order[] = $value;
            }
        }
      
      unset($analysis['results']);
      unset($analysis['info']);
      unset($analysis['waits']);
      $this->modules['related'] = $analysis;
      $this->modules['order'] =  $order;
    }
    return $this->modules;
  }
 
  //get the modules info lists
  private function refer_sys_modules_lists($path){
    foreach(array_slice(scandir($path), 2) as $file){
      $module_file = $path.'/'.$file;
      $module_name = pathinfo($file);
      if(is_file($module_file) 
      && isset($module_name['extension']) 
      && $module_name['extension'] == 'module'){
        $module_name = $module_name["filename"];
        //if the module is marked, change the exist status to true
        require_once($module_file);
        
        $this->modules['modules'][$module_name] = array(
            'path' => $module_file,
            'tables' => array(),
        );
        //get module infomation in hook_module_info()
        $hook_fun_name = $this->sysname.'_'.$module_name."_module_info"; 
        if(function_exists($hook_fun_name)){
          $module_info = $hook_fun_name();
          $this->modules['modules'][$module_name]['tables'] = isset($module_info['tables']) ? $module_info['tables']  : array();
          $this->modules['modules'][$module_name]['custom'] = isset($module_info['custom']) ? $module_info['custom']  : false;
          $this->modules['related'][$module_name] = isset($module_info['dependencies']) ? $module_info['dependencies']  : array();
        }else{
          $this->modules['modules'][$module_name]['tables']  = array();
          $this->modules['modules'][$module_name]['custom']  = false;
          $this->modules['related'][$module_name] = array();
        }
      }
      elseif(is_dir($module_file)){
       $this-> refer_sys_modules_lists($module_file);
      }
    }
  }
  private function unset_not_use_modules($module){
    if(empty($this->use_modules)) return;
    if(!isset($this->use_modules[$module])){
      unset($this->modules['modules'][$module]);
      unset($this->modules['related'][$module]);
    }
  }
  //analysis the modules relative
  private function analysis_modules(&$results,$fulldata,$current){
    try{
        $deep = 0;
        if(!isset($fulldata[$current])){
            $results['no_exist'][] = $current;
            return -1;
        }
        $requires = $fulldata[$current];
        //if not requires data
        if(empty($requires)){
            if(!isset($results['info'][$current])){
                $results['results'][0][] = $current;
                $results['info'][$current] = array(
                  'deep' => 0,
                );
            }
        }else{
            $deep = 1;
            $results['waits'][$current] = $current;
            foreach($requires as $require){
                if(isset($results['waits'][$require])) throw new Exception('Require Looped Error For :'.$current);
                if(!isset($results['info'][$require])){
                    $re_deep = $this->analysis_modules($results,$fulldata,$require);
                }else{
                    $re_deep = $results['info'][$require]['deep'];
                }
                if($re_deep == -1){
                    if(!isset($results['info'][$current])){
                        $results['info'][$current] = array(
                          'deep' => -1,
                        );
                        $results['needs'][$current] = array($require);
                    }else{
                        $results['needs'][$current][] = $require;
                    }
                    continue;
                }
                $deep = $re_deep >= $deep ? $re_deep+1: $deep;
            }
            unset($results['waits'][$current]);
            if(!isset($results['info'][$current])){
                $results['results'][$deep][] = $current;
                $results['info'][$current] = array(
                  'deep' => $deep,
                );
            }
        }
        return $deep;
    }catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
  }
  /**
   * get system name
   */
  public function get_sys_name(){
    return $this->sysname;
  }
  /**
   * get the root path of system
   */
  public function get_sys_root_path(){
    return $this->root_path;
  }
  
  /**
   * get the path of module file
   */
  public function get_sys_module_file_path($module_name){
    if(isset($this->modules['modules'][$module_name])){
      return $this->modules['modules'][$module_name]['path'];
    }
    return null;
  }
  
  /**
   * get the module loading order
   */
  public function get_sys_module_loading_order(){
    return $this->modules['order'];
  }
  
  /**
   * get custom modules
   */
  public function get_sys_custom_modules(){
    $modules = array();
    foreach($this->modules['modules'] as $module=>$value){
      if($value['custom']){
        $modules[$module] = $module;
      }
    }
    return $modules;
  }
  /**
   * invoke the function of sys modules
   */
  public function module_invoke_functions($function_name,$args=array()){;
    foreach($this->modules['order'] as $module_name){  
      $hook_fun_name = $this->sysname.'_'.$module_name."_".$function_name;
      if(function_exists($hook_fun_name)){
         call_user_func_array($hook_fun_name,$args);
      }
    }
  }
}
