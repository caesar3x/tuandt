<?php
/**
 * Virgo Helper
 * All common function
 * Created by Nguyen Tien Dat.
 * Date: 10/9/13
 */
defined('BASEPATH') OR exit('No direct script access allowed.');
/**
 * Get all defined contstant
 */
if(!function_exists('get_defined_constant')){
    function get_defined_constant($key)
    {
        $defined = array(
            'ENVIRONMENT' => 'development',
            'CACHE_TIME' => 86400,
            'URL_REWRITE_CACHE_KEY' => 'url-rewrite',
        );
        if(array_key_exists($key,$defined)){
            return $defined[$key];
        }
        return null;
    }
}
if(!function_exists('is_cache_file_support')){
    function is_cache_file_support()
    {
        $ci = &get_instance();
        if(get_defined_constant('ENVIRONMENT') == 'development'){
            return false;
        }
        $ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
        return $ci->cache->file->is_supported();
    }
}
if(!function_exists('_set_cache')){
    function _set_cache($key,$value)
    {
        $ci = &get_instance();
        $ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
        $time = get_defined_constant('CACHE_TIME');
        if(is_cache_file_support()){
            return $ci->cache->save($key,$value,$time);
        }
        return false;
    }
}
if(!function_exists('_get_cache')){
    function _get_cache($key)
    {
        $ci = &get_instance();
        $ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
        if(is_cache_file_support()){
            return $ci->cache->get($key);
        }
        return false;
    }
}
if(!function_exists('_delete_cache')){
    function _delete_cache($key)
    {
        $ci = &get_instance();
        $ci->load->driver('cache', array('adapter' => 'file', 'backup' => 'file'));
        if(is_cache_file_support()){
            return $ci->cache->delete($key);
        }
        return false;
    }
}
if(!function_exists('is_sr_user_loggin')){
    function is_sr_user_loggin()
    {
        get_instance()->load->model('sr_users/virgo_auth_model');
        $user = get_instance()->virgo_auth_model->get_current_sr_user();
        return (isset($user->id)) ? true : false;
    }
}
if(!function_exists('get_current_sr_user')){
    function get_current_sr_user()
    {
        get_instance()->load->model('sr_users/virgo_auth_model');
        return get_instance()->virgo_auth_model->get_current_sr_user();
    }
}

if (!function_exists("vdebug")) {
    /**
     * vdebug()
     *
     * @param mixed $data
     * @param bool $die FALSE
     * @param bool $add_var_dump FALSE
     * @param bool $add_last_query TRUE
     * @return void
     */
    function vdebug($data, $die = false, $add_var_dump = false, $add_last_query = true)
    {
        $ci = &get_instance();
        $ci->load->library('unit_test');

        $bt = debug_backtrace();
        $src = file($bt[0]["file"]);
        $line = $src[$bt[0]['line'] - 1];
        # Match the function call and the last closing bracket
        preg_match('#' . __FUNCTION__ . '\((.+)\)#', $line, $match);
        $max = strlen($match[1]);
        $varname = NULL;
        $c = 0;
        for ($i = 0; $i < $max; $i++)
        {
            if ($match[1]{$i} == "(" ) $c++;
            elseif ($match[1]{$i} == ")" ) $c--;
            if ($c < 0) break;
            $varname .= $match[1]{$i};
        }

        if(is_object($data))     $message = 'Variable holds an OBJECT';
        elseif(is_array($data))  $message = 'Variable holds an ARRAY';
        elseif(is_string($data)) $message = 'Variable holds a  STRING';
        elseif(is_int($data))    $message = 'Variable holds a  INTEGER';
        elseif(is_true($data))   $message = 'Variable holds a  TRUE BOOLEAN';
        elseif(is_false($data))  $message = 'Variable holds a  FALSE BOOLEAN';
        elseif(is_null($data))   $message = 'Variable is NULL';
        elseif(is_float($data))  $message = 'Variable is FLOAT';
        else                     $message = 'N/A';

        $output  = '<div style="clear:both;"></div>';
        $output .= '<meta charset="UTF-8" />';
        $output .= '<style>::selection{background-color:#E13300!important;color:#fff}::moz-selection{background-color:#E13300!important;color:#fff}::webkit-selection{background-color:#E13300!important;color:#fff}div.debugbody{background-color:#fff;margin:40px;font:9px/12px normal;font-family:Arial,Helvetica,sans-serif;color:#4F5155;min-width:500px}a.debughref{color:#039;background-color:transparent;font-weight:400}h1.debugheader{color:#444;background-color:transparent;border-bottom:1px solid #D0D0D0;font-size:12px;line-height:14px;font-weight:700;margin:0 0 14px;padding:14px 15px 10px;font-family:Consolas}code.debugcode{font-family:Consolas,Monaco,Courier New,Courier,monospace;font-size:12px;background-color:#f9f9f9;border:1px solid #D0D0D0;color:#002166;display:block;margin:10px 0;padding:5px 10px 15px}pre.debugpre{display:block;padding:0;margin:0;color:#002166;font:12px/14px normal;font-family:Consolas,Monaco,Courier New,Courier,monospace;background:0;border:0}div.debugcontent{margin:0 15px}p.debugp{margin:0;padding:0}.debugitalic{font-style:italic}.debutextR{text-align:right;margin-bottom:0;margin-top:0}.debugbold{font-weight:700}p.debugfooter{text-align:right;font-size:11px;border-top:1px solid #D0D0D0;line-height:32px;padding:0 10px;margin:20px 0 0}div.debugcontainer{margin:10px;border:1px solid #D0D0D0;-webkit-box-shadow:0 0 8px #D0D0D0}code.debug p{padding:0;margin:0;width:100%;text-align:right;font-weight:700;text-transform:uppercase;border-bottom:1px dotted #CCC;clear:right}code.debug span{float:left;font-style:italic;color:#CCC}</style>';
        $output .= '<div class="debugbody"><div class="debugcontainer">';
        $output .= '<h1 class="debugheader">'.$varname.'</h1>';
        $output .= '<div class="debugcontent">';
        $output .= '<code class="debugcode"><p class="debugp debugbold debutextR">:: Variable Type</p>' . $message . '</code>';
        if($add_last_query)
        {
            if($ci->db->last_query())
            {
                $output .= '<code class="debugcode"><p class="debugp debugbold debutextR">:: $ci->db->last_query()</p>';
                $output .= $ci->db->last_query();
                $output .= '</code>';
            }
        }

        $output .= '<code class="debugcode"><p class="debugp debugbold debutextR">:: print_r</p><pre class="debugpre">';
        ob_start();
        print_r($data);
        $output .= trim(ob_get_clean());
        $output .= '</pre></code>';

        if($add_var_dump)
        {
            $output .= '<code class="debugcode"><p class="debugp debugbold debutextR">:: var_dump</p><pre class="debugpre">';
            ob_start();
            var_dump($data);
            $vardump = trim(ob_get_clean());
            $vardump = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $vardump);
            $output .=  $vardump;
            $output .= '</pre></code>';
        }

        $output .= '</div><p class="debugfooter">Virgo Debug Helper © Dat Nguyen</p></div></div>';
        $output .= '<div style="clear:both;"></div>';

        if (PHP_SAPI == 'cli')
        {
            echo $varname . ' = ' . PHP_EOL . $output . PHP_EOL . PHP_EOL;
            return;
        }

        echo $output;
        if ($die)
        {
            exit;
        }
    }
}