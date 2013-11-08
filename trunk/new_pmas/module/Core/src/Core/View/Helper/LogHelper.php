<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/7/13
 */
namespace Core\View\Helper;

use Zend\Http\Request;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class LogHelper extends CoreHelper
{
    protected $_extention = '.log';

    protected $_public_folder = 'public';
    
    public function __construct(ServiceManager $serviceLocator,Request $request)
    {
        parent::__construct($serviceLocator,$request);
    }

    /**
     * @param $pathname
     * @return Logger
     */
    public function initFile($pathname)
    {
        $writer = new Stream($pathname);
        $logger = new Logger();
        $logger->addWriter($writer);
        return $logger;
    }
    public function readFile($pathname)
    {
        $writer = new Stream($pathname);
        $logger = new Logger();
        $logger->addWriter($writer);
        return $logger;
    }
    public function post($post_id)
    {
        if(!$post_id){
            return false;
        }
        $pathname = $this->postLogPath($post_id);
        $logger = $this->initFile($pathname);
        $logger->info('anh day');
    }
    /**
     * @param $post_id
     * @return string
     * @throws \Exception
     */
    public function postLogPath($post_id)
    {
        $path = getcwd() . "/".$this->_public_folder."/log/posts/";
        if (!is_dir($path)) {
            if (!@mkdir($path, 0777, true)) {
                throw new \Exception("Unable to create destination: " . $path);
            }
        }
        if($post_id){
            $pathname = $path.$post_id.$this->_extention;
            return $pathname;
        }
    }
    public function systemLogPath()
    {
        $path = getcwd() . "/".$this->_public_folder."/log/";
        if (!is_dir($path)) {
            if (!@mkdir($path, 0777, true)) {
                throw new \Exception("Unable to create destination: " . $path);
            }
        }
        $pathname = $path.'system'.$this->_extention;
        return $pathname;
    }
    public function systemDebugPath()
    {
        $path = getcwd() . "/".$this->_public_folder."/log/";
        if (!is_dir($path)) {
            if (!@mkdir($path, 0777, true)) {
                throw new \Exception("Unable to create destination: " . $path);
            }
        }
        $pathname = $path.'debug'.$this->_extention;
        return $pathname;
    }
}