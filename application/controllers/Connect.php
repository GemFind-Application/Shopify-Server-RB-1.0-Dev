<?php
/*error_reporting(E_ALL);
ini_set('display_errors', '1');*/
defined('BASEPATH') OR exit('No direct script access allowed');
//echo header("Content-Security-Policy: frame-ancestors https://gemfind-product-demo-10.myshopify.com/ https://admin.shopify.com"); 

class Connect extends CI_Controller {

	function __construct()
	{  	
		parent::__construct();
		$this->load->model('general_model');
		$this->load->library('diamond_lib');
		$this->load->library('email');
		$config = array(
		    'protocol' =>PROTOCOL, 
		    'smtp_host' => SMTP_HOST, 
		    'smtp_port' => SMTP_PORT,
		    'smtp_user' => SMTP_USER,
		    'smtp_pass' => SMTP_PASSWORD,
		    'smtp_crypto' => 'tls', 
		    'mailtype' => 'html', 
		    'smtp_timeout' => '4', 
		    'charset' => 'utf-8',
		    'wordwrap' => TRUE,
		    'newline' => "\r\n"
		);
		$this->email->initialize($config);
		if($this->input->get('path_prefix')){
			header('Content-Type: application/liquid');
		}
	}

	function SubmitCustomerSmtpInfo(){
		$data = array(
			'protocol'  => $this->input->post('protocol'),
			'smtphost'  => $this->input->post('smtphost'),
			'smtpport'  => $this->input->post('smtpport'),
			'smtpusername'  => $this->input->post('smtpusername'),
			'smtppassword'    => $this->input->post('smtppassword'),
			'shop_name'    => $this->input->post('shopname')
		);
		$shop_id = $this->input->post('shopid');

		if($shop_id){
			$this->general_model->updateSmtpData($data,$shop_id);
		}
		else{
			$this->general_model->addSmtpData($data);
		}
		echo 'done';
		exit;
		
	}

	public function SubmitCssConfiguration()
	{
	    $colorData = $this->input->raw_input_stream; // Get raw POST data
	    $colorData = json_decode($colorData, true); // Decode JSON data

		$cssData = $this->general_model->getCssConfigurationData($colorData['shop']);

		// echo "<pre>"; print_r($cssData); exit();


		if(!empty($cssData)){

			$defaultValues = [
		        'color-picker-link-color' => $cssData->link,
		        'color-picker-hover-effect' => $cssData->hover,
		        'color-picker-column-header-accent' => $cssData->header,
		        'color-picker-call-to-action-button' => $cssData->button,
		        'color-picker-slider-effect' => $cssData->slider,
		        'color-picker-background-color' => $cssData->background,
		        'color-picker-background-text-color' => $cssData->backgroundText,

		        'shop' => $cssData->shop // Set your default shop value
		    ];

		}else{
			 // Default values in case some are not provided
		    $defaultValues = [
		        'color-picker-link-color' => '#000000',
		        'color-picker-hover-effect' => '#CCCCCC',
		        'color-picker-column-header-accent' => '#333333',
		        'color-picker-call-to-action-button' => '#FF5722',
		        'color-picker-slider-effect' => '#4CAF50',
		        'color-picker-background-color' => "#4CAF50",
		        'color-picker-background-text-color' => "#4CAF50",
		        'shop' => 'default_shop_value' // Set your default shop value
		    ];
		}
	   

	    // Merge default values with received data
	    $colorData = array_merge($defaultValues, $colorData);

	    $data = [
	        'link' => $colorData['color-picker-link-color'],
	        'header' => $colorData['color-picker-column-header-accent'],
	        'button' => $colorData['color-picker-call-to-action-button'],
	        'slider' => $colorData['color-picker-slider-effect'],
	        'hover' => $colorData['color-picker-hover-effect'],
	        'background' => $colorData['color-picker-background-color'],
	        'backgroundText' => $colorData['color-picker-background-text-color'],
	        'shop' => $colorData['shop'],
			'set_default_view'=>$colorData['set_default_view']
	    ];

	    if ($this->general_model->addCssConfiguration($data)) {
	        $response = ['status' => 'success', 'message' => 'Colors saved successfully'];
	    } else {
	        $response = ['status' => 'error', 'message' => 'Failed to save colors'];
	    }

	    echo json_encode($response);
	}

