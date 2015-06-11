<?php
abstract class Base {

	protected $config;


	public function __construct(array $config) {

		$this->config = $config;
	}

	protected function getOAuth2GoogleAPIInstance() {

		$OAuth2URLList = $this->config['OAuth2URL'];
		
		error_log(sprintf("Get Config: %s", print_r($this->config, 1) ));

		error_log(sprintf("Get URL LIST: %s", print_r($OAuth2URLList, 1) ));		
		
		return new OAuth2\GoogleAPI(
			$OAuth2URLList['base'] . '/' . 'ya29.VgG_INOMqTzVrWTk2lhfhrdd1rTRK9o6XXByfEo5JggA07ZQISc_zXblivtJRslMHj4cmOTayCVNjg',
			$OAuth2URLList['redirect'],
			$this->config['clientID'],
			$this->config['clientSecret']
		);
	}

	protected function saveOAuth2TokenData(array $data) {

		try {

    		error_log( "File Put Contents: " . file_put_contents(
    			plugin_dir_path(__FILE__) . $this->config['tokenDataFile'],
    			serialize($data)
    		));

		} catch (Exception $e) {
			// token save error
			error_log(sprintf("Error: %s",$e->getMessage()));
		}

	}

	protected function loadOAuth2TokenData() {

		// load file, return data as PHP array
		return unserialize(file_get_contents(plugin_dir_path(__FILE__) . '.tokenData'));
	}	
	
}
