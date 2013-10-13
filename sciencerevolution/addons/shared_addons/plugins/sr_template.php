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
            'body_class' => array(
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
                        'default' => 'page',// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
            'is_signup' => array(
                'description' => array(// a single sentence to explain the purpose of this method
                    'en' => 'Check is sign up page .'
                ),
                'single' => true,// will it work as a single tag?
                'double' => false,// how about as a double tag?
                'variables' => '',// list all variables available inside the double tag. Separate them|like|this
                'attributes' => array(
                    'is' => array(// this is the name="World" attribute
                        'type' => 'flag',// Can be: slug, number, flag, text, array, any.
                        'flags' => '',// flags are predefined values like asc|desc|random.
                        'default' => false,// this attribute defaults to this if no value is given
                        'required' => false,// is this attribute required?
                    ),
                ),
            ),
        );

        return $info;
    }
    public function is_signup()
    {
        $segment = $this->segments(1);
        if($segment && $segment != ''){
            if(strpos($segment,'contact') !== false || strpos($segment,'signup') !== false){
                $class = $this->attribute('class', 'contact-us');
            }else{
                $class = $this->attribute('class', $segment);
            }
        }else{
            $class = $this->attribute('class', 'home');
        }
    }
    public function body_class()
    {
        $segment = $this->segments(1);
        if($segment && $segment != ''){
            if(strpos($segment,'contact') !== false || strpos($segment,'signup') !== false){
                $class = $this->attribute('class', 'contact-us');
            }else{
                $class = $this->attribute('class', $segment);
            }
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