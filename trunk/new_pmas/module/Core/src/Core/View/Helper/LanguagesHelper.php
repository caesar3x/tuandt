<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Core\View\Helper;

use Core\Cache\CacheSerializer;
use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class LanguagesHelper extends AbstractHelper
{
    protected $serviceLocator;

    protected $defaul_lang = 'en';

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function getFilesHelper()
    {
        return $this->serviceLocator->get('viewhelpermanager')->get('files');
    }
    /**
     * @param $code
     * @return bool
     */
    public function setLanguageSession($code)
    {
        $session = new Container('lang');
        $session->current_lang = $code;
        return true;
    }

    /**
     * @return string
     */
    public function getLanguageSession()
    {
        $session = new Container('lang');
        if($session->current_lang)
        {
            return $session->current_lang;
        }else{
            return $this->defaul_lang;
        }
    }
    public function updateLanguageSession($id,$code)
    {
        $languagesTable = $this->serviceLocator->get('LanguagesTable');
        $entry = $languagesTable->getLang($id);
        if(!empty($entry)){
            $lang_code = $entry->lang_code;
            if($lang_code != $code){
                $this->setLanguageSession($lang_code);
            }
        }
        return true;
    }
    /**
     * @param $path
     * @return array
     */
    public function readTranslateData($path)
    {
        $data = $this->getFilesHelper()->getDataFromCsvFile($path);
        $translate = array();
        if(!empty($data)){
            foreach($data as $row){
                $translate[$row[0]] = $row[1];
            }
        }
        return $translate;
    }

    /**
     * @param $code
     * @param $data
     * @return \Zend\Cache\Storage\Adapter\Filesystem
     */
    public function cachingTranslateData($code,$data)
    {
        $cache = CacheSerializer::init();
        $key = $code.'-lang';
        $cache->setItem($key,$data);
        return $cache;
    }

    /**
     * @param $code
     * @return \Zend\Cache\Storage\Adapter\Filesystem
     */
    public function removeCachedTranslateData($code)
    {
        $cache = CacheSerializer::init();
        $key = $code.'-lang';
        $cache->removeItem($key);
        return $cache;
    }

    /**
     * @param $code
     * @return array|mixed|null
     */
    public function getTranslateData($code)
    {
        if(!$code){
            return null;
        }
        $cache = CacheSerializer::init();
        $key = $code.'-lang';
        $dataCache = $cache->getItem($key);
        $languagesTable = $this->serviceLocator->get('LanguagesTable');
        if(!$languagesTable->has_lang($code)){
            return null;
        }
        if(empty($dataCache)){
            $entry = $languagesTable->getByCode($code);
            $file_path = $entry->file_path;
            $dataCache = $this->readTranslateData($file_path);
            $this->cachingTranslateData($code,$dataCache);
        }
        return $dataCache;
    }

    /**
     * @return string
     */
    public function getLanguagesDropdown()
    {
        $languagesTable = $this->serviceLocator->get('LanguagesTable');
        $langs = $languagesTable->getLanguages();
        $current_lang = $this->getLanguageSession();
        $html = '';
        if(!empty($langs)){
            $html .= '<select name="global_lang" id="global-lang">';
            $html .= '<option value="">'.'Choose a language'.'</option>';
            foreach($langs as $lang){
                if($lang == $current_lang){
                    $html .= '<option value="'.$lang->lang_code.'">';
                }else{
                    $html .= '<option selected value="'.$lang->lang_code.'">';
                }
                $html .= $lang->lang_country;
                $html .= '</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }
}