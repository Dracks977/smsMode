<?php 
namespace SmsMode;

class SmsMode
{
	private $api = null;
	private $emetteur = null;
	private $secure = null;
	private $code = [
		'0' => 'Accepté - le message a été accepté par le système et est en cours de traitement',
		'31' => 'Erreur interne',
		'32' => 'Erreur d’authentification',
		'33' => 'Crédits insuffisants',
		'35' => 'Paramètre obligatoire manquant',
		'50' => 'Temporairement inaccessible'
	];

	public function __construct($apikey, $secure = true, $emetteur = NULL)
	{
		$this->api = $apikey;
		$this->emetteur = $emetteur;
		$this->secure = $secure;
	}

	//num separed by , / text of sms / stop default null
	public function send($num, $texte, $stop = NULL)
	{
		$url = 'https://api.smsmode.com/http/1.6/sendSMS.do';
		$texte = addslashes($texte);
		$texte = iconv("UTF-8", "ISO-8859-15", $texte);
		$fields_string = 'accessToken='.$this->api.'&message='.urlencode($texte).'&numero='.$num;

		// stop sms default no
		if (!is_null($stop)) {
			$fields_string .= '&stop=1';
		}

		// emetteur default null == default emetteur
		if (!is_null($this->emetteur)) {
			$fields_string .= '&emetteur='.$this->emetteur;
		}

		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		
		if (!$this->secure) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		
		$result = curl_exec($ch);
		if ($result == false) {
			return curl_error($ch);
		}

		curl_close($ch);
		return $this->handleReponse($result);
	}

	public function getSolde ()
	{
		$url = 'https://api.smsmode.com/http/1.6/credit.do?accessToken=' . $this->api;
		$ch = curl_init();

		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		if (!$this->secure) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		$result = curl_exec($ch);
		if ($result == false) {
			return curl_error($ch);
		}
		return $this->handleReponse($result);
	}

	public function setEmetteur ($e)
	{
		$this->emetteur = $e;
	}

	public function getEmetteur ()
	{
		return $this->emetteur;
	}

	public function setSecure ($e)
	{
		$this->secure = $e;
	}

	public function getSecure ()
	{
		return $this->secure;
	}

	private function handleReponse ($result)
	{
		$r = explode(" | ", $result);
		if (count($r) != 1) {
			$obj = (object) [
				'code' => $r[0],
				'msg' => $this->code[$r[0]],
				'desc' => $r[1],
				'smsID' => $r[2] ? $r[2] : null
			];
			return $obj;
		}
		// spécifique pour le get solde
		return (object) ['code' => '0', 'msg' => $result];
		
	}
}