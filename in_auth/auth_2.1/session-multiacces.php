<?php
function session_multiacces($service){
    //初期設定
    $dir_config='/var/www/in_auth/auth_2.1/config.php';//config.phpのディレクトリ
    $dir_authError='/var/www/in_auth/auth_2.1/authError.php';//proceError.phpディレクトリ
    $dir_authDb='/var/www/in_auth/auth_2.1/authDb.php';//db.phpのディレクトリ
    //設定読み込み
    include $dir_config;
    //エラー
    function session_error(){
        header('http', true, 403);
        exit();
    }
    //cookie確認
    if(isset($_COOKIE['sessionkey'])==0 OR isset($_COOKIE['terminalkey'])==0){
        session_error();
    }
    $sessionkey=htmlspecialchars($_COOKIE['sessionkey'], ENT_QUOTES);
    $terminalkey=htmlspecialchars($_COOKIE['terminalkey'], ENT_QUOTES);
    //DB接続
    if($service=='admin'){
        global $authDb;
    }else{
        include $dir_authError;
        include $dir_authDb;
    }
    //DBセッション検索
    if(authDb_encvalue($authDb->query('SELECT count(terminalkey) FROM terminal WHERE terminalkey='.$terminalkey.';'))==0){
        session_error();
    }
    $terminal=authDb_encrecord($authDb->query('SELECT * FROM terminal WHERE terminalkey='.$terminalkey.';'));
    //セッションキー確認
    if($terminal['sessionkey']!=$sessionkey){
        //sessionkey不一致
        session_error();
    }
    //セッション有効期限確認
    $time=time();
    if($terminal['sessiontime']+10800<$time){
        //セッション期限切れ
        session_error();
    }
    //サービス利用資格確認
    $st=0;
    foreach($config_service as $serviceName){
        if($serviceName==$service){
            $st=1;
            if(authDb_encvalue($authDb->query('SELECT '.$service.' FROM account WHERE id='.$terminal['id'].';'))==0){
                session_error();
            }
        }
    }
    if($st==0){
        session_error();
    }
    //終了
    return $terminal['id'];
}
?>
