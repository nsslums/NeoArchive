<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session_multiacces;
$uid=session_multiacces('iass');

//動画情報取得
if(isset($_GET['aid'])==0 OR isset($_GET['id'])==0 OR isset($_GET['quality'])==0){
	header('http', true, 403);
	exit();
}
$aid=htmlspecialchars($_GET['aid'], ENT_QUOTES);
$id=htmlspecialchars($_GET['id'], ENT_QUOTES);
$quality=htmlspecialchars($_GET['quality'], ENT_QUOTES);
//dir
$dir=$cfg_datadir.$aid.'/'.$id.'-'.$quality.'.mp4';
if(file_exists($dir)==0){
	header('http', true, 403);
	exit();
}

function write_log($text){
        umask(0);
        $log = date("Y-m-d H:i:s") . " {$_SERVER['REQUEST_URI']}    {$text}\n";
        error_log($log, 3, "access.log");
}

$file = $dir;                                                      // 動画ファイルへのパス
$size = filesize($file);
$fp = fopen($file, "rb");
$step = 2000000;                                                        // 最大伝送サイズ
if (@$_SERVER["HTTP_RANGE"]){                                           // ブラウザがHTTP_RANGEを要求してきた場合
    list($start, $end) = sscanf($_SERVER["HTTP_RANGE"], "bytes=%d-%d"); // 要求された開始位置と終了位置を取得
    $s = $end - $start + 1;
    write_log('$_SERVER["HTTP_RANGE"]:'.$_SERVER["HTTP_RANGE"]);
    if (empty($end)) $end = $start + $step - 1;                         // 終了位置が指定されていない場合$step bytes出す
    fseek($fp, $start);
} else {
    $start = 0;
    $end = $step - 1;
}
if ($end - $start >= $step) $end = $start + $step - 1;                  // 要求が$stepより多い場合$stepに制限
if ($end >= $size - 1) $end = $size - 1;                                // 要求が動画の終了を超えている場合制限
$c_size = $end - $start + 1;                                            // 提供サイズ
$etag = md5($_SERVER["REQUEST_URI"]).$size;                             // コンテンツの識別子
$header_http = "HTTP/1.1 206 Partial Content";
$header_content = "Content-Type: video/mp4";
$header_accept = "Accept-Ranges: bytes";
$header_range = "Content-Range: bytes {$start}-{$end}/{$size}";
$header_length = "Content-Length: {$c_size}";
$header_etag = "Etag: \"{$etag}\"";
header($header_http);
write_log($header_http);
header($header_content);
write_log($header_content);
header($header_accept);                                                 // HTTP_RANGE(部分リクエスト)に対応
write_log($header_accept);
header($header_range);
write_log($header_range);
header($header_length);
write_log($header_length);
header($header_etag);
write_log($header_etag);
if ($c_size) echo fread($fp, $c_size);                                  // ファイルポインタの開始位置からコンテンツ長だけ出力
fclose($fp);
?>
