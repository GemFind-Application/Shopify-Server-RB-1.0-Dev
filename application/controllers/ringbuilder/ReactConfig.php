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
		public function getcssStyle()
	{	

		//echo 'play';

		$shop = $_GET['shop']; 
		
		$cssData = $this->general_model->getCssConfigurationData($shop);
        $resultdata = array();
	    $resultdata['data'] = $cssData;
	   // $resultdata['showVideo'] = $results['showVideo'];
	    
	    echo json_encode($cssData);
	    exit;
        //$results = json_decode($dconfig);
			
		}	
		public function GetDiamondDetail()
		{  
			
			
			$shop = $_GET['shop']; 
			$type = $_GET['IsLabGrown'];
			$diamondId = $_GET['DID']; 
			$diamondData = (array)$this->diamond_lib->getDiamondById($diamondId, $type, $shop);
			if (empty($diamondData['diamondData'])) {
				$emptyResponse = [
					'$id' => '1',
					'diamondId' => null,
					'mainHeader' => null,
					'subHeader' => null,
					'stockNumber' => null,
					'price' => null,
					'caratWeight' => null,
					'cut' => null,
					'color' => null,
					'colorID' => null,
					'clarity' => null,
					'clarityID' => null,
					'cutGrade' => null,
					'cutGradeID' => null,
					'depth' => null,
					'table' => null,
					'polish' => null,
					'symmetry' => null,
					'gridle' => null,
					'culet' => null,
					'fluorescence' => null,
					'measurement' => null,
					'originFancy' => null,
					'contactNo' => null,
					'contactEmail' => null,
					'image1' => null,
					'image2' => null,
					'colorDiamond' => null,
					'videoFileName' => null,
					'certificate' => null,
					'price1' => null,
					'price2' => null,
					'lotNumber' => null,
					'additionalImage' => null,
					'fltPrice' => null,
					'txtInhouse' => false,
					'shape' => null,
					'providerImageUrl' => null,
					'certificateNo' => null,
					'certificateUrl' => null,
					'certificateIconUrl' => null,
					'dealerId' => null,
					'stoneCarat' => null,
					'origin' => null,
					'skun' => null,
					'isFavorite' => false,
					'vendorsku' => false,
					'ratio' => null,
					'costPerCarat' => null,
					'vendorName' => null,
					'vendorID' => null,
					'vendorEmail' => null,
					'vendorContactNo' => null,
					'vendorFax' => null,
					'vendorAddress' => null,
					'vendorStockNo' => null,
					'sOrigin' => null,
					'wholeSalePrice' => null,
					'fancyColorMainBody' => null,
					'fancyColorIntensity' => null,
					'fancyColorOvertone' => null,
					'currencyFrom' => null,
					'currencySymbol' => null,
					'retailerStockNo' => null,
					'retailerInfo' => null,
					'internalUselink' => null,
					'girdleThin' => null,
					'girdleThick' => null,
					'country' => null,
					'diamondDeatilAdditionalInfo' => null,
					'showPrice' => false,
					'isLabCreated' => false,
					'dsEcommerce' => false,
					'defaultDiamondImage' => null
				];
	
				return $emptyResponse;
			} else {
	
				$diamondData = $diamondData['diamondData'];
	
				//if ($showRetailerInfo == 'false') {
	
					unset($diamondData['contactNo']);
					unset($diamondData['contactEmail']);
					unset($diamondData['costPerCarat']);
					unset($diamondData['vendorName']);
					unset($diamondData['vendorEmail']);
					unset($diamondData['vendorContactNo']);
					unset($diamondData['vendorAddress']);
					unset($diamondData['wholeSalePrice']);
	
					if (isset($diamondData['retailerInfo'])) {
						unset($diamondData['retailerInfo']->retailerCompany);
						unset($diamondData['retailerInfo']->retailerName);
						unset($diamondData['retailerInfo']->retailerCity);
						unset($diamondData['retailerInfo']->retailerState);
						unset($diamondData['retailerInfo']->retailerContactNo);
						unset($diamondData['retailerInfo']->retailerEmail);
						unset($diamondData['retailerInfo']->retailerLotNo);
						unset($diamondData['retailerInfo']->retailerStockNo);
						unset($diamondData['retailerInfo']->wholesalePrice);
						unset($diamondData['retailerInfo']->thirdParty);
						unset($diamondData['retailerInfo']->sellerName);
						unset($diamondData['retailerInfo']->sellerAddress);
						unset($diamondData['retailerInfo']->retailerAddress);
					}
				//}
	
				//return $diamondData;
			}
			echo json_encode($diamondData);

			exit;
			
		}
		public function GetMountingDetail()
		{  
			
			
			$shop = $_GET['shop']; 
			 if($_GET['IsLabGrown']=='false'){
				$type = 0;
			}else{
				$type = 1;
			}$type = 0;
			
			$ringId = $_GET['SID']; 
			$ringdata = (array)$this->ringbuilder_lib->getRingById($ringId, $type, $shop);
			if (empty($ringdata['ringData'])) {
				
				return [];
			} else {
	
				$ringdata = $ringdata['ringData'];
	
				//if ($showRetailerInfo == 'false') {
	
					//unset($ringdata['contactNo']);
					//unset($ringdata['contactEmail']);
					unset($ringdata['vendorCompany']);
					unset($ringdata['vendorId']);
					unset($ringdata['vendorEmail']);
					unset($ringdata['vendorPhone']);
					unset($ringdata['vendorName']);
					//unset($ringdata['wholeSalePrice']);
	
					if (isset($ringdata['retailerInfo'])) {
						unset($ringdata['retailerInfo']->retailerCompany);
						unset($ringdata['retailerInfo']->retailerName);
						unset($ringdata['retailerInfo']->retailerCity);
						unset($ringdata['retailerInfo']->retailerState);
						unset($ringdata['retailerInfo']->retailerContactNo);
						unset($ringdata['retailerInfo']->retailerEmail);
						unset($ringdata['retailerInfo']->retailerLotNo);
						unset($ringdata['retailerInfo']->retailerStockNo);
						unset($ringdata['retailerInfo']->wholesalePrice);
						unset($ringdata['retailerInfo']->thirdParty);
						unset($ringdata['retailerInfo']->sellerName);
						unset($ringdata['retailerInfo']->sellerAddress);
						unset($ringdata['retailerInfo']->retailerAddress);
						unset($ringdata['retailerInfo']->retailerID);
						unset($ringdata['retailerInfo']->retailerFax);
						
						
					}
				//}
	
				//return $diamondData;
			}
			echo json_encode($ringdata);

			exit;
			
		}
	}
	?>