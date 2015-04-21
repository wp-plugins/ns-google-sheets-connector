<?php
require('base.php');
require('oauth2/api.php');


class BuildRequestURL extends Base {

	public function execute() {

		echo(
			"Copy and paste the URL below where appropriate:<br><br>" .

			$this->buildURL([
				GoogleSpreadsheet\API::API_BASE_URL
			])
		);
	}

	private function buildURL(array $scopeList) {

		$OAuth2URLList = $this->config['OAuth2URL'];

		// ensure all scopes have trailing forward slash
		foreach ($scopeList as &$scopeItem) $scopeItem = rtrim($scopeItem,'/') . '/';

		$buildQuerystring = function(array $list) {

			$querystringList = [];
			foreach ($list as $key => $value) {
				$querystringList[] = rawurlencode($key) . '=' . rawurlencode($value);
			}

			return implode('&',$querystringList);
		};

		return sprintf(
			"%s/%s?%s\n\n",
			$OAuth2URLList['base'],$OAuth2URLList['auth'],
			$buildQuerystring([
				'access_type' => 'offline',
				'approval_prompt' => 'force',
				'client_id' => $this->config['clientID'],
				'redirect_uri' => $OAuth2URLList['redirect'],
				'response_type' => 'code',
				'scope' => implode(' ',$scopeList)
			])
		);
	}
}


(new BuildRequestURL(require('config.php')))->execute();
