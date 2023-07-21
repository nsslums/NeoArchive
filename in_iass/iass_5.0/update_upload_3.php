<?php
ini_set('display_errors', "On");
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

//時間
$time=time();

//再入力
function reInput($mes){
    global $aid, $uploadid;
    if($aid!==0){
        $global='&aid='.$aid;
    }else{
        $global=null;
    }
    header('location: upload_2.php?uploadid='.$uploadid.$global.'&mes='.$mes);
    exit();
}
//aidチェック
if(isset($_GET['aid'])){
    $aid=$_GET['aid'];
}else{
    error('aidがありません。');
}
//name1チェック
if(isset($_POST['IA_name1'])){
    $name1=htmlspecialchars($_POST['IA_name1'], ENT_QUOTES);
}else{
    error('htmlが異常です。');
}
//name2チェック
if(isset($_POST['IA_name2'])){
    $name2=htmlspecialchars($_POST['IA_name2'], ENT_QUOTES);
}else{
    error('htmlが異常です。');
}
/*
//データチェック
if($aid==0){
    if(db_encvalue($db->query('SELECT count(name) FROM anime WHERE name="'.$name1.'";'))==1){
        reInput('アニメ名1は既に存在します。既に存在するアニメに追加してください。');
    }
    $group2=false;
}else{
    //name2重複チェック
    $t=0;
    foreach($info as $k1=>$v1){
        if($k1==$name2){
            $t=1;
        }
    }
    if($t==1){
        $group2=true;
    }else{
        $group2=false;
    }
}*/
//動画情報取得
$info_upload_count=1000;
for($i=0; $i<$info_upload_count; $i++){
    //終了
    if(isset($_POST['IA_info_number_'.$i])==0){
        $info_upload_count=$i;
        break;
    }
    //取得
    if(isset($_POST['IA_info_number_'.$i])===0){
        error('htmlが異常です。');
    }
    if(isset($_POST['IA_info_subtitle_'.$i])===0){
        error('htmlが異常です。');
    }
    if(isset($_POST['IA_info_from_'.$i])===0){
        error('htmlが異常です。');
    }
    $info_fid[$i]=htmlspecialchars($_POST['IA_info_fid_'.$i], ENT_QUOTES);
    $info_this[$i]['number']=htmlspecialchars($_POST['IA_info_number_'.$i], ENT_QUOTES);
    $info_this[$i]['subtitle']=htmlspecialchars($_POST['IA_info_subtitle_'.$i], ENT_QUOTES);
    $info_this[$i]['from']=htmlspecialchars($_POST['IA_info_from_'.$i], ENT_QUOTES);
    $info_this[$i]['id']=$i;
}
//dir宣言
$uploadfolder='/mnt/datadisk/iass/anime_data/'.$aid.'-';
//動画品質評価
function get_movie_info($fid){
    //宣言
    global $cfg_ffprobe, $uploadfolder;
    $dirMovie=$uploadfolder.$fid.'.mp4';
    $dirInfo='/var/www/bm_iass/userdata/movie_info_tmp.json';
    //ファイル存在確認
    if(file_exists($dirMovie)===0){
        error('アップロードしたファイルがありません。');
    }
    exec($cfg_ffprobe.' -i "'.$dirMovie.'" -show_streams -of json > "'.$dirInfo.'"');
    $vinfo=fileget($dirInfo);
    //ファイル読み込み
    if($vinfo===0){
        echo $cfg_ffprobe.' -i "'.$dirMovie.'" -show_streams -of json > "'.$dirInfo.'"';
        error('ffprobeの結果が読み込めません。');
    }
    //tmpファイル削除
    unlink($dirInfo);
    //必要データ抽出
    $vinfo=json_decode($vinfo, true);
    $rvinfo['codec_name']=$vinfo['streams'][0]['codec_name'];
    $rvinfo['width']=$vinfo['streams'][0]['width'];
    $rvinfo['height']=$vinfo['streams'][0]['height'];
    $rvinfo['duration']=$vinfo['streams'][0]['duration'];
    return $rvinfo;
}
for($i=0; $i<$info_upload_count; $i++){
    //movieInfo検出
    if(file_exists($uploadfolder.$info_fid[$i].'.mp4')==0){
        error('ファイルが見つかりません。dir='.$uploadfolder.$info_fid[$i].'.mp4');
    }
    //print_r($info_fid);
    $movieInfo=get_movie_info($info_fid[$i]);
    //コーディック
    if($movieInfo['codec_name']!='h264'){
        error('コーディックがh264ではありません。アップロード処理を中止します。<br>uploadid='.$uploadid);
    }
    //解像度
    $t=0;
    foreach($cfg_qualityEncode as $k1=>$v1){
        if($movieInfo['width']==$v1['width'] && $movieInfo['height']==$v1['height']){
            $info_this[$i]['quality']=[$k1];
            $t=1;
        }
    }
    if($t==0){
        error('未対応の解像度です。アップロード処理を中止します。<br>'.$movieInfo['width'].'x'.$movieInfo['height'].'<br>uploadid='.$uploadid);
    }
    //再生時間
    $info_this[$i]['playtime']=intval($movieInfo['duration']);
    //アップロード時間
    $info_this[$i]['time']=$time;
}
//icon作成
for($i=0; $i<$info_upload_count; $i++){
    if(file_exists($uploadfolder.$info_fid[$i].'.mp4')){
        exec($cfg_ffmpeg.' -i "'.$uploadfolder.$info_fid[$i].'.mp4" -ss 6 -vframes 1 -f image2 -s 427x240 "'.$uploadfolder.$info_fid[$i].'.jpg"');
    }else{
        error('動画ファイルが存在しないため、アイコンを作成できません。');
    }
}
$info[$name2]=$info_this;
$info_count=count($info[$name2]);
//アップロード
//新規
db_checkError($db->query('INSERT INTO anime (name, time_basic, icon_id) VALUE("'.$name1.'", '.$time.', 0);'));
$naid=db_encvalue($db->query('SELECT aid FROM anime WHERE name="'.$name1.'";'));
//ファイル
mkdir($cfg_datadir.$naid);
$i=0;
foreach($info[$name2] as $con){
    //echo 'mv '.$uploadfolder.$info_fid[$i].'.mp4'.' '.$cfg_datadir.$naid.'/'.$con['id'].'-'.$con['quality'][0].'.mp4';
    exec('mv '.$uploadfolder.$info_fid[$i].'.mp4'.' '.$cfg_datadir.$naid.'/'.$con['id'].'-'.$con['quality'][0].'.mp4');
    exec('mv '.$uploadfolder.$info_fid[$i].'.jpg'.' '.$cfg_datadir.$naid.'/'.$con['id'].'.jpg');
    $i++;
}
//echo json_encode($info);

if(touch($cfg_datadir.$naid.'/'.'info.json')==0){
    error('info.jsonの作成に失敗しました。');
}
if(fileput($cfg_datadir.$naid.'/'.'info.json', json_encode($info))==0){
    error('info.jsonの書き込みに失敗しました。');
}
//html生成
$html='<div id="maincontent">
<div class="upload_center">
    <h2>アップロード</h2>
    <p>アップロードファイル選択>>アップロード中>>必要情報入力>>データ処理中>><strong>アップロード完了</strong></p>
    <br><p class="upload_mainMessage">アップロード完了</p>
</div>
</div>';
//出力
include 'comhtml.php';
comhtml('アップロード', $html, 'style-upload.css');
?>