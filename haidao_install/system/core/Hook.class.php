<?php
class Hook {

    static private  $hooks       =   array();

    /**
     * 执行钩子
     * @param string $tag 标签名称
     * @param mixed $params 传入参数
     * @return void
     */
    static public function execute($hook, $params, $flag = FALSE) {
        $modules = cache('module', '', 'common');
        $modules = array_keys($modules);
        $hook_cache = cache('hooks','','common');
        $hooks = $hook_cache ? array_keys($hook_cache) : array();
        $hookfile = !empty($hooks) ? array_merge($modules,$hooks) : $modules;
        foreach ($hookfile AS $hookname) {
            if(in_array($hookname,$modules)){
                $file = APP_PATH.config('DEFAULT_H_LAYER').'/'.$hookname.'/'.'hook.class.php';
            }else{
                $file = APP_PATH.'plugin/'.$hookname.'/'.'hook.class.php';
            }
            require_cache($file);
        }
        foreach (self::get_hook_class() AS $plugin) {
            if($plugin->hasMethod($hook)){
                $reflectionMethod= $plugin->getMethod($hook);
                $classname = $plugin->getName();
                $substr = strpos($classname,'plugin_');
                if($substr === 0){
                    $subclass = str_replace('plugin_','',$classname);
                    $pluginInstance = $plugin->newInstance($subclass);
                }else{
                    $pluginInstance = $plugin->newInstance();
                }
                $return = $reflectionMethod->invoke($pluginInstance,$params); 
                if(is_array($return)){
                    $result[] = $return;
                } else{
                    $result .= (string)$return;
                }
            }
        }
        if($result !== FALSE){
            return $result;
        }
    }
    /**
     * [get_hook_class 获取插件和模块hook的反射]
     * @return [type] [description]
     */
    static private function get_hook_class(){
        $plugins = array();
        foreach(get_declared_classes() as $class) {  
           $reflectionClass= new ReflectionClass($class); 
            if($reflectionClass->isSubclassOf('plugin')) {
               $plugins[]= $reflectionClass;  
            }  
            if($reflectionClass->isSubclassOf('Hook')) {  
               $plugins[]= $reflectionClass;  
            }  
        }  
        return $plugins;
    }
}