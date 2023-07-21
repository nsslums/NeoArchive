<?php
//設定ファイル読み込み
include 'config.php';
//認証
if(isset($_GET['bot'])){
	if(isset($_GET['key'])){
		if($_GET['key']==='9921634857300934562'){
			//正常
		}else{
			header('http', true, 404);
			exit();
		}
	}else{
		header('http', true, 404);
		exit();
	}
}else{
	include $cfg_session_multiacces;
	$uid=session_multiacces('iass');
}

//ファイル名
if(isset($_GET['aid']) AND isset($_GET['id']) AND isset($_GET['quality'])){
    $file=$cfg_datadir.$_GET['aid'].'/'.$_GET['id'].'-'.$_GET['quality'].'.mp4';
    $name=$_GET['aid'].'-'.$_GET['id'].'-'.$_GET['quality'].'.mp4';
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
//header('Content-Type: audio/mp4');
header('Content-Length: {'.$size.'}');
header('Content-Disposition: attachment; filename="'.$name.'"');
//ファイルデータ送信
readfile($file);
ob_flush();
fclose($fp);
?>