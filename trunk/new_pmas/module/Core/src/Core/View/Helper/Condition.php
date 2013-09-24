<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 9/20/13
 */
namespace Core\View\Helper;

use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

class Condition extends AbstractHelper
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function __invoke($id = null,$tdm = true)
    {
        if($id == null || $id == 0){
            return null;
        }
        if($tdm == true){
            $tdmConditionTable = $this->serviceLocator->get('TdmProductConditionTable');
        }else{
            $tdmConditionTable = $this->serviceLocator->get('RecyclerProductConditionTable');
        }
        $conditionEntry = $tdmConditionTable->getEntry($id);
        if(empty($conditionEntry)){
            return null;
        }
        return $conditionEntry->name;
    }

    /**
     * @param null $id
     * @param bool $tdm
     * @return null
     */
    public function implement($id = null,$tdm = true)
    {
        return $this->__invoke($id,$tdm);
    }

    /**
     * @param $condition
     * @return null
     */
    public function getRecyclerConditionIdByName($condition)
    {
        if(!$condition){
            return null;
        }
        $ConditionTable = $this->serviceLocator->get('RecyclerProductConditionTable');
        $entry = $ConditionTable->getEntryByName($condition);
        if(!empty($entry)){
            return $entry->condition_id;
        }
        return null;
    }

    /**
     * @param $condition
     * @return null
     */
    public function getTdmConditionIdByName($condition)
    {
        if(!$condition){
            return null;
        }
        $ConditionTable = $this->serviceLocator->get('TdmProductConditionTable');
        $entry = $ConditionTable->getEntryByName($condition);
        if(!empty($entry)){
            return $entry->condition_id;
        }
        return null;
    }
}