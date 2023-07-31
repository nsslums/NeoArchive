<?php
//データベース接続情報はdb.phpに記述
//データ保存場所
$cfg_datadir='/mnt/persistent-volume/iass/data/';
$cfg_dustbox='/mnt/persistent-volume/iass/dustbox/';
$cfg_uploadtmp='/mnt/persistent-volume/iass/uploadtmp/';
$cfg_userdata='/var/www/in_iass/userdata/';
$cfg_session='/var/www/in_auth/auth_2.1/session.php';
$cfg_session_multiacces='/var/www/in_auth/auth_2.1/session-multiacces.php';
$cfg_ffprobe='/var/www/in_iass/ffmpeg-4.2.2-i686-static/ffprobe';
$cfg_ffmpeg='/var/www/in_iass/ffmpeg-4.2.2-i686-static/ffmpeg';
$cfg_session_logout='https://auth.binarymonster.net/logout.php?target=iass';//URL
$cfg_qualityDisplay=['1080p'=>'最高品質', '1080p[1440x1080]'=>'高品質', '720p'=>'中品質', '720p[960x720]'=>'中低品質', '480p'=>'低品質'];
$cfg_qualityEncode=['1080p'=>['width'=>1920, 'height'=>1080], '1080p[1440x1080]'=>['width'=>1440, 'height'=>1080], '720p'=>['width'=>1280, 'height'=>720], '720p[960x720]'=>['width'=>960, 'height'=>720], '480p'=>['width'=>640, 'height'=>360]];
$cfg_bgiTrueExtension=['jpg', 'png', 'gif'];
$cfg_defaultUserdata=['autoplay'=>0, 'background'=>'default'];
$cfg_request_status=['待ち...', '録画中', '追加完了', '削除'];
?>
