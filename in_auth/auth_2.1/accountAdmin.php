<?php
//設定
include 'config.php';
//DB接続
include 'authError.php';
include 'authDb.php';
//認証
include 'session.php';
session('admin');
//時間表示
include 'time.php';

//画面出力
function input($status){
    if($status==0){
        //宣言
        global $config_service, $newUser, $html1;
        //表示サービス
        $html0=null;
        foreach($config_service as $serviceName){
            $html0.='<th>'.$serviceName.'</th>';
        }
        //ユーザー一覧
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>@import url("style.css"); @import url("style-adminAccount.css");</style>
            <title>ILNアカウント管理</title>
        </head>
        <body>
            <div id="a-content">
                <h1 id="a-h1">ILNアカウント管理</h1>
                <p>ユーザー一覧</p>
                <a href="accountAdmin.php">再読み込み</a> <a href="?createKey">新規アカウント作成</a>
                '.$newUser.'
                <table border="1">
                    <tr>
                        <th>ID</th>
                        <th>UID</th>
                        <th>PW間違え</th>
                        '.$html0.'
                        <th>最終ログイン</th>
                        <th>削除</th>
                    </tr>
                    '.$html1.'
                </table>
            </div>
        </body>
        </html>';
    }else{
        //宣言
        global $html1, $account_user;
        //端末一覧
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>@import url("style.css"); @import url("style-adminAccount.css");</style>
            <title>ILN端末管理</title>
        </head>
        <body>
            <div id="a-content">
                <h1 id="a-h1">ILNアカウント管理</h1>
                <p><a href="accountAdmin.php">ユーザー一覧</a>>'.$account_user['uid'].'の端末</p>
                <table border="1">
                    <tr>
                        <th>ログイン時間</th>
                        <th>セッション更新時間</th>
                        <th>User Agent</th>
                        <th>削除</th>
                    </tr>
                    '.$html1.'
                </table>
            </div>
        </body>
        </html>';
    }
    exit();
}

//招待キー
$newUser=null;
if(isset($_GET['createKey'])){
    //宣言
    global $config_url, $config_serviceUrl;
    //招待キー作成
    function il_rand($d){
        $data=null;
        for($i=0; $i<$d; $i++){
            $data.=rand(0, 9);
        }
        return $data;
    }
    $key=il_rand(64);
    //DB登録
    $v=$authDb->query('INSERT INTO newuser (nkey) VALUE("'.$key.'");');
    authDb_checkError($v);
    //URL作成
    foreach($config_service as $serviceUrl){
        $newUser.='<p>'.$config_url.'newaccount.php?key='.$key.'&target='.$serviceUrl.'</p>';
    }
}
//サービス有効化・無効化
if(isset($_GET['serviceSetStatus'])){
    //ID, Service取得
    $id=htmlspecialchars($_GET['serviceSetStatus'], ENT_QUOTES);
    $service=htmlspecialchars($_GET['service'], ENT_QUOTES);
    //DB現状確認
    $v=authDb_encvalue($authDb->query('SELECT '.$service.' FROM account WHERE id='.$id.';'));
    if($v==0){
        $v=1;
    }else{
        $v=0;
    }
    //反映
    $v=$authDb->query('UPDATE account SET '.$service.'='.$v.' WHERE id='.$id.';');
    authDb_checkError($v);
}
//PWリセット
if(isset($_GET['rstBatlogin'])){
    //ID取得
    $id=htmlspecialchars($_GET['rstBatlogin'], ENT_QUOTES);
    //DB反映
    $v=$authDb->query('UPDATE account SET batlogin=0 WHERE id='.$id.';');
    authDb_checkError($v);
}
//端末削除
if(isset($_GET['terminalDelete'])){
    //terminalkey取得
    $terminalkey=htmlspecialchars($_GET['terminalDelete'], ENT_QUOTES);
    //DB反映
    $v=$authDb->query('DELETE FROM terminal WHERE terminalkey='.$terminalkey.';');
    authDb_checkError($v);
}
//アカウント削除
if(isset($_GET['delid'])){
    $delid=htmlspecialchars($_GET['delid'], ENT_QUOTES);
    //terminal削除
    $v=$authDb->query('DELETE FROM terminal WHERE id=.'.$delid.';');
    authDb_checkError($v);
    //account削除
    $v=$authDb->query('DELETE FROM account WHERE id='.$delid.';');
    authDb_checkError($v);
}

//画面
if(isset($_GET['id'])){
    //user詳細
    $id=htmlspecialchars($_GET['id'], ENT_QUOTES);
    $terminal=$authDb->query('SELECT * FROM terminal WHERE id='.$id.';');
    authDb_checkError($terminal);
    $account_user=authDb_encrecord($authDb->query('SELECT * FROM account WHERE id='.$id.';'));
    //データ作成
    $html1=null;
    foreach($terminal as $terminal_user){
        $loginTime=time_yutb($terminal_user['terminaltime']);
        $sessionTime=time_yutb($terminal_user['sessiontime']);
        $html1.='<tr><td>'.$loginTime.'</td><td>'.$sessionTime.'</td><td>'.$terminal_user['hua'].'</td><td><a href="?terminalDelete='.$terminal_user['terminalkey'].'&id='.$id.'">削除</a></td></tr>';
    }
    //表示
    input(1);
}else{
    //account取得
    $account=$authDb->query('SELECT * FROM account;');
    authDb_checkError($account);
    //データ作成
    $html1=null;
    foreach($account as $user){
        //batlogin
        if($user['batlogin']>0){
            $batlogin=$user['batlogin'].'　<a href="?rstBatlogin='.$user['id'].'">(リセット)</a>';
        }else{
            $batlogin=$user['batlogin'];
        }
        //設定可能サービス
        $service=null;
        foreach($config_service as $serviceLoop){
            if($user[$serviceLoop]==0){
                $serviceStatus='無効';
                $serviceSetStatus='<a href="?serviceSetStatus='.$user['id'].'&service='.$serviceLoop.'">(有効化)</a>';
            }else{
                $serviceStatus='有効';
                $serviceSetStatus='<a href="?serviceSetStatus='.$user['id'].'&service='.$serviceLoop.'">(無効化)</a>';
            }
            $service.='<td>'.$serviceStatus.$serviceSetStatus.'</td>';
        }
        //端末情報
        $loginTime=authDb_encvalue($authDb->query('SELECT sessiontime FROM terminal WHERE id='.$user['id'].' ORDER BY sessiontime DESC LIMIT 1;'));
        if($loginTime==null){
            $loginTime='未ログイン';
        }else{
            $loginTime=time_yutb($loginTime);
        }
        //データ作成
        $html1.='<tr><td><a href="?id='.$user['id'].'">'.$user['id'].'</a></td><td>'.$user['uid'].'</td><td>'.$batlogin.'</td>'.$service.'<td>'.$loginTime.'</td><td><a href="?delid='.$user['id'].'">削除</a></td></tr>';
    }
    //表示
    input(0);
}
?>