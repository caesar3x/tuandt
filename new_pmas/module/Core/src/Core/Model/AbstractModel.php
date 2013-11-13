<?php
/**
 * Created by Nguyen Tien Dat.
 * Date: 8/8/13
 */
namespace Core\Model;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
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

    /**
     * @return null|\Zend\Db\ResultSet\ResultSet
     */
    public function getAvaiableRows()
    {
        $rowset = $this->tableGateway->select(array('deleted' => 0));
        if ($rowset->count() <= 0) {
            return null;
        }
        return $rowset;
    }
    /**
     * @return int
     */
    public function getTotalRows()
    {
        $availableRows = $this->getAvaiableRows();
        if(!empty($availableRows)){
            return $availableRows->count();
        }
        return 0;
    }
    protected function getViewHelper($name)
    {
        return $this->serviceLocator->get('viewhelpermanager')->get($name);
    }
    /**
     * @param $message
     */
    protected function log($message)
    {
        $writer = new Stream($this->getViewHelper('log')->systemLogPath());
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->info($message);
    }

    /**
     * @param $message
     */
    protected function log_debug($message)
    {
        $writer = new Stream($this->getViewHelper('log')->systemDebugPath());
        $logger = new Logger();
        $logger->addWriter($writer);
        $logger->debug($message);
    }
}