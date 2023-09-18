<?php
class CustomUrl extends CBaseUrlRule
{
	public function createUrl($manager, $route, $params, $ampersand)
	{
		foreach($params as $key=>$val) $route.='/'.$key.'/'.$val;

		$key = "myPrivateSecretKey";
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

		return $this->safe_base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $route, MCRYPT_MODE_ECB, $iv));
	}

	public function parseUrl($manager, $request, $pathInfo, $rawPathInfo)
	{
		$key = "myPrivateSecretKey";
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $this->safe_base64_decode($pathInfo), MCRYPT_MODE_ECB, $iv));
	}

	function safe_base64_encode($data)
	{
		return trim(str_replace(array('+','/','='), array('-','_',''), base64_encode($data))); 
	}

	function safe_base64_decode($data)
	{
		$data = str_replace(array('-','_'), array('+','/'), $data);
		$mod4 = strlen($data) % 4;
		if($mod4){
			$data.=substr("====", $mod4);
		} 
		return base64_decode($data);
	}
}