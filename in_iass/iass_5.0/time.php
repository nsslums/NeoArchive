<?php
function time_yutb($time){
	//return $time;
	if($time==null){
		return '未視聴';
	}

	$now=time();

	$day=86400;
	$p_time=$now-$time;

	if(60>$p_time){
		//59秒以下
		$pt='たった今';
	}elseif(3600>$p_time){
		//59分以内
		$pt=intval($p_time/60).'分前';
	}elseif($day>$p_time){
		//24時間以内
		$pt=intval($p_time/3600).'時間前';
	}elseif(($day*30)>$p_time){
		//1か月以内
		$pt=intval($p_time/$day).'日前';
	}elseif(($day*365)>$p_time){
		//1年以内
		$pt=intval($p_time/($day*30)).'か月前';
	}else{
		//それ以上前
		$pt=intval($p_time/($day*365)).'年前';
	}

	return $pt;
}
?>