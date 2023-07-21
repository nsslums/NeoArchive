<?php
//DBは別ファイルです。db.phpを書き換えてください。
//Sessionは別ファイルです。session.phpを書き換えてください。
//Service関係
//配列的に$config_serviceと$config_serviceUrlは同順である必要があります。
//サービスはDBに登録する必要があります。
$config_service=['admin', 'iass', 'cu_admin', 'matchResult'];//設定可能サービス
$config_serviceUrl=['accountAdmin.php', 'https://iass.binarymonster.net/index.php', 'https://cu-admin.binarymonster.net/index.php', 'https://matchResult.binarymonster.net?p=gameList'];//サービスURL
$config_url='https://auth.binarymonster.net/';//wwwroot
$cookiedomain='binarymonster.net';//cookieアクセス可能ドメイン
?>
