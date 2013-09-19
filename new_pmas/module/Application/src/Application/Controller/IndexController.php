<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use BasicExcel\Writer\Xlsx;
use Core\Model\SimpleXLSX;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Router\Console\Simple;
use Zend\View\Model\ViewModel;
use SimpleExcel\SimpleExcel;

class IndexController extends AbstractActionController
{
    public function auth()
    {
        $sm = $this->getServiceLocator();
        $authService = $sm->get('auth_service');
        if (! $authService->hasIdentity()) {
            return $this->redirect()->toUrl('login');
        }
    }
    public function getMessages()
    {
        $sm = $this->getServiceLocator();
        return $sm->get('messages');
    }
    public function __construct()
    {

    }
    public function indexAction()
    {
        $this->auth();
        return new ViewModel();
    }
    public function testAction()
    {
        $data = array(
            'Names' => array(
                array('Nr.', 'Name', 'E-Mail'),
                array(1, 'Jane Smith', 'jane.smith@fakemail.com'),
                array(2, 'John Smith', 'john.smith@fakemail.com')
            ),
            'Ages' => array(
                array('Nr.', 'Age'),
                array(1, 103),
                array(2, 21)
            ),
            'Genders' => array(
                array('Nr.', 'Gender'),
                array(1, 'Male'),
                array(2, 'Female')
            )
        );

        $csvwriter = new Xlsx();
        $csvwriter->fromArray($data);
        //$csvwriter->writeFile('myfilename.csv');
        //OR
        $csvwriter->download('myfilename.xlsx');
        die;
        $xlsx = new SimpleXLSX('dat.xlsx');
        Debug::dump($xlsx->rows());
        die('--');
        $data = array('id' => '1','name' => 'Recycler Name','brand' => 'Brand','model' => 'Model');
        $excel = new SimpleExcel('CSV');
        $excel->writer->addRow($data);
        $excel->writer->saveFile('example');
        die('export sample');
        $excel = new SimpleExcel('CSV');
        $excel->parser->loadFile('translate.csv');
        Debug::dump($excel->parser->getRow(1)) ;
        echo '<br/>';
        die('test action');
    }
}
