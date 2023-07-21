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

//ランダム関数
function il_rand($d){
    $data=null;
    for($i=0; $i<$d; $i++){
        $data.=rand(0, 9);
    }
    return $data;
}
//話数検出
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
function numberput($text){
    //拡張子除去
    $text=mb_substr($text, 0, -4);
    //配列展開
    $text=str_split($text);
    //文字数カウント
    $moji=count($text);
    $number=null;
    $k=0;
    for($i=0; $i<$moji; $i++){
        if(mojicheck($text[$i])){
            $number.=$text[$i];
            if($i+1<$moji){
                if(mojicheck($text[$i+1])){
                    $number.=$text[$i+1];
                    if($i+2<$moji){
                        if(mojicheck($text[$i+2])){
                            $number.=$text[$i+2];
                            if($i+3<$moji){
                                if(mojicheck($text[$i+3])){
                                    $number.=$text[$i+3];
                                }
                            }
                        }
                    }
                }
            }
            $k=1;
            break;
        }
    }
    if($k===0){
        $number=0;
    }
    return $number;
}
if(isset($_GET['uploadid'])==0){
    //アップロードファイル・ID作成
    $uploadid=il_rand(16);
    $folderdir=$cfg_uploadtmp.$uploadid;
    if(file_exists($folderdir)==1){
        error('UploadIDが重複しています。');
    }
    mkdir($folderdir);
    //アップロード
    if(isset($_FILES['IA_file'])==0){
        error('$_POST[IA_file]が存在しません。');
    }
    $fileNumber=count($_FILES['IA_file']['tmp_name']);
    for($i=0; $i<$fileNumber; $i++){
        //dir宣言
        $fid=$fileNumber-$i-1;
        $filename_s=$fid.'.mp4';
        //アップロードdir読み込み
        if(isset($_FILES['IA_file']['name'][$i])==0 OR isset($_FILES['IA_file']['tmp_name'][$i])==0){
            error('$_FILESを読み込めません。');
        }
        $filename_c=$_FILES['IA_file']['name'][$i];
        $filename_t=$_FILES['IA_file']['tmp_name'][$i];
        //拡張子チェック
        if(mb_substr($filename_c, -3)!=='mp4'){
            error('拡張子が異常です。');
        }
        //アップロード
        if(move_uploaded_file($filename_t, $folderdir.'/'.$filename_s)===0){
            error('アップロードに失敗しました。');
        }
        //uploadInfo
        $uploadInfo[$i]['fid']=$fid;
        $uploadInfo[$i]['dir_c']=$filename_c;
        $uploadInfo[$i]['number']=numberput($filename_c);
    }
    //順番入れ替え
    for($i=0; $i<$fileNumber; $i++){
        for($j=$i+1; $j<$fileNumber; $j++){
            if($uploadInfo[$i]['number']>$uploadInfo[$j]['number']){
                $tmp=$uploadInfo[$i]['number'];
                $uploadInfo[$i]['number']=$uploadInfo[$j]['number'];
                $uploadInfo[$j]['number']=$tmp;
                $tmp=$uploadInfo[$i]['dir_c'];
                $uploadInfo[$i]['dir_c']=$uploadInfo[$j]['dir_c'];
                $uploadInfo[$j]['dir_c']=$tmp;
                $tmp=$uploadInfo[$i]['fid'];
                $uploadInfo[$i]['fid']=$uploadInfo[$j]['fid'];
                $uploadInfo[$j]['fid']=$tmp;
            }
        }
    }
    //Info出力
    $dirInfo=$folderdir.'/info.json';
    if(touch($dirInfo)===0){
        error('一時ファイルの作成に失敗しました。');
    }
    if(fileput($dirInfo, json_encode($uploadInfo))===0){
        error('一時ファイルの書き込みに失敗しました。');
    }
    $message=null;
}else{
    $uploadid=htmlspecialchars($_GET['uploadid']);
    $message=htmlspecialchars($_GET['mes']);
    //dir
    $dirInfo=$cfg_uploadtmp.$uploadid.'/info.json';
    $uploadInfo=fileget($dirInfo);
    if($uploadInfo===0){
        error('uploadinfoが読み込めませんでした。');
    }
    $uploadInfo=json_decode($uploadInfo, true);
    $fileNumber=count($uploadInfo);
}
//追加or新規
if(isset($_GET['aid'])){
    //グローバル変数
    $aid=htmlspecialchars($_GET['aid'], ENT_QUOTES);
    $add='&aid='.$aid;
    //name1
    $name1=db_encvalue($db->query('SELECT name FROM anime WHERE aid='.$aid.';'));
    $name1='value="'.$name1.'" ';
}else{
    $add=null;
    $name1=null;
}
//html生成
include 'upload_javascript.php';
$html='<div id="maincontent">
<div class="upload_center">
    <h2>アップロード</h2>
</div>
<div id="upload">
    <div class="upload_center">
        <p>アップロードファイル選択>>アップロード中>><strong>必要情報入力</strong>>>データ処理中>>アップロード完了</p>
    </div>
<form action="upload_3.php?uploadid='.$uploadid.$add.'" method="POST" accept-charset="UTF-8" class="upload_form"  onsubmit="uploadStart()">
    <p>'.$message.'</p>
    <p>アニメ名1(例:とある科学の超電磁砲)</p>
    <input type="text" name="IA_name1" maxlength="64" '.$name1.'required><br>
    <p>アニメ名2(例:S)</p>
    <input type="text" name="IA_name2" maxlength="64" required><br>
    <table border="1">
    <tr>
        <th class="upload_th1">ファイル名</th>
        <th class="upload_th3">話数(数値)</th>
        <th class="upload_th4">サブタイトル</th>
        <th class="upload_th5">ダウンロード元</th>
    </tr>';
for($i=0; $i<$fileNumber; $i++){
    $html.='<tr>
        <td>'.$uploadInfo[$i]['dir_c'].'</td>
        <td>
            <input type="number" name="IA_info_number_'.$uploadInfo[$i]['fid'].'" value="'.$uploadInfo[$i]['number'].'" maxlength="5" required class="upload_number">
        </td>
        <td>
            <input type="text" name="IA_info_subtitle_'.$uploadInfo[$i]['fid'].'" maxlength="128" placeholder="入力..." required class="upload_subtitle">
        </td>
        <td>
            <input type="text" name="IA_info_from_'.$uploadInfo[$i]['fid'].'" maxlength="64" placeholder="入力..." required class="upload_downloadfrom">
        </td>
    </tr>';
}
$html.='</table><br>
<input type="submit" value="確定">
</form>
</div>
<div id="uploading">
    <div class="upload_center">
        <p>アップロードファイル選択>>アップロード中>>必要情報入力>><strong>データ処理中</strong>>>アップロード完了</p>
    </div>
    <img src="icon/uploading.gif">
</div>'.$script.'</div>';

include 'comhtml.php';
comhtml('アップロード', $html, 'style-upload.css');
?>