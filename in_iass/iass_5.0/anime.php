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
include 'time.php';

$time=time();
//再生時間
function playtime($time){
    if($time<60){
        return $time.'秒';
    }elseif($time<3600){
        $tmp=intval($time/60);
        return $tmp.'分'.($time-($tmp*60)).'秒';
    }else{
        $tmp=intval($time/3600);
        $tmp2=intval(($time-($tmp*3600))/60);
        return $tmp.'時間'.$tmp2.'分'.($time-(($tmp*3600)+($tmp2*60))).'秒';
    }
}

//コンテンツID取得
if(isset($_GET['aid'])){
    $aid=htmlspecialchars($_GET['aid'], ENT_QUOTES);
    //一覧取得
    $dir_info=$cfg_datadir.$aid.'/info.json';
    $info=fileget($dir_info);
    if($info===0){
        error('IASS/Notice contentlist aid='.$aid);
    }
    $info=json_decode($info, true);
    //アニメ名
    $name1=db_encvalue($db->query('SELECT name FROM anime WHERE aid='.$aid.';'));
    $html='<div id="maincontent"><div id="main_movie"><h2>'.$name1.'</h2>';
    //player
    if(isset($_GET['id']) && isset($_GET['quality'])){
        //動画情報
        $id=htmlspecialchars($_GET['id'], ENT_QUOTES);
        $quality=htmlspecialchars($_GET['quality'], ENT_QUOTES);
        //dir宣言
        $dir=$cfg_datadir.$aid.'/'.$id.'-'.$quality.'.mp4';
        //動画情報
        $true=0;
        foreach($info as $cool=>$coolInfo){
            for($i=0; $i<count($coolInfo); $i++){
                if($coolInfo[$i]['id']==$id){
                    $name2=$cool;
                    $number=$coolInfo[$i]['number'];
                    $subtitle=$coolInfo[$i]['subtitle'];
                    $playtime=$coolInfo[$i]['playtime'];
                    $qualitySelect='';
                    $downloadSelect = '';
                    foreach($coolInfo[$i]['quality'] as $quality_link){
                        //再生
                        $purl="'anime.php?aid=".$aid."&id=".$coolInfo[$i]["id"]."&quality=".$quality_link."'";
                        $qualitySelect.='<input type="button" value="'.$quality_link.'('.$cfg_qualityDisplay[$quality_link].')" onclick="redirect('.$purl.')">';
                        //ダウンロード
                        $dlurl="'download.php?aid=".$aid."&id=".$coolInfo[$i]["id"]."&quality=".$quality_link."'";
                        $downloadSelect.='<input type="button" value="ダウンロード>'.$quality_link.'" onclick="redirect('.$dlurl.')">';
                    }
                    //次動画情報
                    if(isset($coolInfo[$i+1]['number'])){
                        if($coolInfo[$i+1]['number']==$number+1){
                            $nextid=$coolInfo[$i+1]['id'];
                        }else{
                            $nextid=-1;
                        }
                    }else{
                        $nextid=-1;
                    }
                    //終了
                    $true=1;
                    break;
                }
            }
            //終了
            if($true==1){
                break;
            }
        }
        //再生
        if(file_exists($dir)){
            //開始時間取得
            $startTime=db_encvalue($db->query('SELECT playtime FROM play WHERE uid='.$uid.' AND aid='.$aid.' AND id='.$id.' ORDER BY pid DESC;'));
            if($startTime==null){
                $startTime=0;
            }elseif($playtime-$startTime<120){
            	$startTime=0;
            }
            //再生履歴登録
            db_checkError($db->query('INSERT INTO play (uid, aid, id, quality, time, playtime) VALUE('.$uid.', '.$aid.', '.$id.', "'.$quality.'", '.$time.', 0);'));
            $pid=db_encvalue($db->query('SELECT pid FROM play WHERE uid='.$uid.' AND time='.$time.';'));
            //html生成
            $html.='<video type="video/mp4" controls preload="metadata" playsinline poster="image.php?aid='.$aid.'&id='.$id.'" id="main_movie_player">
            <source src="movie2.php?aid='.$aid.'&id='.$id.'&quality='.$quality.'">
            <p>ブラウザがH264/mp4をサポートしていません。</p>
            </video>
            <p id="main_anime_info"><strong>'.$name1.' '.$name2.' '.$number.'話 '.$subtitle.'</strong> '.$quality.'('.$cfg_qualityDisplay[$quality].')</p>
            '.$qualitySelect.'<input type="button" value="" id="autoplay" onclick="autoplay()"><input type="button" value="最初から再生" onclick="setPlayTime(0)">'.$downloadSelect;
            //client script
            $html.='<script type="text/javascript">
            //php受け渡しグローバル変数宣言
            var aid='.$aid.';
            var nextid='.$nextid.';
            var quality="'.$quality.'";
            var startTime='.$startTime.';
            var pid='.$pid.';
            //グローバル変数宣言
            var userconfig;
            var playtime;
            var playtimeOld=0;
            //リダイレクト
            function redirect(url){
                location.href=url;
            }
            //自動再生
            function autoplay(){
                //変更
                if(userconfig.autoplay==0){
                    userconfig.autoplay=1;
                    display="(オン)";
                }else{
                    userconfig.autoplay=0;
                    display="(オフ)";
                }
                //更新
                ajax("userconfig.php?userconfig_write=autoplay&data="+userconfig.autoplay, "get");
                //表示
                document.getElementById("autoplay").value="自動再生"+display;
            }
            //画面移動
            document.getElementById("main_movie_player").addEventListener("ended", function(){
                if(userconfig.autoplay==1){
                    if(nextid!=-1){
                        redirect("?aid="+aid+"&id="+nextid+"&quality="+quality);
                    }
                }
            });
            //再生時間記録
            function savePlayTime(){
                playtime=document.getElementById("main_movie_player").currentTime;
                if(playtime!=playtimeOld){
                    if(playtime>5){
                        ajax("movie_location.php?pid="+pid+"&playtime="+playtime, "get");
                        console.log("get");
                    }
                }
                playtimeOld=playtime;
                setTimeout(savePlayTime, 5000);
            }
            //再生時間セット
            function setPlayTime(time){
                document.getElementById("main_movie_player").currentTime=time;
            }
            //ロード時
            window.addEventListener("load", function(){
                //設定読み込み
                userconfig=ajax("userconfig.php?userconfig_read", "get");
                //自動再生状態
                if(userconfig.autoplay==0){
                    var display="(オフ)";
                }else{
                    var display="(オン)";
                }
                document.getElementById("autoplay").value="自動再生"+display;
                //再生位置セット
                setPlayTime(startTime-5);
                //再生時間記録開始
                savePlayTime();
            });
            </script>';
        }else{
            //ファイルが見つかりません
            $html.="<p>エラー<br>動画ファイルが見つかりません。<br>管理者に連絡してください。</p>";
        }
    }
    //再生リスト
    $html.='</div><div class="main_select"><a href="anime_edit.php?aid='.$aid.'">編集</a> <a href="upload_1.php?aid='.$aid.'">追加</a>';
    foreach($info as $k1=>$v1){
        $html.='<h3>'.$k1.'</h3>';
        if($v1==null){
            //コンテンツなし
            $html.='<p>コンテンツが見つかりません。</p>';
            continue;
        }
        foreach($v1 as $v2){
            //再生履歴
            $beforeplay=time_yutb(db_encvalue($db->query('SELECT time FROM play WHERE uid='.$uid.' AND aid='.$aid.' AND id='.$v2['id'].' ORDER BY pid DESC LIMIT 1;')));
            //再生リンク
            $playlink=null;
            foreach($v2['quality'] as $v3){
                $playlink.='<a href="?aid='.$aid.'&id='.$v2['id'].'&quality='.$v3.'">'.$v3.'('.$cfg_qualityDisplay[$v3].')</a> ';
            }
            //html生成
            $html.='<div class="main_select_class">
            <img src="image.php?aid='.$aid.'&id='.$v2['id'].'">
                <div>
                    <p><strong>'.$v2['number'].'話　'.$v2['subtitle'].'</strong></p>
                    <p>投稿:'.$v2['from'].'　投稿:'.time_yutb($v2['time']).'　再生時間:'.playtime($v2['playtime']).'　視聴:'.$beforeplay.'</p>
                    '.$playlink.'
                </div>
            </div>';
        }
    }
    $html.='</div></div>';
    //出力
    include 'comhtml.php';
    comhtml($name1, $html);
}else{
    //$_GET未指定
    error('IASS/Notice $_GET["aid"]');
}
?>
