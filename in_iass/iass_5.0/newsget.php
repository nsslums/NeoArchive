<?php
//設定読み込み
include 'config.php';
//認証
include $cfg_session_multiacces;
$uid=session_multiacces('iass');
//db接続
include 'db.php';
include 'news.php';

if(isset($_GET['nid'])){
    $nid=htmlspecialchars($_GET['nid'], ENT_QUOTES);
    echo json_encode(newsRead($nid));
}else{
    header('http', true, 400);
}
?>