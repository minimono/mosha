<?php
function h($str){
  return htmlspecialchars($str, ENT_QUOTES);
}

function getTrimString($str, $len){
	$cnt = mb_strlen($str);
	$str = mb_substr($str, 0 ,$len);
	if($cnt > $len){
    $str .= '...';
  }
	return $str;
}
?>