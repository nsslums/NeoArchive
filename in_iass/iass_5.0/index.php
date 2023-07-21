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

$time=time()-32400;

//db取得
$anime_edcb=$db->query('SELECT aid, name, icon_id FROM anime WHERE time_edcb>'.($time-604800).' ORDER BY aid DESC;');
db_checkError($anime_edcb);
$anime_basic=$db->query('SELECT aid, name, icon_id FROM anime WHERE time_basic>'.($time-604800).' ORDER BY aid DESC LIMIT 5;');
db_checkError($anime_basic);
$anime=$db->query('SELECT aid, name, icon_id FROM anime ORDER BY aid DESC;');
db_checkError($anime);
//最近の再生
$anime_play=$db->query('SELECT aid, name, icon_id FROM anime WHERE aid IN (SELECT aid FROM (SELECT aid FROM play WHERE uid='.$uid.' ORDER BY pid DESC LIMIT 10) AS play2);');
db_checkError($anime_play);

//コンテンツセレクト作成
function anime_select($aid, $name, $id){
    $html='<a href="anime.php?aid='.$aid.'">
        <div class="main_anime_select">
            <img src="image.php?aid='.$aid.'&id='.$id.'" alt="Error NotFound.">
            <span>'.$name.'</span>
        </div>
    </a>';
    return $html;
}
$html='<div id="maincontent">';
//検索結果
if(isset($_GET['keyword'])){
    //$_GET
    $keyword=htmlspecialchars($_GET['keyword'], ENT_QUOTES);
    //隠しコマンド
    $cmd=explode(' ', $keyword);
    if($cmd[0]=='cmd'){
        if(isset($cmd[1])){
            if($cmd[1]=='news'){
                if(isset($cmd[2])){
                    if($cmd[2]=='edit'){
                        header('location: news_edit.php');
                        exit();
                    }
                }
            }
        }
    }
    //データ取得
    $anime_search=$db->query('SELECT aid, name, icon_id FROM anime WHERE name like "%'.$keyword.'%" ORDER BY aid DESC;');
    db_checkError($anime_search);
    //html変成
    $html.='<h2>検索結果</h2>';
    if($anime_search->rowCount()==0){
        //0件
        $html.='<p>"'.$keyword.'"に一致する検索結果が見つかりませんでした。</p>';
    }else{
        //0件以外
        $html.='<a href="index.php">リセット</a><div class="main_animes">';
        foreach($anime_search as $content){
            $html.=anime_select($content['aid'], $content['name'], $content['icon_id']);
        }
        $html.='</div>';
    }
}
//html生成
$html.='<h2>今期のアニメ</h2><div class="main_animes">';
foreach($anime_edcb as $content){
    $html.=anime_select($content['aid'], $content['name'], $content['icon_id']);
}
$html.='</div><h2>最近追加されたアニメ</h2><div class="main_animes">';
foreach($anime_basic as $content){
    $html.=anime_select($content['aid'], $content['name'], $content['icon_id']);
}
$html.='</div><h2>最近の再生</h2><div class="main_animes">';
foreach($anime_play as $content){
    $html.=anime_select($content['aid'], $content['name'], $content['icon_id']);
}
$html.='</div><h2>すべてのアニメ</h2><div class="main_animes">';
foreach($anime as $content){
    $html.=anime_select($content['aid'], $content['name'], $content['icon_id']);
}
$html.='</div></div>';

//出力
include 'comhtml.php';
comhtml(null, $html);
?>
