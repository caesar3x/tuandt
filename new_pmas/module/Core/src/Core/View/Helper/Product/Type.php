<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 11/2/13
 */
namespace Core\View\Helper\Product;

use Core\View\Helper\CoreHelper;

class Type extends CoreHelper
{
    /**
     * @param null $id
     * @return null
     */
    public function getName($id = null)
    {
        if(null == $id || $id == 0){
            return null;
        }
        $typeTable = $this->serviceLocator->get('ProductTypeTable');
        return $typeTable->getTypeNameById($id);
    }
    /**
     * Get type name by id
     * @param $type_name
     * @return null
     */
    public function getTypeIdByName($type_name)
    {
        if(!$type_name){
            return null;
        }
        $typeTable = $this->serviceLocator->get('ProductTypeTable');
        $entry = $typeTable->getEntryByName($type_name);
        if(!empty($entry)){
            return $entry->type_id;
        }
        return null;
    }
}