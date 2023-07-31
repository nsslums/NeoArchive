<?php
//設定ファイルの読み込み
require_once 'config.php';
//宣言
global $cookiedomain;

if(isset($_COOKIE['sessionkey']) && isset($_GET['target'])){
    setcookie('sessionkey', '', -3600, '/', $cookiedomain);
    $mes = '<p>ログアウトしました。<br><span id="move"></span>秒後にログイン画面に移動します。</p>';
    $target=$_GET['target'];
}else{
    header('http', true, 404);
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログアウト</title>
</head>
<body>
<?php echo $mes ?>
</body>
<script type="text/javascript">
var i=3;

window.onload=function(){
    move();
}

function move(){
    document.getElementById("move").innerText=i;
    i--;
    if(i==0){
        location.href='index.php?target=<?php echo $target; ?>';
    }
    setTimeout(move, 1000);
}
</script>
</html>