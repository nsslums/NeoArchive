<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session_multiacces;
$uid=session_multiacces('iass');

//動画情報取得
if(isset($_GET['aid'])==0 OR isset($_GET['id'])==0 OR isset($_GET['quality'])==0){
	header('http', true, 403);
	exit();
}
$aid=htmlspecialchars($_GET['aid'], ENT_QUOTES);
$id=htmlspecialchars($_GET['id'], ENT_QUOTES);
$quality=htmlspecialchars($_GET['quality'], ENT_QUOTES);
//dir
$dir=$cfg_datadir.$aid.'/'.$id.'-'.$quality.'.mp4';
if(file_exists($dir)==0){
	header('http', true, 403);
	exit();
}

$size = filesize($dir);
$fp = fopen($dir,"rb");

header("Accept-Ranges: bytes");
header("Content-Type: application/force-download");
header("Content-Length: {$size}");
header('Content-disposition: attachment; filename="'.$id.'-'.$quality.'.mp4"');

//出力
readfile($dir);
ob_flush();

fclose($fp);