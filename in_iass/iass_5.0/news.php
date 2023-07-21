<?php
function newsRead($nid=0, $max=16){
    //DB読み込み
    global $db;
    //データ取得
    $data=$db->query('SELECT * FROM news WHERE nid>'.$nid.' ORDER BY nid DESC LIMIT '.$max.';');
    db_checkError($data);
    //件数
    $number=$data->rowCount();
    //変換
    if($number>0){
        $ndata=array_fill(0, $number, null);
        $i=$number-1;
        foreach($data as $value){
            $ndata[$i]['nid']=$value['nid'];
            $ndata[$i]['title']=$value['title'];
            $ndata[$i]['content']=$value['content'];
            $ndata[$i]['time']=date('n/d', $value['time']);
            $i--;
        }
    }else{
        $ndata=null;
    }
    //完了
    return array('data'=>$ndata, 'number'=>$number);
}
function newsWrite($title, $content, $time){
    global $db;
    db_checkError($db->query('INSERT INTO news (title, content, time) VALUE("'.$title.'", "'.$content.'", '.$time.');'));
}
?>