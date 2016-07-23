<?php
class Hook {

    static private  $hooks       =   array();

    /**
     * [add 注册hook]
     * @param [type]  $hook     [description]
     * @param [type]  $class [description]
     * @param boolean $first    [description]
     */
    static public function add($hook, $class){
        if(!isset(self::$hooks[$hook])){
            self::$hooks[$hook] = array();
        }
        if(is_array($class)){
            self::$hooks[$hook] = array_merge(self::$hooks[$hook], $class);
        }else {
            self::$hooks[$hook][] = $class;
        }
    }
    /**
     * 执行钩子
     * @param string $hook 钩子名称
     * @param mixed $params 传入参数
     * @return void
     */
    static public function execute($hook,$flag = false,&$params = null) {
        if(!$hook) return FALSE;
        if(isset(self::$hooks[$hook])) {
            foreach (self::$hooks[$hook] as $class) {
                list($type,$name) = explode('/',$class);
                if($type == 'module'){
                    require_cache(APP_PATH.config('DEFAULT_H_LAYER').'/'.$name.'/'.'hook.class.php');
                    $classname = $name.'_hook';
                }elseif ($type == 'plugin') {
                    require_cache(APP_PATH.'plugin/'.$name.'/'.'hook.class.php');
                    $classname = 'plugin_'.$name;
                }else{
                    return FALSE;
                }
                $result = self::class_exec($classname,$hook,$params);
                
                if($flag){
                    if(is_array($result)){
                        $return[] = $result;
                    }elseif(is_string($result)){
                        $return .= $result;
                    }
                }
            }
            if($flag){
                return $return;
            }
        }else{
            return FALSE;
        }
    }
    /**
     * [class_exec 执行类]
     * @param  [type] $class   [description]
     * @param  string $hook    [description]
     * @param  [type] &$params [description]
     * @return [type]          [description]
     */
    public static function class_exec($class, $hook = '', &$params = null){
        $obj = new $class();
        if(is_callable(array($obj, $hook))){
            return  $obj->$hook($params);
        }
    }
}