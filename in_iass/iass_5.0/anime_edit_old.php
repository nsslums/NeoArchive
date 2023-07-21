<?php
ini_set('display_errors', "On");
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

//aid取得
if(isset($_GET['aid'])){
    $aid=htmlspecialchars($_GET['aid'], ENT_QUOTES);
}else{
    error('$_GET["aid"]がありません。');
}
//info読み込み
$infoDir=$cfg_datadir.$aid.'/info.json';
$info=fileget($infoDir);
if($info===0){
    error('info.jsonを読み込めません。');
}
$info=json_decode($info, true);
$name1=db_encvalue($db->query('SELECT name FROM anime WHERE aid='.$aid.';'));
$icon=db_encvalue($db->query('SELECT icon_id FROM anime WHERE aid='.$aid.';'));
$mes='';
//編集
if(isset($_GET['edit'])){
    //宣言
    $time=time();
    //削除
    if(isset($_POST['IA_delete'])){
        $delete=$_POST['IA_delete'];
        if($delete==1){
            db_checkError($db->query('DELETE FROM anime WHERE aid='.$aid.';'));
            exec('mv "'.$cfg_datadir.$aid.'" "'.$cfg_dustbox.$aid.'_'.$time.'"');
            //出力
            $html='<div id="maincontent"><h2>編集</h2><p>削除しました。</p><a href="index.php">トップに戻る</a></div>';
            include 'comhtml.php';
            comhtml('編集', $html, 'style-upload.css');
            //終了
            exit();
        }
    }
    //再入力関数
    function reInput($mes){
        //変数宣言
        global $aid;
        //出力
        $html='<div id="maincontent"><h2>編集</h2><p>'.$mes.'</p><a href="?aid='.$aid.'">編集画面に戻る</a></div>';
        include 'comhtml.php';
        comhtml('編集', $html, 'style-upload.css');
        //終了
        exit();
    }
    //name1
    if(isset($_POST['IA_name1'])){
        $name1=htmlspecialchars($_POST['IA_name1'], ENT_QUOTES);
    }else{
        error('$_POSTデータが取得できません。IA_name1');
    }
    //name2_add
    $name2_add=null;
    if(isset($_POST['IA_add_name2_enable'])){
        if($_POST['IA_add_name2_enable']==1){
            //追加
            if(isset($_POST['IA_add_name2'])){
                $name2_add=htmlspecialchars($_POST['IA_add_name2'], ENT_QUOTES);
            }
        }
    }
    //icon
    if(isset($_POST['IA_icon'])){
        $icon=htmlspecialchars($_POST['IA_icon'], ENT_QUOTES);
    }
    //group2
    $i=0;
    foreach($info as $cool_key=>$cool){
        //name2
        if(isset($_POST['IA_name2_'.$cool_key])){
            $name2=htmlspecialchars($_POST['IA_name2_'.$cool_key], ENT_QUOTES);
            $info_key[$i]=$name2;
        }else{
            error('$_POSTデータが取得できません。IA_name2'.$cool_key);
        }
        //delete
        if(isset($_POST['IA_cool_delete_'.$cool_key])){
            $info_delete[$i]=htmlspecialchars($_POST['IA_cool_delete_'.$cool_key], ENT_QUOTES);
        }else{
            error('$_POSTデータが取得できません。IA_cool_delete');
        }
        //表示順
        if(isset($_POST['IA_cool_display_'.$cool_key])){
            $info_display[$i]=htmlspecialchars($_POST['IA_cool_display_'.$cool_key], ENT_QUOTES);
        }else{
            error('$_POSTデータが取得できません。_IA_cool_display');
        }
        $i++;
    }
    //配列チェック
    $info_count=count($info_key);
    //name2重複確認
    $t=0;
    for($i=0; $i<$info_count; $i++){
        for($j=$i+1; $j<$info_count; $j++){
            if($info_key[$i]==$info_key[$j]){
                $t=1;
            }
        }
    }
    if($t==1){
        reInput('アニメ名2が重複しています。');
    }
    //表示順
    $t=0;
    for($i=0; $i<$info_count; $i++){
        for($j=$i+1; $j<$info_count; $j++){
            if($info_display[$i]==$info_display[$j]){
                $t=1;
            }
        }
    }
    if($t==1){
        reInput('表示順が重複しています。');
    }
    for($i=0; $i<$info_count; $i++){
        if($info_display[$i]==0){
            reInput('表示順を選択してください。');
        }else{
            $info_display[$i]--;
        }
    }
    //コンテンツ削除関数
    function delete_files($aid, $id, $qualitys){
        //宣言
        global $cfg_datadir, $cfg_dustbox, $time;
        //フォルダ
        if(file_exists($cfg_dustbox.'/'.$aid.'_'.$time)==0){
            mkdir($cfg_dustbox.'/'.$aid.'_'.$time);
        }
        //ファイル
        foreach($qualitys as $quality){
            exec('mv '.$cfg_datadir.$aid.'/'.$id.'-'.$quality.'.mp4 '.$cfg_dustbox.'/'.$aid.'_'.$time.'/'.$id.'-'.$quality.'.mp4');
        }
        exec('mv '.$cfg_datadir.$aid.'/'.$id.'.jpg '.$cfg_dustbox.'/'.$aid.'_'.$time.'/'.$id.'.jpg');
    }
    //group2
    $i=0;
    $group2_count=array_fill(0, $info_count, 0);
    foreach($info as $cool_key=>$cool){
        //削除
        //info
        if($cool!=null){
            $cool_count=count($cool);
        }else{
            continue;
        }
        $content_delete_count=0;
        for($j=0; $j<$cool_count; $j++){
            //削除
            if(isset($_POST['IA_content_'.$cool[$j]['id']])){
                $content_delete=htmlspecialchars($_POST['IA_content_'.$cool[$j]['id']], ENT_QUOTES);
                if($content_delete==1){
                    //ファイル
                    delete_files($aid, $cool[$j]['id'], $cool[$j]['quality']);
                    //データ取得なし
                    continue;
                }
            }
            //移動
            if(isset($_POST['IA_move_'.$cool[$j]['id']])){
                //取得
                $group2_keyid=htmlspecialchars($_POST['IA_move_'.$cool[$j]['id']], ENT_QUOTES)-1;
                //チェック
                if($group2_keyid<0 OR $info_count<=$group2_keyid){
                    error('$group2_keyidが範囲外です。');
                }
            }else{
                error('$_POSTデータが取得できません。2'.$j);
            }
            //既存データコピー
            $info_new[$info_key[$group2_keyid]][$group2_count[$group2_keyid]]=$cool[$j];
            //話数
            if(isset($_POST['IA_number_'.$cool[$j]['id']])){
                $info_new[$info_key[$group2_keyid]][$group2_count[$group2_keyid]]['number']=htmlspecialchars($_POST['IA_number_'.$cool[$j]['id']], ENT_QUOTES);
            }else{
                error('$_POSTデータが取得できません。2'.$j);
            }
            //サブタイトル
            if(isset($_POST['IA_subtitle_'.$cool[$j]['id']])){
                $info_new[$info_key[$group2_keyid]][$group2_count[$group2_keyid]]['subtitle']=htmlspecialchars($_POST['IA_subtitle_'.$cool[$j]['id']], ENT_QUOTES);
            }else{
                error('$_POSTデータが取得できません。3'.$j);
            }
            //ダウンロード元
            if(isset($_POST['IA_from_'.$cool[$j]['id']])){
                $info_new[$info_key[$group2_keyid]][$group2_count[$group2_keyid]]['from']=htmlspecialchars($_POST['IA_from_'.$cool[$j]['id']], ENT_QUOTES);
            }else{
                error('$_POSTデータが取得できません。4'.$j);
            }
            $group2_count[$group2_keyid]++;
        }
        $i++;
    }
    if($name2_add==null){
        for($i=0; $i<$info_count; $i++){
            //重複確認
            $t=0;
            for($j=0; $j<$group2_count[$i]; $j++){
                for($k=$j+1; $k<$group2_count[$i]; $k++){
                    if($info_new[$info_key[$i]][$j]['number']==$info_new[$info_key[$i]][$k]['number']){
                        $t=1;
                    }
                }
            }
            if($t==1){
                reInput('話数が重複しています。');
            }
            //並べ替え
            for($j=0; $j<$group2_count[$i]; $j++){
                for($k=$j; $k<$group2_count[$i]; $k++){
                    if($info_new[$info_key[$i]][$j]['number']>$info_new[$info_key[$i]][$k]['number']){
                        /*$tmp=$info_new[$info_key[$i]][$j]['number'];
                        $info_new[$info_key[$i]][$j]['number']=$info_new[$info_key[$i]][$k]['number'];
                        $info_new[$info_key[$i]][$k]['number']=$tmp;*/
                        $tmp=$info_new[$info_key[$i]][$j];
                        $info_new[$info_key[$i]][$j]=$info_new[$info_key[$i]][$k];
                        $info_new[$info_key[$i]][$k]=$tmp;
                    }
                }
            }
        }
    }
    //並べ替え
    $info_enc=array();
    for($i=0; $i<$info_count; $i++){
        for($j=0; $j<$info_count; $j++){
            if($i==$info_display[$j]){
                //削除
                if($info_delete[$j]==0){
                    //並べ替え
                    if(isset($info_new[$info_key[$j]])){
                        $info_enc[$info_key[$j]]=$info_new[$info_key[$j]];
                    }else{
                        $info_enc[$info_key[$j]]=null;
                    }
                }else{
                    //ファイルなし
                    if(isset($info_new[$info_key[$j]])==0){
                        continue;
                    }
                    //削除
                    foreach($info_new[$info_key[$j]] as $cool){
                        //ファイル
                        delete_files($aid, $cool['id'], $cool['quality']);
                    }
                }
            }
        }
    }
    //name2_add
    if($name2_add!=null){
        $info_enc[$name2_add]=null;
    }
    //更新
    $info=$info_enc;
    if(fileput($infoDir, json_encode($info))==0){
        error('info.jsonの書き込みに失敗しました。');
    }
    db_checkError($db->query('UPDATE anime SET name="'.$name1.'" WHERE aid='.$aid.';'));
    db_checkError($db->query('UPDATE anime SET icon_id='.$icon.' WHERE aid='.$aid.';'));
}
//html変成
$html='<div id="maincontent">
<h2>編集</h2>
<form action="anime_edit.php?aid='.$aid.'&edit" method="POST" accept-charset="UTF-8" class="upload_form">
    <p>アニメ名1</p>
    <input type="text" value="'.$name1.'" name="IA_name1" maxlength="64" required><br>
    <select name="IA_delete">
        <option value="0">削除(未選択)</option>
        <option value="1">削除(実行)</option>
    </select><br>';
