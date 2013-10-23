<?php
/**
 * Created by Nguyen Tien Dat.
 * Email : datnguyen.cntt@gmail.com
 * Date: 10/23/13
 */
namespace Application\Form;

use Core\Form\AbstractForm;
use Zend\Form\Element\File;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\ServiceManager\ServiceLocatorInterface;

class LanguageForm extends AbstractForm
{
    public function __construct(ServiceLocatorInterface $sm)
    {
        parent::__construct($sm,'language-form');
        $this->setAttribute('enctype' , 'multipart/form-data');
        $country = new Text('lang_country');
        $country->setAttributes(array(
            'id' => 'lang_country',
            'class' => 'form-control'
        ));
        $code = new Text('lang_code');
        $code->setAttributes(array(
            'id' => 'lang_code',
            'class' => 'form-control'
        ));
        $sort = new Text('sort_order');
        $sort->setAttributes(array(
            'id' => 'sort_order',
            'class' => 'form-control'
        ));
        $file_path = new Hidden('file_path');
        $file_upload = new File('file_upload');
        $file_upload->setAttributes(array(
            'id' => 'file_upload',
        ));
        $this->add($country)->add($code)->add($sort)->add($file_upload)->add($file_path);
    }
}