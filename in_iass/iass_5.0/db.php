<?php
//db接続情報
$db_host=getenv('DB_HOST');
$db_name='in_iass';
$db_user='in_iass';
$db_password='ph9gfibH%Awnr';

//db接続
try{
    $db=new PDO('mysql:host='.$db_host.'; dbname='.$db_name.';setchar=utf-8',$db_user,$db_password,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}catch(PDOException $e){
    error('IASS/Database connection error.');
}

//関連関数
function db_checkError($v){
    if($v===0){
        error('DBが参照できません。');
    }
}
function db_encvalue($v1){
    db_checkError($v1);
    foreach($v1 as $v2){
        foreach($v2 as $v3){
            return $v3;
        }
    }
}
function db_encrecord($v1){
    db_checkError($v1);
    foreach($v1 as $v2){
        return $v2;
    }
}
?>