<?php
namespace appsbd\Libs;

class AppExtender
{

    private static array $actions = [];
    private static array $filters = [];

    function __construct()
    {


    }


    /**
     * @param string $action_name
     * @param callable $func
     * @param int $priority
     * @param int $lengthOfParam
     */
    static function AddAction( string $action_name, callable $func, $priority=10, $lengthOfParam=1)
    {

        if (is_callable($func)) {
            $std=new \stdClass();
            $std->lop=$lengthOfParam;
            $std->callable=$func;
            if(!isset(self::$actions[$action_name])){
                self::$actions[$action_name]            =[];
                self::$actions[$action_name][$priority] =[];
            }
            self::$actions[$action_name][$priority][] = $std;
        }
    }


    /**
     * @param string $filter_name
     * @param callable $func
     * @param int $priority
     * @param int $lengthOfParam
     */
    static function AddFilter( string $filter_name, callable $func, $priority=10, $lengthOfParam=1)
    {
        $filter_name = strtolower($filter_name);
        if (is_callable($func)) {
            $std=new \stdClass();
            $std->lop=$lengthOfParam;
            $std->callable=$func;
            if(!isset(self::$filters[$filter_name])){
                self::$filters[$filter_name]=[];
                self::$filters[$filter_name][$priority]=[];
            }
            self::$filters[$filter_name][$priority][] = $std;
        }
    }
    static function DoActionsParams($hook_name, $params=[]){
        if (isset(self::$actions[$hook_name])) {
            ksort(self::$actions[$hook_name]);
            foreach (self::$actions[$hook_name] as $pri_array) {
                foreach ($pri_array as $hook) {
                    if (is_callable($hook->callable)) {
                        if($hook->lop>0){
                            if( count($params) > $hook->lop) {
                                $nargs = array_splice($params, 0, $hook->lop);
                            }else{
                                $nargs=$params;
                            }
                        }else{
                            $nargs=[];
                        }
                        call_user_func_array($hook->callable, $nargs);
                    }
                }
            }

        }
    }
    static function DoActions($hook_name, ...$args){
       self::DoActionsParams($hook_name,$args);
    }
    static function DoActionsRef($hook_name,&...$args)
    {
        if (isset(self::$actions[$hook_name])) {
            ksort(self::$actions[$hook_name]);
            foreach (self::$actions[$hook_name] as $pri_array) {
                foreach ($pri_array as $hook) {
                    if (is_callable($hook->callable)) {
                        call_user_func_array($hook->callable, $args);
                    }
                }
            }
        }
    }

    /**
     * @param $name
     * @param array $args
     */
    static function ApplyFiltersParams($name,$args=[])
    {
        $return_value=$args[0];
        if (isset(self::$filters[$name])) {
            ksort(self::$filters[$name]);
            foreach (self::$filters[$name] as $pri_array) {
                foreach ($pri_array as $hook) {
                    if (is_callable($hook->callable)) {

                        if ($hook->lop > 1) {
                            if (count($args) > $hook->lop) {
                                $nargs = array_splice($args, 0, $hook->lop);
                            } else {
                                $nargs = $args;
                            }
                        } else {
                            $nargs = [];
                            $nargs[]  = $args[0];
                        }
                        $return_value = call_user_func_array($hook->callable, $nargs);
                        if (gettype($args[0]) == gettype($return_value)) {
                            $args[0] = $return_value;
                        }

                    }
                }
            }

        }
        return $return_value;
    }

    static function ApplyFilters($name,...$args)
    {
        self::ApplyFiltersParams($name,$args);
    }

}
