<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/8/13
 */
namespace Core\View\Helper;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class TokenString extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function __invoke($content = null, $chars, $end = false)
    {
        if(null === $content){
            return;
        }
        if (strlen ( $content ) > $chars)
        {
            $content = str_replace ( '&nbsp;', ' ', $content );
            $content = str_replace ( "\n", '', $content );
            $content = strip_tags ( trim ( $content ) );
            $content = preg_replace ( '/\s+?(\S+)?$/', '', mb_substr ( $content, 0, $chars ) );
            if($end){
                $content = trim ( $content ) . '[..]';
            }else{
                $content = trim ( $content );
            }
            return $content;
        }
        else
        {
            $content = strip_tags ( trim ( $content ) );
            if($end){
                $content = trim ( $content ) . '[..]';
            }else{
                $content = trim ( $content );
            }
            return $content;
        }
    }
    public function implement($content = null, $chars, $end = true)
    {
        return $this->__invoke($content, $chars, $end);
    }
}