//表示順変更
$info_count=count($info);
$info_keys=array_keys($info);
//内容
$i=0;
foreach($info as $cool_key=>$cool){
    //group2
    $cool_display='';
    $cool_move='';
    for($j=1; $j<=$info_count; $j++){
        if($j==$i+1){
            $selected=' selected';
            $selected_display='(変更無)';
        }else{
            $selected='';
            $selected_display='';
        }
        //表示順
        $cool_display.='<option value="'.$j.'"'.$selected.'>表示順:'.$j.'</option>';
        //移動
        $cool_move.='<option value="'.$j.'"'.$selected.'>'.$info_keys[$j-1].$selected_display.'</option>';
    }
    //group2
    $html.='<p>アニメ名2</p>
    <input type="text" name="IA_name2_'.$cool_key.'" value="'.$cool_key.'" maxlength="64" required><br>
    <select name="IA_cool_display_'.$cool_key.'">
        '.$cool_display.'
    </select>
    <select name="IA_cool_delete_'.$cool_key.'">
        <option value="0">削除(未選択)</option>
        <option value="1">削除(実行)</option>
    </select><br>';
    if($cool!=null){
        $cool_count=count($cool);
        $html.='<table border="1">
            <tr>
                <th class="upload_th1">ファイル名</th>
                <th class="upload_th1">アイコン変更</th>
                <th class="upload_th2">削除</th>
                <th class="upload_th2">移動</th>
                <th class="upload_th3">話数(数値)</th>
                <th class="upload_th4">サブタイトル</th>
                <th class="upload_th5">ダウンロード元</th>
            </tr>';
        for($j=0; $j<$cool_count; $j++){
            if($icon==$cool[$j]['id']){
                $checked='checked';
            }else{
                $checked='';
            }
            $html.='
            <tr>
                <td>'.$cool[$j]['id'].'.mp4</td>
                <td>
                    <input type="radio" name="IA_icon" value="'.$cool[$j]['id'].'"'.$checked.'>
                </td>
                <td class="upload_td2">
                    <input type="checkbox" name="IA_content_'.$cool[$j]['id'].'" value="1">
                </td>
                <td>
                    <select name="IA_move_'.$cool[$j]['id'].'">
                        '.$cool_move.'
                    </select>
                </td>
                <td>
                    <input type="number" required step="0.1" name="IA_number_'.$cool[$j]['id'].'" value="'.$cool[$j]['number'].'" class="upload_number" maxlength="5">
                </td>
                <td>
                    <input type="text" required name="IA_subtitle_'.$cool[$j]['id'].'" value="'.$cool[$j]['subtitle'].'" class="upload_subtitle" maxlength="128">
                </td>
                <td>
                    <input type="text" name="IA_from_'.$cool[$j]['id'].'" value="'.$cool[$j]['from'].'" required class="upload_downloadfrom" maxlength="64">
                </td>
            </tr>';
        }
        $html.='</table><br>';
    }else{
        $html.='<p>コンテンツが見つかりません。</p>';
    }
    $i++;
}
$html.='<br>アニメ名2を追加<input type="checkbox" name="IA_add_name2_enable" value="1">
<br><input type="text" name="IA_add_name2" placeholder="アニメ名2">';
$html.='<br><input type="submit" value="変更">
</form>
</div>';
include 'comhtml.php';
comhtml('編集', $html, 'style-upload.css');
?>