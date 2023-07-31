<?php
//target処理
if(isset($_GET['target'])){
    $target=$_GET['target'];
}else{
    //targetなし
    header('http', true, 403);
    exit();
}
//入力判定
if(isset($_POST['iln_id'])==0 OR isset($_POST['iln_password'])==0){
    //入力画面出力
    input(0);
}

//画面出力
//v1=0 入力, v1=1 再入力
function input($status){
    //宣言
    global $target;
    $comment=null;
    //再入力
    if($status==1){
        $comment.='<p class="warning">IDとPasswordが違う可能性があります。<br>同じIDで複数回パスワードを間違って入力した場合<br>アカウントがロックされます。</p>';
        sleep(1);
    }
    //出力
    echo '<!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <style>@import url("style.css");</style>
        <title>ILNログイン</title>
    </head>
    <body>
        <div id="content">
            <h1>ILN Login</h1>
            <form action="index.php?target='.$target.'" method="POST" accept-charset="UTF-8">
                <input type="text" name="iln_id" maxlength="32" required placeholder="ID..."><br>
                <input type="password" name="iln_password" maxlength="72" required placeholder="Password..."><br>
                <input type="submit" value="Login">
            </form>
            '.$comment.'
        </div>
    </body>
    </html>';
    exit();
}

//DB接続
include 'authError.php';
include 'authDb.php';

//データ取得
$uid=htmlspecialchars($_POST['iln_id'], ENT_QUOTES);
$pw=htmlspecialchars($_POST['iln_password'], ENT_QUOTES);

//idのbyte数を確認
if(strlen($uid)>32){
    input(1);
}
//id検索
$v=$authDb->query('SELECT count(uid) FROM account WHERE uid="'.$uid.'";');
if(authDb_encvalue($v)==0){
    //一致IDなし
    input(1);
}

//bat_login pwd取得
$account=authDb_encrecord($authDb->query('SELECT batlogin, pwd FROM account WHERE uid="'.$uid.'";'));

//同一ID、パスワード間違え数判定
if($account['batlogin']>5){
    //IDは使えません
    input(1);
}

//password判定
if(password_verify($pw, $account['pwd'])==0){
    //password不一致
    $account['batlogin']++;
    $authDb->query('UPDATE account SET batlogin='.$account['batlogin'].' WHERE uid="'.$uid.'";');
    input(1);
}

//batloginの初期化
$v=$authDb->query('UPDATE account SET batlogin=0 WHERE uid="'.$uid.'";');
authDb_checkError($v);

//端末
function il_rand($d){
    $data=null;
    for($i=0; $i<$d; $i++){
        $data.=rand(0, 9);
    }
    return $data;
}
$time=time();

//更新
$id=authDb_encvalue($authDb->query('SELECT id FROM account WHERE uid="'.$uid.'";'));
$terminaltime=$time;
$sessionkey=il_rand(64);
$sessiontime=$time;
$hua=$_SERVER['HTTP_USER_AGENT'];

if(isset($_COOKIE['terminalkey'])==1){
    //既存端末
    $terminalkey=htmlspecialchars($_COOKIE['terminalkey'], ENT_QUOTES);
    //keyなし
    if(authDb_encvalue($authDb->query('SELECT count(terminalkey) FROM terminal WHERE terminalkey="'.$terminalkey.'";'))==0){
        //terminalkey削除
        //DBリセット時など
        /*
        setcookie('terminalkey', '', -3600, $cookiedomain);
        setcookie('sessionkey', '', -3600, $cookiedomain);
        input(1);
        */
        goto newTerminal;
        error_log('cookieにterminalkeyが存在しますがdatabaseに存在しないため、新規端末として扱います。');
        exit();
    }
    //UPDATE
    $v=$authDb->query('UPDATE terminal SET id='.$id.', terminaltime='.$terminaltime.', sessionkey="'.$sessionkey.'", sessiontime='.$sessiontime.', hua="'.$hua.'" WHERE terminalkey="'.$terminalkey.'";');
    authDb_checkError($v);
}else{
    newTerminal:
    //新規端末
    $terminalkey=il_rand(16);
    //使用中のkey
    if(authDb_encvalue($authDb->query('SELECT count(terminalkey) FROM terminal WHERE terminalkey="'.$terminalkey.'";'))==1){
        input(1);
    }
    //INSERT
    $v=$authDb->query('INSERT INTO terminal (id, terminalkey, terminaltime, sessionkey, sessiontime, hua) VALUE('.$id.', "'.$terminalkey.'", '.$terminaltime.', "'.$sessionkey.'", '.$sessiontime.', "'.$hua.'");');
    authDb_checkError($v);
}

include 'config.php';

//cookie登録
setcookie('terminalkey', $terminalkey, $time+15552000, '/', $cookiedomain);
setcookie('sessionkey', $sessionkey, 0, '/', $cookiedomain);

//画面移動
for($i=0; $i<count($config_service); $i++){
    if($config_service[$i]==$target){
        header('Location: '.$config_serviceUrl[$i]);
        exit();
    }
}
header('http', true, 403);
?>