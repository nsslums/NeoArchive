<?php
//DBは別ファイルです。db.phpを書き換えてください。
//Sessionは別ファイルです。session.phpを書き換えてください。
//Service関係
//配列的に$config_serviceと$config_serviceUrlは同順である必要があります。
//サービスはDBに登録する必要があります。

//環境変数から取得
$domain = getenv('HTTP_S_DOMAIN');
$schema = getenv('HTTP_SCHEMA');

$config_service = ['admin', 'iass', 'cu_admin', 'matchResult'];//設定可能サービス
$config_serviceUrl = ['accountAdmin.php', $schema.'://iass.'.$domain.'/index.php', $schema.'://cu-admin.'.$domain.'/index.php', $schema.'://matchResult.'.$domain.'?p=gameList'];//サービスURL
$config_url = $schema.'://auth.'.$domain.'/';//wwwroot
$cookiedomain = $domain;//cookieアクセス可能ドメイン
?>
