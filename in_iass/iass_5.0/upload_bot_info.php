<?php
//設定読み込み
include 'config.php';
//DB接続
include 'db.php';
//fileドライバ
include 'file.php';
//通知
include 'news.php';

$key='***REMOVED***';//iass_recclient.phpとペアである必要があります。
//テスト用URL
//http://localhost/IASS_v5.0/iass_v5.0/upload_bot_info.php?key=***REMOVED***&type=send_animeInfo&data=time:1,channel:BS11,name1:%E3%81%A8%E3%81%82%E3%82%8B%E7%A7%91%E5%AD%A6%E3%81%AE%E8%B6%85%E9%9B%BB%E7%A3%81%E7%A0%B2,name2:1%E6%9C%9F,videoSize:1920x1080,play_time:1440,subtitle:%E3%81%82,math:1
//http://localhost/IASS_v5.0/iass_v5.0/upload_bot_info.php?key=***REMOVED***&type=get_math&data=name1:%E3%81%A8%E3%81%82%E3%82%8B%E7%A7%91%E5%AD%A6%E3%81%AE%E8%B6%85%E9%9B%BB%E7%A3%81,name2:2%E6%9C%9F

if(isset($_GET['key'])==0 OR isset($_GET['type'])==0 OR isset($_POST['data'])==0){
	echo 'ERROR';
	exit;
}

if($_GET['key']===$key){
	if($_GET['type']=='send_animeInfo'){
		//データ取得&展開
		$data=explode(",",$_POST['data']);
		if(count($data)==8){
			foreach($data as $value){
				$value=explode(":",$value);
				$iData[$value[0]]=$value[1];
			}
		}else{
			echo 'ERROR';
			exit;
		}
		//データ修正
		$iData['time']=intval($iData['time']);
		$iData['play_time']=intval($iData['play_time']);
		//SQL問い合わせ
		$anime=$db->query('SELECT aid FROM anime WHERE name="'.$iData['name1'].'";');
		db_checkError($anime);
		if($anime->rowCount()==0){
			//新規
			db_checkError($db->query('INSERT INTO anime (name, time_edcb, icon_id) VALUE("'.$iData['name1'].'", '.$iData['time'].', 0);'));
			$aid=db_encvalue($db->query('SELECT aid FROM anime WHERE name="'.$iData['name1'].'";'));
			$id=0;
			//ファイル系
			$dir=$cfg_datadir.$aid;
			mkdir($dir);
			$dir=$cfg_datadir.$aid.'/info.json';
			touch($dir);
		}else{
			//追加
			$aid=db_encvalue($anime);
			//更新
			db_checkError($db->query('UPDATE anime SET time_edcb='.$iData['time'].' WHERE aid='.$aid.';'));
			//ファイル系
			$dir=$cfg_datadir.$aid.'/info.json';
			$info=fileget($dir);
			if($info===0){
				echo 'ERROR';
				exit();
			}
			$info=json_decode($info, true);
			//利用id検索
			$id=0;
			foreach($info as $cool){
				foreach($cool as $content){
					if($id<$content['id']){
						$id=$content['id'];
					}
				}
			}
			$id++;
		}
		//解像度
		$movieInfo=explode('x', $iData['videoSize']);
		$t=0;
		foreach($cfg_qualityEncode as $k1=>$v1){
			if($movieInfo[0]==$v1['width'] && $movieInfo[1]==$v1['height']){
				$quality=[$k1];
				$t=1;
			}
		}
		if($t==0){
			echo 'ERROR';
			exit();
		}
		//データ作成
		$info[$iData['name2']][]=[
			'id'=>$id,
			'number'=>$iData['math'],
			'subtitle'=>$iData['subtitle'],
			'playtime'=>$iData['play_time'],
			'quality'=>$quality,
			'from'=>'EDCB/'.$iData['channel'],
			'time'=>$iData['time']
			];
		//データ書き込み
		if(fileput($dir, json_encode($info))==0){
			echo 'ERROR';
		}
		//データ送信
		echo $aid.'-'.$id;
		//通知
		newsWrite('録画完了', 'まもなく'.$iData['name1'].'</a>が追加されます。', time());
		exit;
	}elseif($_GET['type']=='get_math'){
		//データ取得&展開
		$data=explode(",",$_POST['data']);
		if(count($data)==2){
			foreach($data as $value){
				$value=explode(":",$value);
				$iData[$value[0]]=$value[1];
			}
		}else{
			echo 'ERROR';
			exit;
		}
		//SQL問い合わせ
		$anime=$db->query('SELECT aid FROM anime WHERE name="'.$iData['name1'].'";');
		db_checkError($anime);
		if($anime->rowCount()==0){
			//新規
			$math=1;
		}else{
			//追加
			$aid=db_encvalue($anime);
			//ファイル系
			$dir=$cfg_datadir.$aid.'/info.json';
			$info=fileget($dir);
			if($info===0){
				echo 'ERROR';
				exit();
			}
			$info=json_decode($info, true);
			//name2
			if(isset($info[$iData['name2']])){
				//追加
				//numberの最大
				$math=0;
				for($i=0; $i<count($info[$iData['name2']]); $i++){
					if($math<$info[$iData['name2']][$i]['number']){
						$math=$info[$iData['name2']][$i]['number'];
					}
				}
				$math++;
			}else{
				//新規
				$math=1;
			}
		}
		//出力
		echo $math;
		exit;
	}
}

echo 'ERROR';
exit;
?>