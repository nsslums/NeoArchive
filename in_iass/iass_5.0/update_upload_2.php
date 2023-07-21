<?php
ini_set('display_errors', "On");
//設定ファイル読み込み
include 'config.php';
//エラー
include 'error.php';
//認証
include $cfg_session;
$uid=session('iass');
//file
include 'file.php';
//db接続
include 'db.php';
//ソースファイル
$source_dir='';

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
if(isset($_GET['aid'])){
    //iassアップロード
    if($_GET['aid']){
        $aid=$_GET['aid'];
        $olddata_dir='';
        include 'update_db.php';
        $name1=udb_encvalue($udb->query('SELECT name1 FROM animeTable WHERE id='.$aid.';'));
        $list=$udb->query('SELECT * FROM a'.$aid.'Table;');
        $i=0;
        foreach($list as $movie){
                //uploadInfo
                $uploadInfo[$i]['fid']=$i;
                $uploadInfo[$i]['dir_c']=$movie['id'];
                $uploadInfo[$i]['number']=$movie['math'];
                $uploadInfo[$i]['subtitle']=$movie['name2'].$movie['subtitle'];
                $uploadInfo[$i]['from']=$movie['dataFrom'];
            $i++;
        }
    }else{
        error('aidが読み込めません。');
    }
    $fileNumber=$i;
    //順番入れ替え
    /*
    for($i=0; $i<$fileNumber; $i++){
        for($j=$i+1; $j<$fileNumber; $j++){
            if($uploadInfo[$i]['number']>$uploadInfo[$j]['number']){
                $tmp=$uploadInfo[$i];
                $uploadInfo[$i]=$uploadInfo[$j];
                $uploadInfo[$j]=$tmp;
            }
        }
    }*/
    $message=null;
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
<form action="update_upload_3.php?aid='.$aid.'" method="POST" accept-charset="UTF-8" class="upload_form"  onsubmit="uploadStart()">
    <p>'.$message.'</p>
    <p>アニメ名1(例:とある科学の超電磁砲)</p>
    <input type="text" name="IA_name1" maxlength="64" value="'.$name1.'" required><br>
    <p>アニメ名2(例:S)</p>
    <input type="text" name="IA_name2" maxlength="64" value="データ移行中" required><br>
    <table border="1">
    <tr>
        <th class="upload_th1">ファイル名</th>
        <th class="upload_th3">話数(数値)</th>
        <th class="upload_th4">サブタイトル</th>
        <th class="upload_th5">ダウンロード元</th>
    </tr>';
for($i=0; $i<$fileNumber; $i++){
    $html.='<tr>
        <td>
            '.$uploadInfo[$i]['dir_c'].'
            <input type="hidden" name="IA_info_fid_'.$i.'" value="'.$uploadInfo[$i]['dir_c'].'">
        </td>
        <td>
            <input type="number" name="IA_info_number_'.$i.'" value="'.$uploadInfo[$i]['number'].'" maxlength="5" required class="upload_number">
        </td>
        <td>
            <input type="text" name="IA_info_subtitle_'.$i.'" value="'.$uploadInfo[$i]['subtitle'].'" maxlength="128" placeholder="入力..." required class="upload_subtitle">
        </td>
        <td>
            <input type="text" name="IA_info_from_'.$i.'" value="'.$uploadInfo[$i]['from'].'" maxlength="64" placeholder="入力..." required class="upload_downloadfrom">
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