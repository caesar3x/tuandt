<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 10/7/13
 */
namespace Core\View\Helper;

use Core\Model\Usermeta;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class UserHelper extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    public function log($path,$note = null)
    {
        if(!$path || $path == null){
            return true;
        }
        $authService = $this->serviceLocator->get('auth_service');
        if ($authService->hasIdentity()) {
            $identity = $authService->getIdentity();
            $id = $identity->id;
            $usermetaTable = $this->serviceLocator->get('UsermetaTable');
            $data = array(
                'meta_key' => 'user_log',
                'meta_value' => $path,
                'user_id' => $id
            );
            if(is_array($note)){
                $noteEncode = serialize($note);
            }else{
                $noteEncode = $note;
            }
            $data['note'] = $noteEncode;
            $usermeta = new Usermeta();
            $usermeta->exchangeArray($data);
            return $usermetaTable->save($usermeta);
        }
        return true;
    }

    /**
     * @param $path
     * @return string
     */
    public function getResourceName($path)
    {
        if(!$path || $path == null){
            return null;
        }
        $resourceTable = $this->serviceLocator->get('ResourcesTable');
        $entry = $resourceTable->getEntryByPath($path);
        if(!empty($entry)){
            return $entry->name;
        }
        return null;
    }
    /**
     * @param $data
     * @return bool
     */
    public function is_serialized( $data )
    {
        if ( !is_string( $data ) )
            return false;
        $data = trim( $data );
        if ( 'N;' == $data )
            return true;
        if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
            return false;
        switch ( $badions[1] ) {
            case 'a' :
            case 'O' :
            case 's' :
                if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                    return true;
                break;
            case 'b' :
            case 'i' :
            case 'd' :
                if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                    return true;
                break;
        }
        return false;
    }
}