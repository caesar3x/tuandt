<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/8/13
 */
namespace Core\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceManager;

class AbstractModel
{
    protected $tableGateway;

    protected $serviceLocator;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function setServiceLocator(ServiceManager $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getEntry($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return null;
        }
        return $row;
    }

    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }
    public function getLastInsertValue()
    {
        return $this->tableGateway->getLastInsertValue();
    }

    public function getAvaiableRows()
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0));
        if (!$rowset) {
            return null;
        }
        return $rowset;
    }
}