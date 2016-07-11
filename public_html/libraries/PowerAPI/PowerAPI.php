<?php

class PowerAPI
{
	private $url;
	private $version;
	private $ua = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36";

	public function __construct($url, $version)
	{
		if(substr($url, -1) !== "/")
			$this->url = $url . "/";
		else
			$this->url = $url;
		$this->version = $version;
	}

	public function setUserAgent($ua)
	{
		$this->ua = $ua;
	}

	/* Authentication */

	private function getAuthTokens()
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$html = curl_exec($ch);

		curl_close($ch);

		if(!$html)
		{
			throw new Exception('Unable to retrieve authentication tokens from PS server.');
		}

		preg_match('/<input type="hidden" name="pstoken" value="(.*?)" \/>/s', $html, $pstoken);
		$data['pstoken'] = $pstoken[1];

		preg_match('/<input type="hidden" name="contextData" value="(.*?)" \/>/s', $html, $contextData);
		$data['contextData'] = $contextData[1];

		return $data;
	}

	public function auth($uid, $pw)
	{
		$tokens = $this->getAuthTokens();

		switch($this->version)
		{
		case 7:
			$fields = array('pstoken' => urlencode($tokens['pstoken']), 'contextData' => urlencode($tokens['contextData']), 'dbpw' => urlencode(hash_hmac("md5", strtolower($pw), $tokens['contextData'])), 'translator_username' => urlencode(""), 'translator_password' => urlencode(""),
					'translator_ldappassword' => urlencode(""), 'returnUrl' => urlencode(""), 'serviceName' => urlencode("PS Parent Portal"), 'serviceTicket' => "", 'pcasServerUrl' => urlencode("/"), 'credentialType' => urlencode("User Id and Password Credential"),
					'request_locale' => urlencode("en_US"), 'account' => urlencode($uid), 'ldappassword' => urlencode($pw), 'pw' => urlencode(hash_hmac("md5", str_replace("=", "", base64_encode(md5($pw, true))), $tokens['contextData'])), 'translatorpw' => urlencode(""));
			break;
		case 6:
			$fields = array('pstoken' => urlencode($tokens['pstoken']), 'contextData' => urlencode($tokens['contextData']), 'returnUrl' => urlencode($this->url . "guardian/home.html"), 'serviceName' => urlencode("PS Parent Portal"), 'serviceTicket' => "", 'pcasServerUrl' => urlencode("/"),
					'credentialType' => urlencode("User Id and Password Credential"), 'account' => urlencode($uid), 'pw' => urlencode(hash_hmac("md5", strtolower($pw), $tokens['contextData'])));
			break;
		default:
			throw new Exception('Invalid PowerSchool version.');
		}

		$fields_string = "";
		foreach($fields as $key => $value)
		{
			$fields_string .= $key . '=' . $value . '&';
		}
		rtrim($fields_string, '&');

		$ch = curl_init();

		$tmp_fname = tempnam("/tmp/", "PSCOOKIE");

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $this->url . "guardian/home.html");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $tmp_fname);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $tmp_fname);
		curl_setopt($ch, CURLOPT_REFERER, $this->url . "/public/");
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

		$result = curl_exec($ch);
		curl_close($ch);

		if(!strpos($result, "Grades and Attendance"))
		{ // This should show up instantly after login
			throw new Exception('Unable to login to PS server.'); // So if it doesn't, something went wrong. (normally bad username/password)
		}

		return new PowerAPIUser($this->url, $this->version, $this->ua, $tmp_fname, $result);
	}
}

class PowerAPIUser
{
	public $url, $version, $cookiePath, $ua, $homeContents;


	public function __construct($url, $version, $ua, $cookiePath, $homeContents)
	{
		$this->url = $url;
		$this->version = $version;
		$this->ua = $ua;
		$this->cookiePath = $cookiePath;
		$this->homeContents = $homeContents;
	}

