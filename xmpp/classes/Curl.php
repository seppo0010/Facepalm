<?php
class Curl {
	static function do_post($url, $fields = NULL) {
		$ch = curl_init();
		//set the url, number of POST vars, POST data 
		curl_setopt($ch,CURLOPT_URL,$url);
		if ($fields != NULL) {
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($fields));
		}
		//execute post 
		ob_start();
		$result = curl_exec($ch);
		//close connection 
		curl_close($ch);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
