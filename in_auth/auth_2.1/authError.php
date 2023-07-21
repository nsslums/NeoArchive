<?php
//旧proceError()
function authError($message){
    $html=null;
    $html.='<p>エラーが発生しました。<br>複数回同じエラーが発生する場合は管理人に連絡してください。</p>';
    $html.='<a href="index.html">トップに戻る</a>';
    $html.='<p>詳細<br>'.$message.'</p>';
    echo $html;
    exit();
}
?>