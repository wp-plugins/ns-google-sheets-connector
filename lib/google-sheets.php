<?php
//make sure you include this file and call the constructor of class
class googlesheet {
	private $token;
	private $spreadsheet;
	private $worksheet;
	private $spreadsheetid;
	private $worksheetid;

	public function __construct() {
	}

	//constructed on call
	public function authenticate($username, $password) {
		$url = "https://www.google.com/accounts/ClientLogin";
		$fields = array("accountType" => "HOSTED_OR_GOOGLE", "Email" => $username, "Passwd" => $password, "service" => "wise", "source" => "pfbc");
		//this end constructor as in google documentation
		$curl = curl_init();
		//avoid blank lines or it will crash
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		$response = curl_exec($curl);
		//get response... same status code as on ajax xhtml
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($status == 200) {
			if (stripos($response, "auth=") !== false) {
				preg_match("/auth=([a-z0-9_\-]+)/i", $response, $matches);
				$this -> token = $matches[1];
			}
		}
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
		if (!empty($this -> token)) {
			$url = $this -> getPostUrl();
			if (!empty($url)) {
				$headers = array("Content-Type: application/atom+xml", "Authorization: GoogleLogin auth=" . $this -> token, "GData-Version: 3.0");
				//need to retreive the id of collums
				$columnIDs = $this -> getColumnIDs();
				if ($columnIDs) {
					$fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
					foreach ($data as $key => $value) {
						$key = $this -> formatColumnID($key);
						if (in_array($key, $columnIDs))
							$fields .= "<gsx:$key><![CDATA[$value]]></gsx:$key>";
					}
					$fields .= '</entry>';
					//end constructor
					$curl = curl_init();
					curl_setopt($curl, CURLOPT_URL, $url);
					curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($curl, CURLOPT_POST, true);
					curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
					$response = curl_exec($curl);
					$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					curl_close($curl);
				}
			}
		}
	}

	//helper functions ...as defined in gogle api
	private function getColumnIDs() {
		$url = "https://spreadsheets.google.com/feeds/cells/" . $this -> spreadsheetid . "/" . $this -> worksheetid . "/private/full?max-row=1";
		$headers = array("Authorization: GoogleLogin auth=" . $this -> token, "GData-Version: 3.0");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);

		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		//url are for 3.0... should change on depracated
		if ($status == 200) {
			$columnIDs = array();
			$xml = simplexml_load_string($response);
			if ($xml -> entry) {
				$columnSize = sizeof($xml -> entry);
				for ($c = 0; $c < $columnSize; ++$c)
					$columnIDs[] = $this -> formatColumnID($xml -> entry[$c] -> content);
			}
			return $columnIDs;
		}
		//no return functions exits sillent
		return "";
	}

	//need the url for post push method
	private function getPostUrl() {
		$url = "https://spreadsheets.google.com/feeds/spreadsheets/private/full?title=" . urlencode($this -> spreadsheet);
		$headers = array("Authorization: GoogleLogin auth=" . $this -> token, "GData-Version: 3.0");
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		//let's compare statuses
		if ($status == 200) {
			$spreadsheetXml = simplexml_load_string($response);
			if ($spreadsheetXml -> entry) {
				$this -> spreadsheetid = basename(trim($spreadsheetXml -> entry[0] -> id));
				$url = "https://spreadsheets.google.com/feeds/worksheets/" . $this -> spreadsheetid . "/private/full";
				if (!empty($this -> worksheet))
					$url .= "?title=" . $this -> worksheet;
				//setopt
				curl_setopt($curl, CURLOPT_URL, $url);
				$response = curl_exec($curl);
				$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				if ($status == 200) {
					$worksheetXml = simplexml_load_string($response);
					if ($worksheetXml -> entry)
						$this -> worksheetid = basename(trim($worksheetXml -> entry[0] -> id));
				}
			}
		}
		curl_close($curl);
		if (!empty($this -> spreadsheetid) && !empty($this -> worksheetid))
			return "https://spreadsheets.google.com/feeds/list/" . $this -> spreadsheetid . "/" . $this -> worksheetid . "/private/full";

		return "";
	}

	//no return
	//need to do a preformat on id of collumn
	private function formatColumnID($val) {
		return preg_replace("/[^a-zA-Z0-9.-]/", "", strtolower($val));
	}

}
?>