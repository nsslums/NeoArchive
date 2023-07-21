<?php
function comhtml($title, $main, $style_add=null, $script=null){
    //宣言
    global $cfg_defaultUserdata;
    //ユーザー設定
    global $uid, $cfg_userdata, $cfg_session_logout;
    $dir=$cfg_userdata.$uid;
    if(file_exists($dir)==0){
        mkdir($dir);
    }
    $dir=$dir.'/config.json';
    if(file_exists($dir)==0){
        touch($dir);
        $userdata=$cfg_defaultUserdata;
        fileput($dir, json_encode($userdata));
    }else{
        $userdata=fileget($dir);
        if($userdata===0){
            error('userdataを読み込めませんでした。');
        }
        $userdata=json_decode($userdata, true);
    }
    //map
    if($title!=null){
        $title='/'.$title;
    }
    //add-style
    $style=null;
    if($style_add!=null){
        $style.='@import url("'.$style_add.'");';
    }
    //body背景
    if($userdata['background']=='default'){
        $style.='body{
            background-color: #ffffff;
        }';
    }else{
        $style.='body{
            background-image: url("image.php?bg='.$userdata['background'].'");
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }';
    }
    $tmp="'<div class=sub_news_table><h3>'+data.time+' '+data.title+'</h3><p>'+data.content+'</p></div>'";
    //出力
    echo '<!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>@import url("style.css");'.$style.'</style>
        <link rel="shortcut icon" href="icon/favicon.ico">
        <title>IASS'.$title.'</title>
    </head>
    <body>
        <div id="top">
            <a href="index.php">
                <img src="icon/logo.png" id="top_logo">
            </a>
            <form action="index.php" method="GET" accept-charset="UTF-8" id="top_search">
                <input type="text" name="keyword" placeholder="検索..." required>
                <input type="submit" value="検索">
            </form>
            <a href="request.php"><img src="icon/onair.svg" class="top_icon"></a>
            <a href="upload_1.php"><img src="icon/upload.svg" class="top_icon"></a>
            <a href="set_userconfig.php"><img src="icon/setting.svg" class="top_icon"></a>
            <a href="'.$cfg_session_logout.'"><img src="icon/exit.svg" class="top_icon"></a>
        </div>
        <div id="top_smart">
            <form action="https://***REMOVED***/index.php" method="GET" accept-charset="UTF-8" id="top_search">
                <input type="text" name="keyword" placeholder="検索..." required="">
                <input type="submit" value="検索">
            </form>
        </div>
        <div id="subcontent">
            <h2>サイトマップ</h2>
            <a href="index.php">トップ</a><a href="">'.$title.'</a>
            <h2>新着情報</h2>
            <div id="sub_news">
                <div class="sub_news_table">
                    <p>Now Loading...</p>
                </div>
            </div>
        </div>
        '.$main.'
    </body>
    <script type="text/javascript" src="jquery-3.4.1.min.js"></script>
    <script type="text/javascript">
    //ajax
    function ajax(url, gp, data=null, ed=1){
        var returndata;
        $.ajax({
            url: url,
            type: gp,
            cache: false,
            dataType: "json",
            data: data,
            async: false
        })
        .done(function(data){
            returndata=data;
        })
        .fail(function(){
            if(ed==1){
                alert(".fail ajax("+url+")");
                location.reload;
            }
        });
        return returndata;
    }
    //宣言
    var newsData, nid, html;
    //ニュース
    function news(){
        newsData=ajax("newsget.php?nid="+nid, "get", null, 0);
        console.log(nid);
        if(newsData.number>0){
            newsData.data.forEach(function(data){
                html='.$tmp.'+html;
                nid=data.nid;
            });
            document.getElementById("sub_news").innerHTML=html;
        }
        setTimeout(news, 30000);
    }
    //ニュース実行
    window.addEventListener("load", function(){
        nid=0;
        html="";
        news();
    });
    </script>
    '.$script.'
    </html>';
}
?>