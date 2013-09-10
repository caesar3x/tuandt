<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/5/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Slugify extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __invoke($string = null)
    {
        if(null === $string){
            return;
        }
        $string = utf8_decode($string);
        $string = html_entity_decode($string);

        $a = 'ÀÁÂÃÄÅẢẠĂẮẴẶẲẰẤẦẪẨẬàáạâãäåảăắằẳẵặấầẫẩậÒÓÔÕỎÖØỌỐỒỘỖỔƠỚỜỠỢỞòóôỏõöøọốồỗộổơớờợỡởÈÉÊẺËẾỀỆỄỂèéêẻëếềễệểÇçÌỈÍÎÏỈĨỊìíîïĩỉịÙÚÛÜŨỤỦƯÙỨỮỰỬùúûüủũụưừứữựửÿỳýỹỵỷÑñĐđ·/_,:;%';
        $b = 'AAAAAAAAAAAAAAAAAAAaaaaaaaaaaaaaaaaaaaOOOOOOOOOOOOOOOOOOOoooooooooooooooooooEEEEEEEEEEeeeeeeeeeeCcIIIIIIIIiiiiiiiUUUUUUUUUUUUUuuuuuuuuuuuuuyyyyyyNnDd-------';
        $string = strtr($string, utf8_decode($a), $b);

        $ponctu = array("?", ".", "!", ",");
        $string = str_replace($ponctu, "", $string);

        $string = trim($string);
        $string = preg_replace('/([^a-z0-9]+)/i', '-', $string);
        $string = strtolower($string);

        if (empty($string)) return 'n-a';

        return utf8_encode($string);
    }
    public function implement($string)
    {
        return $this->__invoke($string);
    }
}