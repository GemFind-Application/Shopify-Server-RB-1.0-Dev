<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
defined('BASEPATH') OR exit('No direct script access allowed');
class ReactConfig extends CI_Controller {
	
	function __construct()
	{  	
		parent::__construct();
		
		$this->load->library('diamond_lib');
		$this->load->model('general_model');
		$this->load->library('ringbuilder_lib');
		$this->load->helper('cookie');
		$this->load->library('email');
		$this->load->library('form_validation');
        $this->load->helper('form');
	}

	public function index()
	{	

		//echo 'play';

		$shop = $_GET['shop']; 
		
		$dconfig = $this->general_model->getDiamondConfig($shop);	
        $resultdata = array();
	    $resultdata['data'] = $dconfig;
	   // $resultdata['showVideo'] = $results['showVideo'];
	    
	    echo json_encode($resultdata);
	    exit;
        //$results = json_decode($dconfig);
			
		}	
	}
	?>