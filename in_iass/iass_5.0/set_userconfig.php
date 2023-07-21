<?php
//設定ファイル読み込み
include 'config.php';
//認証
include $cfg_session;
$uid=session('iass');
//エラー
include 'error.php';
//db接続
include 'db.php';
include 'file.php';

//ユーザー設定
$dir=$cfg_userdata.$uid;
if(file_exists($dir)==0){
    mkdir($dir);
}
$dir_uc=$dir.'/config.json';
if(file_exists($dir_uc)==0){
    touch($dir_uc);
    $userdata=$cfg_defaultUserdata;
}else{
    $userdata=fileget($dir_uc);
    if($userdata===0){
        error('userdataを読み込めませんでした。');
    }
    $userdata=json_decode($userdata, true);
}

//壁紙変更
$message=null;
if(isset($_POST['bg-kind'])){
    $bg=htmlspecialchars($_POST['bg-kind'], ENT_QUOTES);
    $dbg='default';
    if($bg==1){
        //画像アップロード
        if($_FILES['bg-file']['name']!=null){
            $name=htmlspecialchars($_FILES['bg-file']['name'], ENT_QUOTES);
            $ext=pathinfo($name, PATHINFO_EXTENSION);
            $t=0;
            foreach($cfg_bgiTrueExtension as $trueExt){
                if($trueExt==$ext){
                    $t=1;
                    break;
                }
            }
            if($t==0){
                $message.='アップロードできるファイル形式はjpg, png, gifのみです。';
            }else{
                //ファイル変更
                $name='bg.'.$ext;
                $dir=$cfg_userdata.$uid.'/'.$name;
                if(move_uploaded_file($_FILES['bg-file']['tmp_name'], $dir)==0){
                    $message.='ファイルがアップロードできませんでした。';
                }else{
                    $dbg=$name;
                }
            }
        }else{
            $dir=$cfg_userdata.$uid.'/';
            $t=0;
            foreach($cfg_bgiTrueExtension as $ext){
                $dir=$dir.'bg.'.$ext;
                if(file_exists($dir)){
                    $dbg='bg.'.$ext;
                    $t=1;
                    break;
                }
            }
            if($t==0){
                $message.='壁紙に画像を使用する前に画像をアップロードしてください。';
            }
        }
    }
    $userdata['background']=$dbg;
    if(fileput($dir_uc, json_encode($userdata))==0){
        $message.='設定ファイルに書き込めませんでした。';
    }
}
//html生成
$html='<div id="maincontent">
<h2>ユーザー設定</h2>
<form action="set_userconfig.php" enctype="multipart/form-data" method="POST" accept-charset="UTF-8">
    <p>壁紙設定</p>
    <p>'.$message.'</p>
    <p>デフォルト(白)<input type="radio" name="bg-kind" value="0"></p>
    <p>画像を使用<input type="radio" name="bg-kind" value="1"></p>
    <input type="file" name="bg-file" accept="image"><br>
    <input type="submit" value="変更">
</form>
<h2>アカウント設定(外部サイト)</h2>
<a href="">準備中</a>
</div>';

//設定
include 'comhtml.php';
comhtml('設定', $html);
?>