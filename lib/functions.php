<?php


function fix_bogdan($str =''){
	$str = trim($str);
	if("0" == substr($str,-1)){
		$str = substr($str, 0, -1);
	}
	$str = trim($str);
	if("}" == substr($str,-1)){
		$str = substr($str, strpos($str, "{") );
	}
	if("]" == substr($str,-1)){
		$str = substr($str, strpos($str, "[") );
	}
	return $str;
}
