<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Science Events Class
 * 
 * @package        PyroCMS
 * @subpackage    Science Contact Module
 * @category    events
 * @author        datnguyen.cntt@gmail.com
 * @website        http://vdragons.com
 */
class Events_Languages {
    
    protected $ci;
    
    public function __construct()
    {
        $this->ci =& get_instance();
        
        //register the public_controller event
        Events::register('public_controller', array($this, 'run'));

		//Events::register('soslaundry_event', array($this, 'run'));
    }
    
    public function run()
    {

    }
    
}
/* End of file events.php */