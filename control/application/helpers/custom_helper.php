<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('asset_url'))
{
	function asset_url()
	{
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$root = explode('/',$_SERVER['PHP_SELF']);
		return $protocol.$_SERVER['HTTP_HOST'].'/uploads/';
	}
}

if ( ! function_exists('web_url'))
{
	function web_url()
	{
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$root = explode('/',$_SERVER['PHP_SELF']);
		return $protocol.$_SERVER['HTTP_HOST'];
	}
}

if ( ! function_exists('GetLabel'))
{
	function GetLabel($status)
	{
		switch($status){
			case "open":
			$label = "danger";
			break;
			case "closed":
			$label = "success";
			break;
			default:
			$label = "warning";
			break;
		}
		return $label;
	}
}


if ( ! function_exists('GetNotification'))
{
	function GetNotification($success,$err)
	{
		$notif = array();
			$btn_plus = "display:none;";
			if(!empty($success)){
				$btn_plus = "";
				$notif[] = array(
									"notif_title"=>$success,
									"notif_class"=>"alert-success"
									);
			}else if(!empty($err)){
				$notif[] = array(
									"notif_title"=>$err,
									"notif_class"=>"alert-error"
									);
			}
		return compact("notif","btn_plus");
	}
}

if ( ! function_exists('DropdownYears'))
{
	function DropdownYears()
	{
		$start = date("Y",strtotime("-1 year"));
		$end = date("Y", now());

		$years = array();
		for($start;$start<=$end;$start++){
			$years[$start] = $start;
		}

		return $years;
	}
}

if ( ! function_exists('DropdownMonths'))
{
	function DropdownMonths()
	{
		$months = array();
		for ($i = 0; $i < 12; $i++) {
	        $time = strtotime(sprintf('%d months', $i));     
	        $months[date('n', $time)] = date('F', $time);
	    }

	    return $months;
	}
}

if ( ! function_exists('GenerateRandomString'))
{
	function GenerateRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
	}
}

if ( ! function_exists('GenerateUniqueCode'))
{
	function GenerateUniqueCode($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = '';

	    for ($p = 0; $p <= $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }

	    return $string;
	}
}

if ( ! function_exists('Currency'))
{
	function Currency($currency)
	{
		$c = "Rp. ";
		if($currency === 'USD'){
			$c = '$ ';
		}
		return $c;
	}
}

if ( ! function_exists('Pricing'))
{
	function Pricing($currency='USD', $price=0)
	{
		$price = !empty($price) ? number_format($price) : $price;
		$c = Currency($currency);
		$p = !empty($price) ? $c.$price : 0;
		return $p;
	}
}

if ( ! function_exists('SetK'))
{
	function setK($num) {
	  if($num>1000) {
	        $x = round($num);
	        $x_number_format = number_format($x);
	        $x_array = explode(',', $x_number_format);
	        $x_parts = array('K', 'M', 'B', 'T');
	        $x_count_parts = count($x_array) - 1;
	        $x_display = $x;
	        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
	        $x_display .= $x_parts[$x_count_parts - 1];
	        return $x_display;
	  }
	  return number_format($num);
	}
}

if ( ! function_exists('GetItemTarget'))
{

	function GetItemTarget($id=0)
		{
			$ref_name = "stocks";
			if($id > 2){
				$ref_name = "items";
			}
			return $ref_name;
		}
}

if ( ! function_exists('GetItemCategory'))
{
	function GetItemCategory($target, $category){
		if($target == "stocks"){
			if($category > 1){
				return $category - 1;
			}else{
				return $category;
			}
		}else{
			return $category - 1;
		}
	}
}

if ( ! function_exists('GetItemReference'))
{

	function GetItemReference($id=0)
		{
			switch($id){
				case 1: $ref_name = "stock";
				break;
				case 2: $ref_name = "stock";
				break;
				case 3: $ref_name = "treatment";
				break;
				case 4: $ref_name = null;
				break;
				default: $ref_name = null;
				break;
			}
			
			return $ref_name;
		}
}

if ( ! function_exists('GetPaymentMethod'))
{

	function GetPaymentMethod()
		{
			return array(
						  "cash"=>"Cash",
						  "debit_or_credit"=>"Debit/Credit",
						  "saldo"=>"Saldo",
						  "reward"=>"Poin Reward"
						  );
		}
}

if ( ! function_exists('GetFileTypes'))
{
	function GetFileTypes($ext)
	{
		$types = "";
		switch($ext){
			case "pdf" : $types = $ext;
			break;
			case "xls" : $types = "excel";
			break;
			case "xlsx" : $types = "excel";
			break;
			case "doc" : $types = "word";
			break;
			case "docx" : $types = "word";
			break;
		}
		return $types;
	}
}

?>