	public function index()
	{
		// echo "hi"; exit();
		
		//header("Content-Security-Policy: frame-ancestors https://".$_GET['shop']." https://admin.shopify.com");

		$params = $_GET; // Retrieve all request parameters
		$hmac = $_GET['hmac']; // Retrieve HMAC request parameter
		$shop = $params['shop'];
		$api_key = $this->config->item('api_key');
		$shared_secret = $this->config->item('shared_secret');


		$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
		ksort($params); // Sort params lexographically
		$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);

		 $query = array(
			    "client_id" => $api_key, // Your API key
			    "client_secret" => $shared_secret, // Your app credentials (secret key)
			    "code" => $params['code'] // Grab the access key from the URL
			  );
			  
			  $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";
			  $shop_access_token = getShopToken($access_token_url,$query);
		
		if($_SERVER['HTTP_SEC_FETCH_DEST'] == 'iframe'){
			// Use hmac data to check that the response is from Shopify or not

			if (hash_equals($hmac, $computed_hmac)) {
			  // Set variables for our request
			 

			  $data['access_key'] = $params['code'];
			  $data['access_token'] = $shop_access_token;
			  $data['shop_url'] = $shop;

			  $data['diamondconfigdata'] = $this->general_model->getDiamondConfig($shop);
			  $data['recurring_charges_data'] = $this->general_model->generalGetData($shop,'shop','app_charges');

		
		         if($this->input->post('SubmitDiamondSetting') == "Submit")
		         {
	
			   	    $this->SubmitDiamondSetting($shop,$params);
		          }
			  //$data["customer"] = $this->general_model->getCustomerData($shop);	
			  $dappcharges = $this->general_model->getAppChargesData($shop);
			  //echo "dappcharges:".$dappcharges."<br/>";
			  $dconfig = $this->general_model->getDiamondConfigData($shop);	
			  //echo "dconfig:".$dconfig."<br/>";
			  $customerData = $this->general_model->getCustomerData($shop);	
			  //echo "customerData:".$customerData."<br/>";
			  if($dappcharges != "")
			  {
				  if($dconfig == "" && $customerData == "")
				  {
					  $data["customer"] = "";
				  }
				  else
				  {
					  $data["customer"] = "existence";
				  }
			  }
			  else if($dconfig != ""){
				  //echo "<br/>Else else if:";
				  if($customerData != "")
				  {
					  $data["customer"] = "existence";
				  }
				  else
				  {
					  $data["customer"] = "";
				  }
			  }		
			  else
			  {
				  //echo "<br/>Else Part:";
				  if($customerData != "")
				  {
					  $data["customer"] = "existence";
				  }
				  else
				  {
					  $data["customer"] = "";
				  }
			  }
			  $smtpData = $this->general_model->getSmtpData($shop);
			  $cssConfigurationData = $this->general_model->getCssConfigurationData($shop);

				
				if($smtpData){
					$data['shopid']=$smtpData->shopid;
					$data['protocol']=$smtpData->protocol;
					$data['smtphost']=$smtpData->smtphost;
					$data['smtpport']=$smtpData->smtpport;
					$data['smtpusername']=$smtpData->smtpusername;
					$data['smtppassword']=$smtpData->smtppassword;
				}else{
					$data['shopid']='';
					$data['protocol']='';
					$data['smtphost']='';
					$data['smtpport']='';
					$data['smtpusername']='';
					$data['smtppassword']='';
				}


				// if($cssConfigurationData){					
				// 	$data['link']=$cssConfigurationData->link;
				// 	$data['header']=$cssConfigurationData->header;
				// 	$data['button']=$cssConfigurationData->button;
				// 	$data['slider']=$cssConfigurationData->slider;
				// 	$data['hover']=$cssConfigurationData->smtppassword;
				// }else{			
				// 	$data['link']='';
				// 	$data['header']='';
				// 	$data['button']='';
				// 	$data['slider']='';
				// 	$data['hover']='';
				// }

				$data['cssConfigurationData'] = $cssConfigurationData;

			  $this->load->view('admin_form', $data);
			  $this->registerShopifyAppUninstallWebhookRing($shop,$shop_access_token);
			 

			  

			} else {
			  // Someone is trying to be shady!
			  die('This request is NOT from Shopify!');
			}
		}else{
			$this->checkAndInstallHubspotRingbuilder($shop,$shop_access_token);
			$this->installApplication($shop,$shop_access_token);
			$shopDomain = str_replace('.myshopify.com', '', $shop);
	    	$redirectURL = 'https://admin.shopify.com/store/'.$shopDomain.'/apps/gemfind-ringbuilder';
			redirect($redirectURL, 'refresh');
			exit;
		}

	}

	public function registerShopifyAppUninstallWebhookRing($shop_domain,$shop_access_token){

		$API_KEY =$this->config->item('api_key');
		$SECRET = $this->config->item('shared_secret');
		$STORE_URL = $shop_domain;

		$params = $_GET;
	    $TOKEN = $shop_access_token; 
	    $url = 'https://'. $API_KEY . ':' . md5($SECRET . $TOKEN) . '@' . $STORE_URL . '/admin/webhooks.json';
	    $paramshook = array("webhook" => array( "topic"=>"app/uninstalled",
		   "address"=> base_url()."webhook/setShopifyUninstall",
		   "format"=> "json"));
	   $session = curl_init();
	   curl_setopt($session, CURLOPT_URL, $url);
	   curl_setopt($session, CURLOPT_POST, 1);
	   curl_setopt($session, CURLOPT_POSTFIELDS, stripslashes(json_encode($paramshook)));
	   curl_setopt($session, CURLOPT_HEADER, false);
	   curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/json', 'X-Shopify-Access-Token: '.$TOKEN));
	   curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	   $result = curl_exec($session);
	   curl_close($session);
		 // Store the access token
	   $result = json_decode($result, true);
	   
   }

   public function installApplication($shop,$shop_access_token){

	file_put_contents('watch.txt', 'Install email fired' , FILE_APPEND );
	    $request_headers = array(
						"X-Shopify-Access-Token:" . $shop_access_token,
						"Content-Type:application/json"
					);


		$shop_detail_api_url = "https://" . $shop . "/admin/shop.json";
		$resultShop = getCurlData($shop_detail_api_url,$request_headers);

		$data = $resultShop->shop;
		//$data=json_decode($result, true);

		// echo '<pre>'; print_r($resultShop->shop); exit();
		$finalData = array(
			'{{id}}' => $data->id, 
			'{{name}}' => $data->name,
			'{{domain}}' => $data->domain,
			'{{email}}' => $data->email,
			'{{created_at}}' => $data->created_at,
			'{{updated_at}}' => $data->updated_at

		);


		$admin_template = $this->load->view('emails/admin_mail_install_templete.html','',true);
		$admin_email_body = str_replace(array_keys($finalData), array_values($finalData), $admin_template);	
		$admin_subject = $data->name . " : GemFind RingBuilderⓇ Dev App Install";
		$admin_toEmail = ['dev@gemfind.com'];

		if(!empty($data->domain)){
			$this->email->from('noreply@gemfind.com', 'GemFind RingBuilderⓇ Dev');
			$this->email->to('dev@gemfind.com');
			$this->email->subject($admin_subject);
			$this->email->message($admin_email_body);
			$this->email->set_mailtype('html');
			$this->email->send();
		}

	}


	public function checkAndInstallHubspotRingbuilder($shop, $shop_access_token) {
	    $app_path = APPPATH . '/logs/';

	    // Get the shop domain name from the $shop variable
	    $shopDomain = $shop;

	    // Check if the shop domain exists in the database
	    $shopData = $this->general_model->getAppStatus($shopDomain);

	    if (!$shopData) {
	        // Shop is not present, call your function here for fresh install
	        $this->installnewhubspotRingbuilder($shop, $shop_access_token);
	        file_put_contents($app_path . 'freshInstall.txt', 'statusIsactive');

	    } elseif ($shopData === 'Inactive') {
	        // Call your function here for re-installation (status is "inactive")
	        $this->installnewhubspotRingbuilder($shop, $shop_access_token);
	        file_put_contents($app_path . 'statusIsINactive.txt', 'statusIsactive');
	    } else {
	        // Shop is present and not inactive, no need to call the function
	        file_put_contents($app_path . 'statusIsactive.txt', 'statusIsactive');
	    }
	}

 public function installnewhubspotRingbuilder($shop,$shop_access_token){

    $request_headers = array(
					"X-Shopify-Access-Token:" . $shop_access_token,
					"Content-Type:application/json"
				);


	$shop_detail_api_url = "https://" . $shop . "/admin/shop.json";
	$resultShop = getCurlData($shop_detail_api_url,$request_headers);

	$file = "result_log11.txt";
     	file_put_contents($file, $resultShop );

  	$now = new DateTime();
	$now->setTimezone(new DateTimezone('Asia/Kolkata'));
	$current_date = $now->format('Y-m-d H:i:s');
	$domain = $resultShop->shop->domain;

	$arr = array(
	    'filters' => array(
	     array(
	        'propertyName' => 'email',
	        'operator' => 'EQ',
	        'value' => $resultShop->shop->email
	      )
	    )
	);
	$post_json = json_encode($arr);

	$file = "newarray_log1.txt";
    file_put_contents($file, $post_json);

    $email_id=$resultShop->shop->email;
    $endpoint ='https://api.hubapi.com/contacts/v1/contact/email/'.$email_id.'/profile';
    $ch = curl_init();
    $headers = [
	    'Content-Type: application/json',	
	    'Authorization: Bearer ' . YOUR_ACCESS_TOKEN,
	];
    //curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errors = curl_error($ch);
    curl_close($ch);

    $file = $domain."install_status_log".time().".txt";
    file_put_contents($file, $status_code);

    $file = $domain."install_response_log".time().".txt";
    file_put_contents($file, $response);

    if ($status_code == 200) {
		$arr1 = array(
            'properties' => array(
                array(
                    'property' => 'email',
                    'value' => $resultShop->shop->email
                ),
                array(
                    'property' => 'Install_Date',
                    'value' => $current_date
            	),
               array(
                    'property' => 'app_status',
                    'value' => 'RE-INSTALL-RINGBUILDER-DEV'
                )
            )
        );
		$post_json1 = json_encode($arr1);
        $email_id1=$resultShop->shop->email;
        $endpoint1 ='https://api.hubapi.com/contacts/v1/contact/email/'.$email_id1.'/profile';


       $ch1 = curl_init();
        $headers = [
		    'Content-Type: application/json',	
		    'Authorization: Bearer ' . YOUR_ACCESS_TOKEN,
		];
        curl_setopt($ch1, CURLOPT_POST, true);
        curl_setopt($ch1, CURLOPT_POSTFIELDS, $post_json1);
        curl_setopt($ch1, CURLOPT_URL, $endpoint1);
        curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);

		// Execute the request and get the response
		$response = curl_exec($ch);

        $response1 = curl_exec($ch1);
        $status_code1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
        $curl_errors1 = curl_error($ch1);
        curl_close($ch1);

        $file = $domain."reinstall_status_log".time().".txt";
        file_put_contents($file, $status_code1);

        $file = $domain."reinstall_response_log".time().".txt";
        file_put_contents($file, $response1);
    }  else{
		$arr = array(
        'properties' => array(
            array(
                'property' => 'email',
                'value' => $resultShop->shop->email
            ),
            array(
                'property' => 'shop_name',
                'value' => $resultShop->shop->name
            ),
            array(
                'property' => 'domain_name',
                'value' => $resultShop->shop->domain
            ),
            array(
                'property' => 'phone',
                'value' => $resultShop->shop->phone
            ),
            array(
                'property' => 'state',
                'value' => $resultShop->shop->province
            ),
            array(
                'property' => 'country',
                'value' => $resultShop->shop->country
            ),
            array(
                'property' => 'address',
                'value' => $resultShop->shop->address1
            ),
            array(
                'property' => 'city',
                'value' => $resultShop->shop->city
            ),
            array(
                    'property' => 'Install_Date',
                    'value' => $current_date
            	),
            array(
                'property' => 'app_status',
                'value' => 'INSTALL-RINGBUILDER-DEV'
            )
        )
    );
    $post_json = json_encode($arr);
    // $file = "post_data_log.txt";
    // file_put_contents($file, $post_json);
    $endpoint = 'https://api.hubapi.com/contacts/v1/contact';

    $ch = curl_init();
    $headers = [
	    'Content-Type: application/json',	
	    'Authorization: Bearer ' . YOUR_ACCESS_TOKEN,
	];
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_errors = curl_error($ch);
    curl_close($ch);
    $file = $domain."fresh_install_response_log".time().".txt";
    file_put_contents($file, $response);
    $file = $domain."fresh_install_status_log".time().".txt";
    file_put_contents($file, $status_code);
    return;
	}

	}

	public function SubmitDiamondSetting($shop,$params)
	{
		
		$api_key = $this->config->item('api_key');
		$shared_secret = $this->config->item('shared_secret');

		// echo '<pre>'; print_r($params); exit();

		 $query = array(
			    "client_id" => $api_key, // Your API key
			    "client_secret" => $shared_secret, // Your app credentials (secret key)
			    "code" => $params['code'] // Grab the access key from the URL
			  );


		      		// Generate access token URL
				  			      		// Generate access token URL
				  	$access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";
				  	// Configure curl client and execute request
				  	
				  	// print_r($query);
				  	$ch = curl_init();
				  	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				  	curl_setopt($ch, CURLOPT_URL, $access_token_url);
				  	curl_setopt($ch, CURLOPT_POST, count($query));
				  	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
				  	$result = curl_exec($ch);
				  	curl_close($ch);
				  	// Store the access token
				  	$result = json_decode($result, true);
				  	if($result['access_token']){
			  			$access_token = $result['access_token']; 
			  		}elseif($shop_access_token){
			  			$access_token = $shop_access_token; 
			  		}else{
			  			$access_token = $this->input->post('sp_access_token');
			  		} 

			  		$request_headers = array(
	                    "X-Shopify-Access-Token:" . $access_token,
	                    "Content-Type:application/json"
	                );

			  		$shop_detail_api_url = "https://" . $params['shop'] . "/admin/shop.json";

			  		$resultShop = getCurlData($shop_detail_api_url,$request_headers);

			  		if($resultShop){
				  		$store_id = $resultShop->shop->id;
				  		$store_location_id = $resultShop->shop->primary_location_id;
			  		}

			  		  $redirect_base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
					  $redirect_base_url .= "://". @$_SERVER['HTTP_HOST'];
					  $redirect_base_url .= @$_SERVER['REQUEST_URI'];
			  		

		            $diamondAdminOptionData = array();
	                $diamondAdminOptionData = array(
	                  'dealerid'  => $this->input->post('dealerid'),
	                  'from_email_address'  => $this->input->post('from_email_address'),
	                  'admin_email_address'  => $this->input->post('admin_email_address'),
	                  'dealerauthapi'    => $this->input->post('dealerauthapi'),
	                  'ringfiltersapi'    => $this->input->post('ringfiltersapi'),
	                  'mountinglistapi'    => $this->input->post('mountinglistapi'),
	                  'mountinglistapifancy'    => $this->input->post('mountinglistapifancy'),
	                  'ringstylesettingapi'    => $this->input->post('ringstylesettingapi'),
	                  'navigationapi'    => $this->input->post('navigationapi'),
	                  'navigationapirb'    => $this->input->post('navigationapirb'),
	                  'filterapi'    => $this->input->post('filterapi'),
	                  'filterapifancy'    => $this->input->post('filterapifancy'),
	                  'diamondlistapi'    => $this->input->post('diamondlistapi'),
	                  'diamondlistapifancy'    => $this->input->post('diamondlistapifancy'),
	                  'diamondshapeapi'    => $this->input->post('diamondshapeapi'),
	                  'diamonddetailapi'    => $this->input->post('diamonddetailapi'),
	                  'stylesettingapi'    => $this->input->post('stylesettingapi'),
	                  'enable_hint'    => $this->input->post('enable_hint'),
	                  'enable_email_friend'    => $this->input->post('enable_email_friend'),
	                  'enable_schedule_viewing'    => $this->input->post('enable_schedule_viewing'),
	                  'enable_more_info'    => $this->input->post('enable_more_info'),
	                  'enable_print'    => $this->input->post('enable_print'),
	                  'enable_admin_notification'    => $this->input->post('enable_admin_notification'),
	                  'default_viewmode'    => $this->input->post('default_viewmode'),
	                  'show_filter_info'    => $this->input->post('show_filter_info'),
	                  'show_powered_by'    => $this->input->post('show_powered_by'),
	                  'enable_sticky_header'    => $this->input->post('enable_sticky_header'),
	                  'settings_carat_ranges'    => $this->input->post('settings_carat_ranges'),
					  'display_tryon'    => $this->input->post('display_tryon') ?? 0,
	                  'shop_logo' => $this->input->post('shop_logo'),
	                  'announcement_text' => $this->input->post('announcement_text'),
	                  'announcement_text_rbdetail' => $this->input->post('announcement_text_rbdetail'),
	                  'ring_meta_title' => $this->input->post('ring_meta_title'),
					  'ring_meta_description' => $this->input->post('ring_meta_description'),
					  'ring_meta_keywords' => $this->input->post('ring_meta_keywords'),
					  'diamond_meta_title' => $this->input->post('diamond_meta_title'),
					  'diamond_meta_description' => $this->input->post('diamond_meta_description'),
					  'diamond_meta_keyword' => $this->input->post('diamond_meta_keyword'),
					  'time_interval' => $this->input->post('time_interval'),
					  'site_key' => $this->input->post('site_key'),
					  'secret_key' => $this->input->post('secret_key'),
					  'price_row_format' => $this->input->post('price_row_format'),
					  'products_pp' => $this->input->post('products_pp'),
					  'sorting_order' => $this->input->post('sorting_order'),
					  'tool_version' => $this->input->post('tool_version'),
					  'font_family' => $this->input->post('font_family'),
                      'shop_title'=>$this->input->post('shop_title'),
					  'theme_font_family'=>$this->input->post('theme_font_family')

	                  );

	                // echo "<pre>"; print_r($diamondAdminOptionData); exit();

	                $data['diamondconfigdata'] = $this->general_model->getDiamondConfig($shop);

		            if($data['diamondconfigdata']){
		            	
		            	$diamondAdminOptionData['updated_date'] = date('Y-m-d h:i:s');
		            	if($access_token){
			            	$diamondAdminOptionData['shop_access_token'] = $access_token;
			            	$diamondAdminOptionData['store_id'] = $store_id;
			            	$diamondAdminOptionData['store_location_id'] = $store_location_id;
		            	}
			            $this->general_model->updateData($diamondAdminOptionData,$shop);
			        }else{
			        	
			        	$diamondAdminOptionData['shop'] = $shop;
			        	if($access_token){
				        	$diamondAdminOptionData['shop_access_token'] = $access_token;
				        	$diamondAdminOptionData['store_id'] = $store_id;
			            	$diamondAdminOptionData['store_location_id'] = $store_location_id;
		            	}
			        	$this->general_model->addData($diamondAdminOptionData);
			        }



		            $this->session->set_flashdata('SystemOptionMSG', "Data Saved");

		            redirect($redirect_base_url, 'refresh'); 
	}

	public function SubmitCustomerInfo()
	{

		$customerEmail = $this->input->post('email');
		$customerShop = $this->input->post('shop');

		$customerData = array(
		  'business'  => $this->input->post('business'),
		  'name'  => $this->input->post('name'),
		  'address'  => $this->input->post('address'),
		  'country'  => $this->input->post('country'),
		  'state'  => $this->input->post('state'),
		  'city'  => $this->input->post('city'),
		  'zip_code'  => $this->input->post('zip_code'),
		  'telephone'  => $this->input->post('telephone'),
		  'website'    => $this->input->post('website_url'),
		  'email'    => $this->input->post('email'),
		  'shop'    => $this->input->post('shop'),
		  'ji-certified'    => $this->input->post('ji-certified'),
		  'notes'    => $this->input->post('notes')
		);

		$this->general_model->addCustomerData($customerData);
		file_put_contents("customer_register.txt", $customerData);

		$diamondAdminOptionData = array(
			'dealerid'  => '1089',
			'admin_email_address'  => 'dev@gemfind.com',
			'shop'    => $this->input->post('shop'),
			'dealerauthapi'    => 'http://api.jewelcloud.com/api/RingBuilder/AccountAuthentication',
			'ringfiltersapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetFilters?',
			'mountinglistapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetMountingList?',
			'mountinglistapifancy'    => 'http://api.jewelcloud.com/api/RingBuilder/GetMountingDetail?',
			'ringstylesettingapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetStyleSetting?',
			'navigationapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetNavigation?',
			'navigationapirb'    => 'http://api.jewelcloud.com/api/RingBuilder/GetRBNavigation?',
			'filterapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetDiamondFilter?',
			'filterapifancy'    => 'http://api.jewelcloud.com/api/RingBuilder/GetColorDiamondFilter?',
			'diamondlistapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetDiamond?',
			'diamondlistapifancy'    => 'http://api.jewelcloud.com/api/RingBuilder/GetColorDiamond?',
			'diamondshapeapi'    => 'http://api.jewelcloud.com/api/ringbuilder/GetShapeByColorFilter?',
			'diamonddetailapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetDiamondDetail?',
			'stylesettingapi'    => 'http://api.jewelcloud.com/api/RingBuilder/GetStyleSetting?',
			'shop_access_token' => $this->input->post('sp_access_token')
		);		
	
		$this->general_model->addData($diamondAdminOptionData);
					

		//file_put_contents('default_data.txt',json_encode($diamondAdminOptionData));

		$options = $this->input->post('ji-certified');
		$ji_certified = $options != "" ? 'Yes' : 'No';

		
		// Send Email
		if(isset($customerEmail) && isset($customerShop)){ 					
			$templateValueReplacement = array(
				'{{shopurl}}' => $this->input->post('shop'), 
				'{{business}}' => $this->input->post('business'),
				'{{name}}' => $this->input->post('name'),
				'{{address}}' => $this->input->post('address'),
				'{{country}}' => $this->input->post('country'),
				'{{state}}' => $this->input->post('state'),
				'{{city}}' => $this->input->post('city'),
				'{{zip_code}}' => $this->input->post('zip_code'),
				'{{telephone}}' => $this->input->post('telephone'),
				'{{website}}' => $this->input->post('website_url'),
				'{{ji-certified}}' => $ji_certified,
				'{{email}}' => $this->input->post('email'),
				'{{notes}}' => $this->input->post('notes')
			);
			// Send Email to customer
			$customer_template = $this->load->view('emails/customer_customer_info_template.html','',true);                
			$customer_email_body = str_replace(array_keys($templateValueReplacement), array_values($templateValueReplacement), $customer_template);	
			$customer_subject = "Customer Information Form";
			$customer_toEmail = $this->input->post('email');
			$this->email->from('smtp@gemfind.com', 'GemFind RingBuilder');
			$this->email->to($customer_toEmail);
			$this->email->subject($customer_subject);
			$this->email->message($customer_email_body);
			$this->email->send();
								
			// Send Email to Admin
			$admin_template = $this->load->view('emails/admin_customer_info_template.html','',true);
			$admin_email_body = str_replace(array_keys($templateValueReplacement), array_values($templateValueReplacement), $admin_template);	
			$admin_subject = $this->input->post('business').": New Shopify RingBuilder";
			
			//$admin_toEmail = "rahul.evdpl@gmail.com";
			$admin_toEmail = "appinstall@gemfind.com";
			$this->email->from('smtp@gemfind.com', 'GemFind RingBuilder');
			$this->email->to($admin_toEmail);
			$this->email->subject($admin_subject);
			$this->email->message($admin_email_body);
			$this->email->send();
			
			$this->session->set_flashdata('SystemOptionMSG', "Data has been saved successfully!!");
			echo json_encode($this->session->flashdata('SystemOptionMSG'));
		}
		//redirect($redirect_base_url, 'refresh'); 
		$this->installhubspotRingbuilder($templateValueReplacement);
	}


	public function installhubspotRingbuilder($templateValueReplacement){


	    $shop_access_token = $this->input->post('sp_access_token'); 
	    $domain = $this->input->post('website_url');


	     $request_headers = array(
					"X-Shopify-Access-Token:" . $shop_access_token,
					"Content-Type:application/json"
				);


		$shop_detail_api_url = "https://" . $domain . "/admin/shop.json";
		$resultShop = getCurlData($shop_detail_api_url,$request_headers);

		
		$now = new DateTime();
		$now->setTimezone(new DateTimezone('Asia/Kolkata'));
		$current_date = $now->format('Y-m-d H:i:s');

		

		$arr = array(
		    'filters' => array(
		     array(
		        'propertyName' => 'email',
		        'operator' => 'EQ',
		        'value' => $resultShop->shop->email
		      )
		    )
		);
		$post_json = json_encode($arr);

		$file = "array_log.txt";
        file_put_contents($file, $post_json);



        $email_id=$resultShop->shop->email;
        $endpoint ='https://api.hubapi.com/contacts/v1/contact/email/'.$email_id.'/profile';
        $ch = curl_init();
        $headers = [
		    'Content-Type: application/json',	
		    'Authorization: Bearer ' . YOUR_ACCESS_TOKEN,
		];
        //curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errors = curl_error($ch);
        curl_close($ch);

        $file = $domain."customer_status_log".time().".txt";
        file_put_contents($file, $status_code);

        $file = $domain."customer_response_log".time().".txt";
        file_put_contents($file, $response);

        if ($status_code == 200) {
			$arr1 = array(
	            'properties' => array(
	                array(
	                    'property' => 'email',
	                    'value' => $this->input->post('email')
	                ),
	                array(
	                    'property' => 'Install_Date',
	                    'value' => $current_date
                	),
                	array(
                    'property' => 'ShopifyRB_Service_Fee',
                    'value' => $this->input->post('price')
                	),
	               array(
	                    'property' => 'app_status',
	                    'value' => 'REGISTER-RINGBUILDER-DEV'
	                )
	            )
	        );
			$post_json1 = json_encode($arr1);
			file_put_contents('post_data_dia.txt',$post_json1);
	        $email_id1=$resultShop->shop->email;
	        $endpoint1 ='https://api.hubapi.com/contacts/v1/contact/email/'.$email_id1.'/profile';
	        
	        $ch1 = curl_init();
	        $headers = [
			    'Content-Type: application/json',	
			    'Authorization: Bearer ' . YOUR_ACCESS_TOKEN,
			];
	        curl_setopt($ch1, CURLOPT_POST, true);
	        curl_setopt($ch1, CURLOPT_POSTFIELDS, $post_json1);
	        curl_setopt($ch1, CURLOPT_URL, $endpoint1);
	        curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
	        $response1 = curl_exec($ch1);
	        $status_code1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
	        $curl_errors1 = curl_error($ch1);
	        curl_close($ch1);

	        $file = $domain."customer_re_register_status_log".time().".txt";
	        file_put_contents($file, $status_code1);

	        $file = $domain."customer_re_register_response_log".time().".txt";
	        file_put_contents($file, $response1);
        }  else{

        	 //echo "<pre>"; print_r($response); exit();

        	$arr2 = array(
            'properties' => array(
                array(
                    'property' => 'email',
                    'value' => $this->input->post('email')
                ),
                array(
                    'property' => 'shop_name',
                    'value' => $this->input->post('shop')
                ),
                array(
                    'property' => 'domain_name',
                    'value' => $this->input->post('website_url')
                ),
                array(
                    'property' => 'phone',
                    'value' => $this->input->post('telephone')
                ),
                array(
                    'property' => 'state',
                    'value' => $this->input->post('state')
                ),
                array(
                    'property' => 'address',
                    'value' => $this->input->post('address')
                ),
                array(
                    'property' => 'city',
                    'value' => $this->input->post('city')
                ),
                array(
                    'property' => 'ShopifyRB_Service_Fee',
                    'value' => $this->input->post('price')
                ),
                array(
	                    'property' => 'Install_Date',
	                    'value' => $current_date
                ),
                array(
                    'property' => 'app_status',
                    'value' => 'REGISTER-RINGBUILDER-DEV'
                )
            )
        );
        $post_json2 = json_encode($arr2);
        $file = "post_data_log3.txt";
        file_put_contents($file, $post_json2);
        $endpoint2 = 'https://api.hubapi.com/contacts/v1/contact';
        $ch2 = curl_init();
        $headers = [
		    'Content-Type: application/json',	
		    'Authorization: Bearer ' . YOUR_ACCESS_TOKEN,
		];
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $post_json2);
        curl_setopt($ch2, CURLOPT_URL, $endpoint2);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch2);
        $status_code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
        $curl_errors2 = curl_error($ch2);
        curl_close($ch2);
       // echo "<pre>"; print_r($response2); exit();
        $file = $domain."customer_register_response_log".time().".txt";
        file_put_contents($file, $response2);
        $file = $domain."customer_register_status_log".time().".txt";
        file_put_contents($file, $status_code2);
        }
	}	
}