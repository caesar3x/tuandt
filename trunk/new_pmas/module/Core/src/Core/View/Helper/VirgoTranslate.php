<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Core\View\Helper;

use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class VirgoTranslate extends AbstractHelper
{
    protected $serviceLocator;

    protected $defaul_lang = 'en';

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return mixed
     */
    public function getLangHelper()
    {
        return $this->serviceLocator->get('viewhelpermanager')->get('lang');
    }

    /**
     * @param $string
     * @return mixed
     */
    public function __invoke($string)
    {
        $current_lang = $this->getLangHelper()->getLanguageSession();
        /*$this->getLangHelper()->removeCachedTranslateData($current_lang);*/
        $translate = $this->getLangHelper()->getTranslateData($current_lang);
        if(!is_array($translate)){
            return $string;
        }
        if(array_key_exists($string,$translate)){
            return $translate[$string];
        }
        return $string;
    }

    /**
     * @param $string
     * @return mixed
     */
    public function trans($string)
    {
        return $this->__invoke($string);
    }
}