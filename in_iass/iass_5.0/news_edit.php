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

$time=time();

//newsの追加
if(isset($_POST['IA_news_title'])){
    if(isset($_POST['IA_news_content'])){
        $title=htmlspecialchars($_POST['IA_news_title'], ENT_QUOTES);
        $content=htmlspecialchars($_POST['IA_news_content'], ENT_QUOTES);
        db_checkError($db->query('INSERT INTO news (title, content, time) VALUE ("'.$title.'", "'.$content.'", '.$time.')'));
    }
}
//削除
if(isset($_POST['IA_news_count'])){
    //データ取得
    $news=$db->query('SELECT * FROM news ORDER BY nid DESC;');
    db_checkError($news);
    //$_POST
    foreach($news as $record){
        if(isset($_POST['IA_news_delete_'.$record['nid']])){
            if($_POST['IA_news_delete_'.$record['nid']]==1){
                //削除
                db_checkError($db->query('DELETE FROM news WHERE nid='.$record['nid'].';'));
            }
        }
    }
}
//データ取得
$news=$db->query('SELECT * FROM news ORDER BY nid DESC;');
db_checkError($news);
//html変成
$html='<div id="maincontent">
<h2>NEWSの追加</h2>
<form action="news_edit.php" method="POST" accept-charset="UTF-8">
    <br><input type="text" placeholder="タイトル" name="IA_news_title">
    <br><input type="text" placeholder="内容" name="IA_news_content">
    <br><input type="submit" value="追加">
</form>
<form action="news_edit.php" method="POST" accept-charset="UTF-8">
    <table>
        <tr>
            <th>日　</th><th>タイトル　</th><th>内容　</th><th>削除</th>
        </tr>';
foreach($news as $record){
    $html.='<tr>
        <td>'.date('n/d', $record['time']).'　</td>
        <td>'.$record['title'].'　</td>
        <td>'.$record['content'].'　</td>
        <td><input type="checkbox" name="IA_news_delete_'.$record['nid'].'" value="1"></td>
    </tr>';
}
$html.='</table>
<input type="hidden" name="IA_news_count" value="">
<input type="submit" value="適用">
</form>
</div>';
//出力
include 'comhtml.php';
comhtml('NEWSの追加', $html);
?>