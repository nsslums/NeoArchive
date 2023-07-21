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

//時間
$time=time();

//uploadid
if(isset($_GET['uploadid'])){
    $uploadid=htmlspecialchars($_GET['uploadid'], ENT_QUOTES);
    $dir=$cfg_uploadtmp.$uploadid.'/info.json';
    $info_upload=fileget($dir);
    if($info_upload===0){
        error($uploadid.'/info.jsonを読み込めませんでした。');
    }
    $info_upload=json_decode($info_upload, true);
}else{
    error('?uploadidが見つかりません。');
}
//追加のみ
if(isset($_GET['aid'])){
    $aid=htmlspecialchars($_GET['aid'], ENT_QUOTES);
    $dir=$cfg_datadir.$aid.'/info.json';
    $info=fileget($dir);
    if($info===0){
        error('info.jsonを読み込めませんでした。');
    }
    $info=json_decode($info, true);
}else{
    $aid=0;
}
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
//name1重複チェック
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
}
//動画情報取得
$info_upload_count=count($info_upload);
for($i=0; $i<$info_upload_count; $i++){
    if(isset($_POST['IA_info_number_'.$i])===0){
        error('htmlが異常です。');
    }
    if(isset($_POST['IA_info_subtitle_'.$i])===0){
        error('htmlが異常です。');
    }
    if(isset($_POST['IA_info_from_'.$i])===0){
        error('htmlが異常です。');
    }
    $info_this[$i]['number']=htmlspecialchars($_POST['IA_info_number_'.$i], ENT_QUOTES);
    $info_this[$i]['subtitle']=htmlspecialchars($_POST['IA_info_subtitle_'.$i], ENT_QUOTES);
    $info_this[$i]['from']=htmlspecialchars($_POST['IA_info_from_'.$i], ENT_QUOTES);
    $info_this[$i]['id']=$i;
}
//話数数値チェック
$mojilist=['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'];
function mojicheck($text){
    global $mojilist;
    for($j=0; $j<11; $j++){
        if($text===$mojilist[$j]){
            return 1;
        }
    }
    return 0;
}
for($i=0; $i<$info_upload_count; $i++){
    $text=str_split($info_this[$i]['number']);
    foreach($text as $v1){
        if(mojicheck($v1)===0){
            error('htmlが異常です。');
        }
    }
}
//dir宣言
$uploadfolder=$cfg_uploadtmp.$uploadid.'/';
//動画品質評価
function get_movie_info($fid){
    //宣言
    global $cfg_ffprobe, $uploadfolder;
    $dirMovie=$uploadfolder.$fid.'.mp4';
    $dirInfo=$uploadfolder.'movie_info_tmp.json';
    //ファイル存在確認
    if(file_exists($dirMovie)===0){
        error('アップロードしたファイルがありません。');
    }
    exec($cfg_ffprobe.' -i "'.$dirMovie.'" -show_streams -of json > "'.$dirInfo.'"');
    $vinfo=fileget($dirInfo);
    //ファイル読み込み
    if($vinfo===0){
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
    $movieInfo=get_movie_info($info_this[$i]['id']);
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
        error('未対応の解像度です。アップロード処理を中止します。<br>uploadid='.$uploadid);
    }
    //再生時間
    $info_this[$i]['playtime']=intval($movieInfo['duration']);
    //アップロード時間
    $info_this[$i]['time']=$time;
}
//icon作成
for($i=0; $i<$info_upload_count; $i++){
    if(file_exists($uploadfolder.$i.'.mp4')){
        exec($cfg_ffmpeg.' -i "'.$uploadfolder.$i.'.mp4" -ss 6 -vframes 1 -f image2 -s 427x240 "'.$uploadfolder.$i.'.jpg"');
    }else{
        error('動画ファイルが存在しないため、アイコンを作成できません。');
    }
}
//ID再割り当て
if($aid!=0){
    //ID再割り当て
    $obid=0;
    foreach($info as $v1){
        foreach($v1 as $v2){
            if($obid<$v2['id']){
                $obid=$v2['id'];
            }
        }
    }
    for($i=0; $i<$info_upload_count; $i++){
        $fid=$info_this[$i]['id']+$obid+1;
        //ファイル用ID保持
        $file_info_id[]=['old'=>$info_this[$i]['id'], 'new'=>$fid];
        //id変更
        $info_this[$i]['id']=$fid;
    }
}
//info結合
if($group2==true){
    //追加
    $info_count=count($info[$name2]);
    for($i=0; $i<$info_upload_count; $i++){
        $info[$name2][$i+$info_count]=$info_this[$i];
    }
}else{
    //新規
    $info[$name2]=$info_this;
}
$info_count=count($info[$name2]);
//話数重複チェック
for($i=0; $i<$info_count; $i++){
    if($i<$info_count-1){
        for($j=$i+1; $j<$info_count; $j++){
            if($info[$name2][$i]['number']==$info[$name2][$j]['number']){
                reInput('同じ話数が複数存在します。');
            }
        }
    }
}
//並べ替え
for($i=0; $i<$info_count; $i++){
    for($j=$i; $j<$info_count; $j++){
        if($info[$name2][$i]['number']>$info[$name2][$j]['number']){
            $tmp=$info[$name2][$i];
            $info[$name2][$i]=$info[$name2][$j];
            $info[$name2][$j]=$tmp;
        }
    }
}
//ファイル名変更
if($aid!=0){
    foreach($file_info_id as $file_id){
        //ファイル重複バグ修正
        exec('mv "'.$uploadfolder.$file_id['old'].'.mp4" "'.$uploadfolder.$file_id['old'].'.mvtmp.mp4"');
        exec('mv "'.$uploadfolder.$file_id['old'].'.jpg" "'.$uploadfolder.$file_id['old'].'.mvtmp.jpg"');
    }
    foreach($file_info_id as $file_id){
        //ファイル名変更
        exec('mv "'.$uploadfolder.$file_id['old'].'.mvtmp.mp4" "'.$uploadfolder.$file_id['new'].'.mp4"');
        exec('mv "'.$uploadfolder.$file_id['old'].'.mvtmp.jpg" "'.$uploadfolder.$file_id['new'].'.jpg"');
    }
}
//アップロード
if($aid==0){
    //新規
    db_checkError($db->query('INSERT INTO anime (name, time_basic, icon_id) VALUE("'.$name1.'", '.$time.', 0);'));
    $naid=db_encvalue($db->query('SELECT aid FROM anime WHERE name="'.$name1.'";'));
}else{
    //追加
    db_checkError($db->query('UPDATE anime SET time_basic='.$time.' WHERE aid='.$aid.';'));
    $naid=$aid;
}
//通知
include 'news.php';
newsWrite('追加', $name1.'がアップロードされました。', $time);
//dir宣言
$adir=$cfg_datadir.$naid.'/';
//新規>フォルダ作成
if($aid===0){
    mkdir($adir);
}
//ファイル移動
for($i=0; $i<$info_upload_count; $i++){
    exec('mv "'.$uploadfolder.$info_this[$i]['id'].'.mp4" "'.$adir.$info_this[$i]['id'].'-'.$info_this[$i]['quality'][0].'.mp4"');
    exec('mv "'.$uploadfolder.$info_this[$i]['id'].'.jpg" "'.$adir.$info_this[$i]['id'].'.jpg"');
}
if(touch($adir.'info.json')==0){
    error('info.jsonの作成に失敗しました。');
}
if(fileput($adir.'info.json', json_encode($info))==0){
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