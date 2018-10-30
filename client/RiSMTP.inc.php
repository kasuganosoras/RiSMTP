<?php
class RiSMTPClient {
	
	public $api;
	public $apipass;
	public $host;
	public $port;
	public $auth;
	public $user;
	public $pass;
	
	public function __construct($api, $apipass, $host, $port, $auth = false, $user = "", $pass = "") {
		$this->api = $api;
		$this->apipass = $apipass;
		$this->host = $host;
		$this->port = $port;
		if($auth) {
			$this->auth = "true";
			$this->user = $user;
			$this->pass = $pass;
		} else {
			$this->auth = "false";
			$this->user = "";
			$this->pass = "";
		}
	}
	
	public function sendMail($to, $from, $subject = "", $body, $mailtype = "", $cc = "", $bcc = "", $headers = "", $debug = false) {
		if($debug) {
			$debug = "true";
		} else {
			$debug = "false";
		}
		$mail = Array(
			'host'     => $this->host,
			'port'     => $this->port,
			'auth'     => $this->auth,
			'user'     => $this->user,
			'pass'     => $this->pass,
			'cc'       => $cc,
			'bcc'      => $bcc,
			'to'       => $to,
			'from'     => $from,
			'subject'  => $subject,
			'body'     => $body,
			'mailtype' => $mailtype,
			'headers'  => $headers,
			'debug'    => $debug,
			'apipass'  => md5(sha1($this->apipass))
		);
		return $this->http($this->api, $mail);
	}
	
	public function http($url, $post = '', $cookie = '', $headers = '', $returnCookie = 0) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_REFERER, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		if ($post) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		}
		if ($cookie) {
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		}
		if ($headers) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($curl);
		if (curl_errno($curl)) {
			return curl_error($curl);
		}
		curl_close($curl);
		if ($returnCookie) {
			list($header, $body) = explode("\r\n\r\n", $data, 2);
			preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
			$info['cookie'] = substr($matches[1][0], 1);
			$info['content'] = $body;
			return $info;
		} else {
			return $data;
		}
	}
}
