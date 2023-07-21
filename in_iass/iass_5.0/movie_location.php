<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session_multiacces;
$uid=session_multiacces('iass');
//エラー
include 'error.php';
//db接続
include 'db.php';

//再生時間
if(isset($_GET['pid'])==0){
    header('http', true, 400);
    exit();
}
if(isset($_GET['playtime'])==0){
    header('http', true, 400);
    exit();
}
//再生時間取得
$playtime=htmlspecialchars($_GET['playtime'], ENT_QUOTES);
$pid=htmlspecialchars($_GET['pid'], ENT_QUOTES);
$playtime=intval($playtime);
$pid=intval($pid);
//再生時間記録
db_checkError($db->query('UPDATE play SET playtime='.$playtime.' WHERE pid='.$pid.';'));
echo 1;
exit();
?>