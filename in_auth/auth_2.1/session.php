<?php
//global宣言
$sessionphp_url=null;
$sessionphp_service=null;
function session($service){
    //初期設定
    $dir_config='/var/www/in_auth/auth_2.1/config.php';//config.phpのディレクトリ
    $dir_authError='/var/www/in_auth/auth_2.1/authError.php';//proceError.phpディレクトリ
    $dir_authDb='/var/www/in_auth/auth_2.1/authDb.php';//db.phpのディレクトリ
    //設定読み込み
    include $dir_config;
    //リダイレクト
    global $sessionphp_url, $sessionphp_service;
    $sessionphp_url=$config_url;
    $sessionphp_service=$service;
    function redirect($e){
        //宣言
        global $sessionphp_url, $sessionphp_service;
        //リダイレクト・終了
        header('Location: '.$sessionphp_url.'index.php?target='.$sessionphp_service.'&errcode='.$e);
        exit();
    }
    //cookie確認
    if(isset($_COOKIE['sessionkey'])==0 OR isset($_COOKIE['terminalkey'])==0){
        redirect(1);
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
        redirect(2);
    }
    $terminal=authDb_encrecord($authDb->query('SELECT * FROM terminal WHERE terminalkey='.$terminalkey.';'));
    //セッションキー確認
    if($terminal['sessionkey']!=$sessionkey){
        //sessionkey不一致
        redirect(3);
    }
    //セッション有効期限確認
    $time=time();
    if($terminal['sessiontime']+10800<$time){
        //セッション期限切れ
        redirect(4);
    }
    //サービス利用資格確認
    $st=0;
    foreach($config_service as $serviceName){
        if($serviceName==$service){
            $st=1;
            if(authDb_encvalue($authDb->query('SELECT '.$service.' FROM account WHERE id='.$terminal['id'].';'))==0){
                redirect('5');
            }
        }
    }
    if($st==0){
        redirect(6);
    }
    //セッション更新
    function il_auth_rand($d){
        $data=null;
        for($i=0; $i<$d; $i++){
            $data.=rand(0, 9);
        }
        return $data;
    }
    $terminal['sessionkey']=il_auth_rand(64);
    $terminal['sessiontime']=time();
    //cookie更新
    setcookie('sessionkey', $terminal['sessionkey'], 0, '/', $cookiedomain);
    //db更新
    $v=$authDb->query('UPDATE terminal SET sessionkey="'.$terminal['sessionkey'].'", sessiontime='.$terminal['sessiontime'].' WHERE terminalkey="'.$terminalkey.'";');
    authDb_checkError($v);
    //global宣言変数初期化
    $sessionphp_url=null;
    $sessionphp_service=null;
    //終了
    return $terminal['id'];
}
?>
