<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('asset_url'))
{
	function asset_url()
	{
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$root = explode('/',$_SERVER['PHP_SELF']);
		return $protocol.$_SERVER['HTTP_HOST'].'/saraswatiweb'.'/uploads/';
	}
}

if ( ! function_exists('web_url'))
{
	function web_url()
	{
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$root = explode('/',$_SERVER['PHP_SELF']);
		return $protocol.$_SERVER['HTTP_HOST'].'/saraswatiweb'.'/'.$root[1];
	}
}

if ( ! function_exists('split_firstname_lastname'))
{
	function split_firstname_lastname($fullname)
	{
		$exp = explode(' ',$fullname);
        $firstname = isset($exp[0]) ? $exp[0] : $fullname;
        $lastname = substr($fullname, strlen($firstname)+1, strlen($fullname) - strlen($firstname));

        return array(
        			"firstname"=>$firstname,
        			"lastname"=>$lastname
         			 );
	}
}

if ( ! function_exists('ip'))
{
	function ip()
	{
	    $ipaddress = '';

	    if($_SERVER['REMOTE_ADDR'])
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress ;
	 }
}

?>