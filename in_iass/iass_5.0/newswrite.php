<?php
//設定読み込み
include 'config.php';
//認証
include $cfg_session_multiacces;
$uid=session_multiacces('iass');
//db接続
include 'db.php';
include 'news.php';

if(isset($_GET['title']) AND isset($_GET['content'])){
    $title=htmlspecialchars($_GET['title'], ENT_QUOTES);
    $content=htmlspecialchars($_GET['content'], ENT_QUOTES);
    newsWrite($title, $content, time());
    echo '書き込み完了';
}
?>