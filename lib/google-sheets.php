<?php

// include_once ( plugin_dir_path(__FILE__) .  'base.php' );
// include_once ( plugin_dir_path(__FILE__) .  'oauth2/token.php' );
// include_once ( plugin_dir_path(__FILE__) .  'oauth2/googleapi.php' );
require_once plugin_dir_path(__FILE__).'php-google-oauth/Google_Client.php';
include_once ( plugin_dir_path(__FILE__) . 'autoload.php' );
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;


class googlesheet {
	private $token;
	private $spreadsheet;
	private $worksheet;
	const clientId = '1058344555307-fcus00minenokgq9vm48toli90q22783.apps.googleusercontent.com';
	const clientSecret = 'v7lv1NNg9ctr2HNwLvDAXtvo';
	const redirect = 'urn:ietf:wg:oauth:2.0:oob';
		
	public function __construct() {
	}
	
	public function ns_sheets_get_creds() {
		return array (
			'OAuth2URL' => array(
				'base' => 'https://accounts.google.com/o/oauth2',
				'auth' => 'auth', // for Google authorization
				'token' => 'token', // for OAuth2 token actions
				'redirect' => 'urn:ietf:wg:oauth:2.0:oob'
			),
		
			'clientID' => '1058344555307-fcus00minenokgq9vm48toli90q22783.apps.googleusercontent.com',
			'clientSecret' => 'v7lv1NNg9ctr2HNwLvDAXtvo',
			'tokenDataFile' => '.tokendata'
		);
	}

	//constructed on call
	public static function preauth($access_code){
		/*
		$access = new ExchangeCodeForTokens($this->ns_sheets_get_creds());
		$access->execute($access_code);
		// load OAuth2 token data - exit if false
		//if (($tokenData = $this->loadOAuth2TokenData()) === false) {
		//    ns_google_sheets_connector::ns_debug_log(sprintf("Token does not exist."));
		//	return;
		//}
		$tokenData = unserialize(file_get_contents(plugin_dir_path(__FILE__) . '.tokendata'));
		// setup Google OAuth2 handler
		$OAuth2GoogleAPI = $this->getOAuth2GoogleAPIInstance();
		$OAuth2GoogleAPI->setTokenData(
			$tokenData['accessToken'],
			$tokenData['tokenType'],
			$tokenData['expiresAt'],
			$tokenData['refreshToken']
		);
		$OAuth2GoogleAPI->setTokenRefreshHandler(function(array $tokenData) {
			// save updated OAuth2 token data back to file
			$this->saveOAuth2TokenData($tokenData);
		});  */
		
		$client = new Google_Client();
		$client->setClientId(googlesheet::clientId);
		$client->setClientSecret(googlesheet::clientSecret);
		$client->setRedirectUri(googlesheet::redirect);
		$client->setScopes(array('https://spreadsheets.google.com/feeds'));
		
		$results = $client->authenticate($access_code);
		
		$tokenData = json_decode($client->getAccessToken(), true);
		googlesheet::updateToken($tokenData);
	}
	
	public static function updateToken($tokenData){
		$tokenData['expire'] = time() + intval($tokenData['expires_in']);
		try{
			$tokenJson = json_encode($tokenData);
			update_option('ns_google_sheets_connector_token', $tokenJson);
		} catch (Exception $e) {
			ns_google_sheets_connector::ns_debug_log("Token write fail!");
		}
	}
	
	public function auth(){
		$tokenData = json_decode(file_get_contents(plugin_dir_path(__FILE__) . ".tokendata"), true);
		
		if(time() > $tokenData['expire']){
			$client = new Google_Client();
			$client->setClientId(googlesheet::clientId);
			$client->setClientSecret(googlesheet::clientSecret);
			$client->refreshToken($tokenData['refresh_token']);
			$tokenData = array_merge($tokenData, json_decode($client->getAccessToken(), true));
			googlesheet::updateToken($tokenData);
		}
		
		/* this is needed */
		$serviceRequest = new DefaultServiceRequest($tokenData['access_token']);
		ServiceRequestFactory::setInstance($serviceRequest);
	}

	//preg_match is a key of error handle in this case
	public function settitleSpreadsheet($title) {
		$this -> spreadsheet = $title;
	}

	//finished setting the title
	public function settitleWorksheet($title) {
		$this -> worksheet = $title;
	}

	//choosing the worksheet
	public function add_row($data) {
    	$spreadsheetService = new Google\Spreadsheet\SpreadsheetService();
		$spreadsheetFeed = $spreadsheetService->getSpreadsheets();
		$spreadsheet = $spreadsheetFeed->getByTitle($this->spreadsheet);
		$worksheetFeed = $spreadsheet->getWorksheets();
		$worksheet = $worksheetFeed->getByTitle($this->worksheet);
		$listFeed = $worksheet->getListFeed();

		//$row = array('date'=>'3/22/2015', 'your-name'=>'asdf', 'your-email'=>'asdf@asd.com', 'your-subject'=>'HI!', 'your-message'=>'there.');
		$listFeed->insert($data);
	}


}
?>