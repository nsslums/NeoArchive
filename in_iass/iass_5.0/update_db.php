<?php
//db接続情報
$db_host='localhost';
$db_name='iass_db';
$db_user='iass';
$db_password='TdJNM09983';

//db接続
try{
    $udb=new PDO('mysql:host='.$db_host.'; dbname='.$db_name.';setchar=utf-8',$db_user,$db_password,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}catch(PDOException $e){
    error('IASS/Database connection error.');
}

//関連関数
function udb_checkError($v){
    if($v===0){
        error('DBが参照できません。');
    }
}
function udb_encvalue($v1){
    db_checkError($v1);
    foreach($v1 as $v2){
        foreach($v2 as $v3){
            return $v3;
        }
    }
}
function udb_encrecord($v1){
    udb_checkError($v1);
    foreach($v1 as $v2){
        return $v2;
    }
}
?>