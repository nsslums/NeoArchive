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

//追加
if(isset($_POST['IA_onair_name'])){
    if(isset($_POST['IA_onair_kind'])){
        //取得
        $name=htmlspecialchars($_POST['IA_onair_name'], ENT_QUOTES);
        $kind=htmlspecialchars($_POST['IA_onair_kind'], ENT_QUOTES);
        //追加
        db_checkError($db->query('INSERT INTO request (name, status, time) VALUE ("'.$name.'", 0, '.$time.')'));
    }
}
//変更
if(isset($_POST['IA_status_count'])){
    //$_POST
    $status_count=htmlspecialchars($_POST['IA_status_count'], ENT_QUOTES);
    //データ取得
    $request=$db->query('SELECT * FROM request ORDER BY rid DESC;');
    db_checkError($request);
    //$_POST
    foreach($request as $content){
        if(isset($_POST['IA_status_'.$content['rid']])){
            //データ取得
            $status=htmlspecialchars($_POST['IA_status_'.$content['rid']], ENT_QUOTES);
            //更新・削除
            if($status!=$content['status']){
                if($status==3){
                    //削除
                    db_checkError($db->query('DELETE FROM request WHERE rid='.$content['rid'].';'));
                }else{
                    //更新
                    db_checkError($db->query('UPDATE request SET status='.$status.' WHERE rid='.$content['rid'].';'));
                }
            }
        }
    }
}
//データ取得
$request=$db->query('SELECT * FROM request ORDER BY rid DESC;');
db_checkError($request);
//html変成
$html='<div id="maincontent">
<h2>リクエスト</h2>
<p>見たいアニメを追加。<br>例:「とある科学の超電磁砲T」のように正確に記述。</p>
<br>
<form action="request.php" method="POST" accept-charset="UTF-8">
    <input type="text" name="IA_onair_name" placeholder="アニメ名..." required>
    <br><select name="IA_onair_kind">
        <option value="recoding">放送予定</option>
        <option value="download">放送終了</option>
    </select>
    <br><input type="submit" value="追加">
</form>
<form action="request.php" method="POST" accept-charset="UTF-8">
    <table id="onair-list">
        <tr>
            <th>登録　</th><th>アニメ</th><th>状態</th>
        </tr>';
$i=0;
foreach($request as $content){
    //html変成
    $html.='<tr>
        <td>'.time_yutb($content['time']).'　</td>
        <td>'.$content['name'].'　</td>
        <td><select name="IA_status_'.$content['rid'].'">';
    //status
    $status_count=count($cfg_request_status);
    for($j=0; $j<$status_count; $j++){
        if($content['status']==$j){
            $selected=' selected';
        }else{
            $selected='';
        }
        $html.='<option value="'.$j.'" '.$selected.'>'.$cfg_request_status[$j].'</option>';
    }
    //html変成
    $html.='</select>
        </td>
    </tr>';
    $i++;
}
//html変成
$html.='</table>
    <input type="hidden" name="IA_status_count" value="'.$i.'">
    <input type="submit" value="変更">
</form>
</div>';
include 'comhtml.php';
comhtml('リクエスト', $html);
?>