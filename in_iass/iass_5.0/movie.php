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

$etag = md5($_SERVER["REQUEST_URI"]).$size;

if(@$_SERVER["HTTP_RANGE"]){
	list($start,$end) = sscanf($_SERVER["HTTP_RANGE"],"bytes=%d-%d");
	if(empty($end)) $end = $start + 2000000 - 1;
	if($end>=($size-1)) $end = $size - 1;
	header("HTTP/1.1 206 Partial Content");
	header("Content-Range: bytes {$start}-{$end}/{$size}");
	$size = $end - $start + 1;
	fseek($fp,$start);
}

header("Accept-Ranges: bytes");
header("Content-Type: video/mp4");
header("Content-Length: $size");
//header('Etag: "\'.$etag\"');

//出力
echo fread($fp, $size);
//echo fread($fp, 10000000);

//キャッシュ消去
ob_flush();

fclose($fp);
?>
