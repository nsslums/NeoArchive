<?php
//※proceError.phpをincludeするかproceError()を宣言してください。
//設定
$db_host=getenv('DB_HOST');//接続先DBのホスト名
$db_name='in_auth';//DB名
$db_user='in_auth';//User名
$db_password='***REMOVED***';//パスワード

//db接続
try{
    $authDb=new PDO('mysql:host='.$db_host.'; dbname='.$db_name.';setchar=utf-8',$db_user,$db_password,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}catch(PDOException $e){
    error_log($e);
    authError('DBに接続できません。');
}

//関連関数
function authDb_checkError($v){
    if($v===0){
        authError('DBが参照できません。');
    }
}
function authDb_encvalue($v1){
    authDb_checkError($v1);
    foreach($v1 as $v2){
        foreach($v2 as $v3){
            return $v3;
        }
    }
}
function authDb_encrecord($v1){
    authDb_checkError($v1);
    foreach($v1 as $v2){
        return $v2;
    }
}
?>