<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session_multiacces;
$uid=session_multiacces('iass');

//ファイル名
if(isset($_GET['aid']) AND isset($_GET['id'])){
	$file=$cfg_datadir.$_GET['aid'].'/'.$_GET['id'].'.jpg';
}elseif(isset($_GET['bg'])){
	$file=$cfg_userdata.$uid.'/'.$_GET['bg'];
}else{
	header('http', true, 404);
	exit();
}

//ファイル確認
if(file_exists($file)==0){
	header('http', true, 404);
	exit();
}
//ファイル情報読み込み
$size = filesize($file);
$fp = fopen($file,"rb");
//ファイル情報送信
header("Content-Length: {$size}");
header("Content-Type: image/jpg");
//ファイルデータ送信
readfile($file);
ob_flush();
fclose($fp);
?>
