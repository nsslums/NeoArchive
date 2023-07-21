<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session;
$uid=session('iass');
//エラー
include 'error.php';
//file
include 'file.php';
//db接続
include 'db.php';

//main
include 'upload_javascript.php';
$html='<div id="maincontent">
<div class="upload_center">
    <h2>アップロード</h2>
</div>
<div id="upload">
    <div class="upload_center">
        <p><strong>アップロードファイル選択</strong>>>アップロード中>>必要情報入力>>データ処理中>>アップロード完了</p>
        <p><font color="ff0000">アップロード中にサイト内を移動すると処理に失敗する場合があります。</font></p>
        <br><p>アップロードするファイルを選択してください。<br>mp4/H264以外のファイルはアップロードできません。</p>';
if(isset($_GET['aid'])){
    $html.='<form action="upload_2.php?aid='.$_GET['aid'].'" enctype="multipart/form-data" method="POST" accept-charset="UTF-8" onsubmit="uploadStart()">';
}else{
    $html.='<form action="upload_2.php" method="POST" enctype="multipart/form-data" accept-charset="UTF-8" onsubmit="uploadStart()">';
}
$html.='<input type="file" name="IA_file[]" required="" multiple="multiple" accept="video/mp4">';
$html.='<input type="submit" value="アップロード">
                </form>
            </div>
        </div>
    <div id="uploading">
        <div class="upload_center">
            <p>アップロードファイル選択>><strong>アップロード中</strong>>>必要情報入力>>データ処理中>>アップロード完了</p>
        </div>
        <img src="icon/uploading.gif">
    </div>
    '.$script.'
</div>';

//html出力
include 'comhtml.php';
comhtml('アップロード', $html, 'style-upload.css');
?>