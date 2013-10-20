<?php
/**
 * Science Revolution Template Plugin
 *
 * Science Revolution Template Plugin.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Addon\Plugins
 * @copyright	Copyright (c) 2009 - 2010, PyroCMS
 */
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/12/13
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Plugin_Sr_template extends Plugin
{
    public $version = '1.0.0';

    public $name = array(
        'en'	=> 'Science Revolution Template Plugin'
    );

    public $description = array(
        'en'	=> 'Science Revolution Template Plugin.'
    );
    public function _self_doc()
    {
        $info = array(
            'content_class' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Set content class.'
                ),
                'single' => true,// will it work as a single tag?
                'double' => false,// how about as a double tag?
                'variables' => '',// list all variables available inside the double tag. Separate them|like|this
                'attributes' => array(
                    'class' => array(// this is the name="World" attribute
                        'type' => 'text',// Can be: slug, number, flag, text, array, any.
                        'flags' => '',// flags are predefined values like asc|desc|random.
                        'default' => '',// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
            'login_class' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Set body class.'
                ),
                'single' => true,// will it work as a single tag?
                'double' => false,// how about as a double tag?
                'variables' => '',// list all variables available inside the double tag. Separate them|like|this
                'attributes' => array(
                    'class' => array(// this is the name="World" attribute
                        'type' => 'text',// Can be: slug, number, flag, text, array, any.
                        'flags' => '',// flags are predefined values like asc|desc|random.
                        'default' => 'notlogin',// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
            'get_user_name' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Get full name of current user .'
                ),
                'single' => true,// will it work as a single tag?
                'double' => false,// how about as a double tag?
                'variables' => '',// list all variables available inside the double tag. Separate them|like|this
                'attributes' => array(
                    'is' => array(// this is the name="World" attribute
                        'type' => 'text',// Can be: slug, number, flag, text, array, any.
                        'flags' => '',// flags are predefined values like asc|desc|random.
                        'default' => '',// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
            'is_loggin' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Check is user loggedin .'
                ),
                'single' => true,// will it work as a single tag?
                'double' => false,// how about as a double tag?
                'variables' => '',// list all variables available inside the double tag. Separate them|like|this
                'attributes' => array(
                    'is' => array(// this is the name="World" attribute
                        'type' => 'text',// Can be: slug, number, flag, text, array, any.
                        'flags' => '',// flags are predefined values like asc|desc|random.
                        'default' => 'no',// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
            'flash_messages' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Get flash messages.'
                ),
                'single' => true,// will it work as a single tag?
                'double' => false,// how about as a double tag?
                'variables' => '',// list all variables available inside the double tag. Separate them|like|this
                'attributes' => array(
                    'messages' => array(// this is the name="World" attribute
                        'type' => 'text',// Can be: slug, number, flag, text, array, any.
                        'flags' => '',// flags are predefined values like asc|desc|random.
                        'default' => '',// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
        );

        return $info;
    }

    /**
     * Get flash messages
     * @return string
     */
    public function flash_messages()
    {
        $this->load->library('session');
        $messages = $this->session->flashdata('flash_messages');
        $html = '';
        $segment = $this->segments(1);
        if($segment && $segment != ''){
            $html = '<div class="container">';
        }
        if(!empty($messages)){
            $namespaces = array('success','error');
            foreach($namespaces as $ns){
                if(isset($messages[$ns])){
                    $html .= '<div class="messages '.$ns.'">';
                    if(is_array($messages[$ns])){
                        foreach($messages[$ns] as $msg){
                            $html .= '<p>'.$msg.'</p>';
                        }
                    }else{
                        $html .= '<p>'.$messages[$ns].'</p>';
                    }
                    $html .= '</div>';
                }
            }
        }
        if($segment && $segment != ''){
            $html .= '</div>';
        }
        $msgHtml = $this->attribute('messages', $html);
        return $html;
    }

    /**
     * Check user is loggin
     * @return string
     */
    public function is_loggin()
    {
        $this->load->helper('virgo');
        if(is_sr_user_loggin()){
            return 'yes';
        }
        return 'no';
    }

    /**
     * check is g
     */
    public function get_user_name()
    {
        $this->load->helper('virgo');
        $this->load->helper('sr_user');
        if(is_sr_user_loggin()){
            return get_sr_user_name();
        }
        return null;
    }
    public function login_class()
    {
        $this->load->helper('virgo');
        if(is_sr_user_loggin()){
            return 'loggedin';
        }
        return 'notlogin';
    }
    public function content_class()
    {
        $method = $this->router->fetch_method();
        $class = $this->router->fetch_class();
        $module = $this->router->fetch_module();
        $segment = $this->segments(1);
        if($segment && $segment != ''){
            $cs = 'hasform';
            if($method == 'profile' && $class == 'sr_users' && $module == 'sr_users'){
                $cs .= ' profile';
            }
            if($method == 'index' && $class == 'article' && $module == 'catalog'){
                $cs .= ' view-article';
            }
            if($method == 'add' && $class == 'article' && $module == 'catalog'){
                $cs .= ' upload-main';
            }
            $class = $this->attribute('class', $cs);
        }else{
            $class = $this->attribute('class', 'home');
        }
        return $class;
    }
    public function segments($n)
    {
        return $this->uri->segment($n);
    }
}