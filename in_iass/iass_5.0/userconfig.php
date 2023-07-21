<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session;
$uid=session('iass');
//エラー
include 'error.php';
//db接続
include 'db.php';
include 'file.php';

//userconfig取得
$dir=$cfg_userdata.$uid;
if(file_exists($dir)==0){
    mkdir($dir);
}
$dir=$dir.'/config.json';
if(file_exists($dir)==0){
    touch($dir);
    $userdata=$cfg_defaultUserdata;
    $userdata=json_encode($userdata);
    if(fileput($dir, $userdata)==0){
        error('userdataを書き込めませんでした。');
    }
}else{
    $userdata=fileget($dir);
    if($userdata===0){
        error('userdataを読み込めませんでした。');
    }
}

//読み込み
if(isset($_GET['userconfig_read'])){
    echo $userdata;
}
//書き込み
if(isset($_GET['userconfig_write'])){
    if(isset($_GET['data'])){
        $uc=htmlspecialchars($_GET['userconfig_write'], ENT_QUOTES);
        $data=htmlspecialchars($_GET['data'], ENT_QUOTES);
        $userdata=json_decode($userdata, true);
        foreach($userdata as $key=>$value){
            if($key==$uc){
                $userdata[$key]=$data;
                if(fileput($dir, json_encode($userdata))==0){
                    error('userdataを書き込めませんでした。');
                }else{
                    echo 1;
                }
            }
        }
    }
}
?>