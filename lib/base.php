<?php
abstract class Base {

	protected $config;


	public function __construct(array $config) {

		$this->config = $config;
	}

	protected function getOAuth2GoogleAPIInstance() {

		$OAuth2URLList = $this->config['OAuth2URL'];

		return new OAuth2\GoogleAPI(
			$OAuth2URLList['base'] . '/' . $OAuth2URLList['token'],
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
