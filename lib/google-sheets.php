<?php

include_once ( plugin_dir_path(__FILE__) .  'base.php' );
include_once ( plugin_dir_path(__FILE__) .  'oauth2/token.php' );
include_once ( plugin_dir_path(__FILE__) .  'oauth2/googleapi.php' );
include_once ( plugin_dir_path(__FILE__) . 'autoload.php' );
use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;


class googlesheet extends Base {
	private $token;
	private $spreadsheet;
	private $worksheet;
		
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
	public function authenticate($access_code) {
		$access = new ExchangeCodeForTokens($this->ns_sheets_get_creds());
		$access->execute($access_code);
		// load OAuth2 token data - exit if false
		//if (($tokenData = $this->loadOAuth2TokenData()) === false) {
		//    error_log(sprintf("Token does not exist."));
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
		});

		$serviceRequest = new DefaultServiceRequest($tokenData['accessToken']);
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




class ExchangeCodeForTokens extends Base {

	public function execute($authCode) {

		$OAuth2GoogleAPI = $this->getOAuth2GoogleAPIInstance();

		/* make request for OAuth2 tokens
		echo(sprintf(
			"Requesting OAuth2 tokens via authorization code: %s\n",
			$authCode
		));
        */
        
		try {
		    error_log(sprintf("Check for token: %s",$authCode));
			$tokenData = $OAuth2GoogleAPI->getAccessTokenFromAuthCode($authCode);
		    error_log(print_r($tokenData,1));
			// save token data to disk
			error_log(sprintf(
				"Success! Saving token data to [%s]",
				$this->config['tokenDataFile']
			));
			

			$this->saveOAuth2TokenData($tokenData);

		} catch (Exception $e) {
			// token fetch error
			error_log(sprintf("Error: %s",$e->getMessage()));
		}
	}
		

}


?>