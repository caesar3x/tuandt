<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/17/13
 */
namespace Core\View\Helper;

use Core\Cache\CacheSerializer;
use Zend\Debug\Debug;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class FilesHelper extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @param null $path
     * @return null|string
     * @throws \Exception
     */
    public function getUploadLanguageFolderPath($path = null)
    {
        if($path == null){
            $path = getcwd() . "/upload/language/";
            if (!is_dir($path)) {
                if (!@mkdir($path, 0777, true)) {
                    throw new \Exception("Unable to create destination: " . $path);
                }
            }
            return $path;
        }else{
            $pathfull = getcwd() . $path;
            return $pathfull;
        }
    }

    /**
     * @return string
     */
    public function getUploadLanguageFolderUrl()
    {
        return dirname("/upload/language/");
    }
    /**
     * @param $filename
     * @param $path
     * @param int $i
     * @return string
     */
    public function getFilenameUnique($filename,$path,$i = 1)
    {
        $fileInfo = pathinfo($filename);
        $name = $filename;
        if(file_exists($path.$filename)){
            $name = $fileInfo['filename'].'_'.$i.'.'.$fileInfo['extension'];
            if(file_exists($path.$name)){
                $i++;
                $name = self::getFilenameUnique($filename,$path,$i);
            }
        }
        return $name;
    }

    /**
     * @param $path
     * @return array
     */
    public function getDataFromCsvFile($path)
    {
        $fullpath = $this->getUploadLanguageFolderPath($path);
        $data = array();
        if(file_exists($fullpath)){
            if (($handle = fopen($fullpath, "r")) !== FALSE) {
                while (($row = fgetcsv($handle, 10000, ",")) !== FALSE) {
                    if(isset($row[0]) && !empty($row[0])){
                        $data[] = $row;
                    }
                }
                fclose($handle);
            }
        }
        return $data;
    }
}