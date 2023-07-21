<?php
//key確認
if(isset($_GET['key'])){
    $key=htmlspecialchars($_GET['key'], ENT_QUOTES);
}else{
    //keyなし
    sleep(1);
    header('HTTP', true, 403);
    exit();
}
//target確認
if(isset($_GET['target'])){
    $target=$_GET['target'];
}else{
    //targetなし
    sleep(1);
    header('HTTP', true, 403);
    exit();
}
//DB接続
include 'authError.php';
include 'authDb.php';
//keyのDB検索
$v=$authDb->query('SELECT count(nkey) FROM newuser WHERE nkey="'.$key.'";');
if(authDb_encvalue($v)==0){
    //一致するkeyなし
    sleep(1);
    header('HTTP', true, 403);
    exit();
}

//入力画面
//$status=0 初期, $status=1 ID使えない, $status=2 同じPWを入力, $status=3 違うPW
function input($status){
    //宣言
    global $key, $target;
    $comment=null;
    //status
    if($status==1){
        $comment='<p class="warning">このIDは既に使われているため使えません。</p>';
    }elseif($status==2){
        $comment='<p class="warning">同じPasswordを入力してください。</p>';
    }elseif($status==3){
        $comment='<p class="warning">このパスワードは条件を満たしていません。</p>';
    }
    //出力
    if($status>=0 AND $status<=3){
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <style>@import url("style.css");</style>
            <title>ILNアカウント作成</title>
        </head>
        <body>
            <div id="content">
                <h1>ILNアカウント作成ページ</h1>
                <p>このURLで作成できるアカウントは1個です。<br>
                    パスワードは以下の条件を達成しないと作成できません。<br>
                    ・8文字以上72文字以下である。<br>
                    ・全体の50%以上が同じ文字でない。<br>
                    ・大文字、小文字、数字、記号の2種類以上を使用する。</p>
                '.$comment.'
                <form action="newaccount.php?key='.$key.'&target='.$target.'" method="POST" accept-charset="UTF-8">
                    <input type="text" name="iln_n_id" maxlength="32" required placeholder="IDを入力してください。"><br>
                    <input type="password" name="iln_n_password1" maxlength="72" required placeholder="Passwordを入力してください。"><br>
                    <input type="password" name="iln_n_password2" maxlength="72" required placeholder="もう一度Passwordを入力してください。"><br>
                    <input type="submit" value="アカウント作成">
                </form>
            </div>
        </body>
        </html>';
    }elseif($status>=4){
        echo '<!DOCTYPE html>
        <html lang="ja">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>@import url("style.css");</style>
            <title>ILNアカウント作成</title>
        </head>
        <body>
            <div id="content">
                <h1>ILNアカウント作成完了!</h1>
                <p>アカウント作成が完了しました。<br>
                    <span id="move">5</span>秒後にログイン画面に移動します。</p>
            </div>
        </body>
        <script type="text/javascript">
        var i=5;
        
        window.onload=function(){
            move();
        }
        
        function move(){
            document.getElementById("move").innerText=i;
            i--;
            if(i==0){
                location.href="index.php?target='.$target.'";
            }
            setTimeout(move, 1000);
        }
        </script>
        </html>';
    }
    exit();
}

//id, pwd入力確認
if(isset($_POST['iln_n_id'])==0 OR isset($_POST['iln_n_password1'])==0 OR isset($_POST['iln_n_password1'])==0){
    //入力なし
    //入力画面を出力
    input(0);
}

//入力取得
$nid=htmlspecialchars($_POST['iln_n_id'], ENT_QUOTES);
$npw1=htmlspecialchars($_POST['iln_n_password1'], ENT_QUOTES);
$npw2=htmlspecialchars($_POST['iln_n_password2'], ENT_QUOTES);

//idのbyte数を確認
if(strlen($nid)>32){
    //id利用不能
    input(1);
}
//idが利用可能か確認
$v=$authDb->query('SELECT count(uid) FROM account WHERE uid="'.$nid.'";');
if(authDb_encvalue($v)==1){
    //id利用不能
    input(1);
}

//パスワード条件チェック
function password_check($pw){
    $pw=str_split($pw);//PWを配列に展開
    $cd=count($pw);//PWの文字数
    //文字数チェック
    if($cd<8 OR $cd>72){
        //PW8文字以上72文字以下
        return 0;
    }

    //文字要素検索
    $char[0]=$char[1]=$char[2]=$char[3]=0;
    foreach($pw as $loop){
        if(ctype_upper($loop)==1){
            $char[0]++;
        }elseif(ctype_lower($loop)==1){
            $char[1]++;
        }elseif(ctype_digit($loop)==1){
            $char[2]++;
        }else{
            $char[3]++;
        }
    }
    //文字要素合算
    $charu=0;
    foreach($char as $loop){
        if($loop>0){
            $charu++;
        }
    }
    //文字要素分岐
    if($charu<2){
        //大文字、小文字、数字、記号を2つ以上使う
        return 0;
    }

    //文字検索
    $coMax=intval($cd/2);//同じ文字の最大占有率
    for($i=0; $i<$cd; $i++){
        $co=0;
        for($j=0; $j<$cd; $j++){
            if($pw[$i]==$pw[$j]){
                $co++;
            }
        }
        if($co>=$coMax){
            //同じ文字は50%まで
            return 0;
        }
    }

    //このパスワードは使えます
    return 1;
}

//passwordチェック
if($npw1!=$npw2){
    //同じパスワードを入力
    input(2);
}
if(password_check($npw1)==0){
    //passwordが条件を満たしていません
    input(3);
}
//password暗号化
$dbpw=password_hash($npw1, PASSWORD_DEFAULT);

//DB登録
$v=$authDb->query('INSERT INTO account (uid, pwd) VALUE("'.$nid.'", "'.$dbpw.'");');
authDb_checkError($v);

//招待keyの削除
$v=$authDb->query('DELETE FROM newuser WHERE nkey="'.$key.'";');
authDb_checkError($v);

//完了を出力
input(4);
?>