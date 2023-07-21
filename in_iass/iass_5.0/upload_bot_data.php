<?php
//設定読み込み
include 'config.php';
include 'db.php';
include 'file.php';
include 'news.php';

$key='***REMOVED***';//iass_recclient.phpとペアである必要があります。

$pass_mov='movdata_id-'.$key;
$pass_prg='prgdata_id-'.$key;

if(isset($_FILES[$pass_mov]['tmp_name'])==1){
	$tmp=$_FILES[$pass_mov]['tmp_name'];
	$name=explode('-', basename($_FILES[$pass_mov]['name']));
	if(count($name)!=3){
		newsWrite('システムアップロードエラー', 'DIR ERROR<br>実行きせずに終了しました。', time());
		exit;
	}
	//軽量版
	if($name[2]=='480p.mp4'){
		$dir=$cfg_datadir.$name[0].'/info.json';
		$info=fileget($dir);
		$info=json_decode($info, true);
		foreach($info as $key=>$cool){
			foreach($cool as $key2=>$cnt){
				if($cnt['id'] == $name[1]){
					$info[$key][$key2]['quality'][1]='480p';
				}
			}
		}
		fileput($dir, json_encode($info));
	}
	//dir設定
	$trg=$cfg_datadir.$name[0].'/'.$name[1].'-'.$name[2];
	$icon=$cfg_datadir.$name[0].'/'.$name[1].'.jpg';
	echo '<br><br>'.$trg.'<br>'.$icon.'<br><br>';
	//アップロード
	echo 'mov';
	if(file_exists($trg)==0){
		//アップロード
		move_uploaded_file($tmp, $trg);
		//icon作成
		exec($cfg_ffmpeg.' -i "'.$trg.'" -ss 6 -vframes 1 -f image2 -s 427x240 "'.$icon.'"');
	}else{
		newsWrite('システムアップロードエラー', 'EDCB同一ファイルがあります。<br>'.$name[0].'/'.$name[1].'-'.$name[2].'<br>上書きせずに終了しました。', time());
	}
}elseif(isset($_FILES[$pass_prg]['tmp_name'])==1){
	$tmp=$_FILES[$pass_prg]['tmp_name'];
	$trg=explode('-', basename($_FILES[$pass_mov]['name']));
	$trg=$cfg_datadir.$trg[0].'/'.$trg[1];
	//アップロード
	if(file_exists($trg)==0){
		move_uploaded_file($tmp, $trg);
	}else{
		//newsWrite('システムアップロードエラー', 'EDCB同一ファイルがあります。<br>上書きせずに終了しました。', time());
	}
}else{
	echo 'NOPOST';
}

exit;
?>