    public function fetchPage($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $this->url . $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiePath);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiePath);
        curl_setopt($ch, CURLOPT_REFERER, $this->url . "/public/");
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }

	public function fetchTranscript()
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, $this->url . "guardian/studentdata.xml?ac=download");
		curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiePath);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiePath);
		curl_setopt($ch, CURLOPT_REFERER, $this->url . "/public/");
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); //Give Powerschool 30 seconds to start transmitting data.
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); //XML file should download in 20 seconds or less

		$result = curl_exec($ch);

        if(curl_errno($ch)) //On error, return false.
        {
            return false;
        }

		curl_close($ch);

		return $result;
	}
	/* Scraping */

	private function stripA($strip)
	{
		if(substr($strip, 0, 2) == "<a")
		{
			preg_match('/<a (.*?)>(.*?)<\/a>/s', $strip, $stripped);
			return $stripped[2];
		}else
		{
			return $strip;
		}
	}

	public function getName()
	{
		if($this->version == 7)
		{
			throw new Exception('Scraping is not supported in PS7.');
		}

		preg_match('/<div id="userName">(.*?) <span>/s', $this->homeContents, $userName);

		$bits = explode(", ", $userName[1]);

		return Array('direct' => $userName[1], 'split' => $bits, '$FirstName' => $bits[1], 'lastname' => $bits[0], 'regular' => $bits[1] . " " . $bits[0]);
	}

	public function parseGrades()
	{
		if($this->version == 7)
		{
			throw new Exception('Scraping is not supported in PS7.');
		}
		$result = $this->homeContents;
		/* Parse different terms */
		preg_match_all('/<tr align="center" bgcolor="#f6f6f6">(.*?)<\/tr>/s', $result, $slices);
		preg_match_all('/<td rowspan="2" class="bold">(.*?)<\/td>/s', $slices[0][0], $slices);
		$slices = $slices[1];
		$slicesCount = count($slices);
		unset($slices[0]);
		unset($slices[1]);
		unset($slices[$slicesCount - 2]);
		unset($slices[$slicesCount - 1]);
		$slices = array_merge(array(), $slices);

		/* Parse classes */
		preg_match('/<table border="1" cellpadding="2" cellspacing="0" align="center" bordercolor="#dcdcdc" width="99%">(.*?)<\/table>/s', $result, $classesdmp);
		$classesdmp = $classesdmp[0];

		preg_match_all('/<tr align="center" bgcolor="(.*?)">(.*?)<\/tr>/s', $classesdmp, $classes, PREG_SET_ORDER);
		unset($classes[count($classes) - 1]);
		unset($classes[0]);
		unset($classes[1]);
		unset($classes[2]);

		foreach($classes as $class)
		{
			preg_match('/<td align="left">(.*?)<br>(.*?)<a href="mailto:(.*?)">(.*?)<\/a><\/td>/s', $class[2], $classData);
			$name = $classData[1];

			preg_match_all('/<td>(.*?)<\/td>/s', $class[2], $databits, PREG_SET_ORDER);

			$data = Array('name' => $name, 'teacher' => Array('name' => $classData[4], 'email' => $classData[3]), 'period' => $databits[0][1], 'absences' => $this->stripA($databits[count($databits) - 2][1]), 'tardies' => $this->stripA($databits[count($databits) - 1][1]));

			$databitsCount = count($databits);
			unset($databits[0]);
			unset($databits[$databitsCount - 2]);
			unset($databits[$databitsCount - 1]);
			$databits = array_merge(Array(), $databits);

			preg_match_all('/<a href="scores.html\?(.*?)">(.*?)<\/a>/s', $class[2], $scores, PREG_SET_ORDER);

			$i = 0;

			foreach($scores as $score)
			{
				preg_match('/frn\=(.*?)\&fg\=(.*)/s', $score[1], $URLbits);
				$scoreT = explode("<br>", $score[2]);
				if($scoreT[0] !== "--" && !is_numeric($scoreT[0])) // This is here to handle special cases with schools using letter grades
					$data['scores'][$URLbits[2]] = $scoreT[1];
				//  or grades not being posted
				else
					$data['scores'][$URLbits[2]] = $scoreT[0];

				$i++;
			}

			$classesA[] = $data;
		}

		return $classesA;
	}
}
