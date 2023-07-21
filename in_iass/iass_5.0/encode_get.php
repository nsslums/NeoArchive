<?php
ini_set('display_errors', "On");
//設定読み込み
include 'config.php';
//エラー
include 'error.php';
//DB接続
include 'db.php';
//fileドライバ
include 'file.php';

//認証
//認証
if(isset($_GET['bot'])){
	if(isset($_GET['key'])){
		if($_GET['key']==='***REMOVED***'){
			//正常
		}else{
			header('http', true, 404);
			exit();
		}
	}else{
		header('http', true, 404);
		exit();
	}
}else{
	header('http', true, 404);
	exit();
}

//エンコードリスト
$aids = $db->query('SELECT aid FROM anime;');
db_checkError($aids);

function search($aid){
    //宣言
    global $cfg_datadir;
    //読み込み
    $dir = $cfg_datadir.$aid.'/info.json';
    $info = json_decode(fileget($dir), true);
    //処理
    foreach($info as $cool){
        foreach($cool as $content){
            $low = false;
            foreach($content['quality'] as $quality){
                if($quality == '480p'){
                    $low = true;
                }
            }
            if($low == false){
                if(file_exists($cfg_datadir.$aid.'/'.$content['id'].'-480p.mp4') == 1 OR file_exists($cfg_datadir.$aid.'/'.$content['id'].'-'.$content['quality'][0].'.mp4') == 0){
                    echo json_encode(['status' => '2']);
                    exit();
                }
                echo json_encode(['status' => '1', 'aid' => $aid, 'id' => $content['id'], 'quality' => $content['quality'][0]]);
                exit();
            }
        }
    }
}

foreach($aids as $aid){
    search($aid['aid']);
}

echo json_encode(['status' => '0']);
?>