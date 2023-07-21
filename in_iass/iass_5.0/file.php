<?php
function fileget($dir){
    if(file_exists($dir)){
        if(is_readable($dir)){
            return file_get_contents($dir);
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}
function fileput($dir, $data){
    if(file_exists($dir)){
        if(is_writable($dir)){
            if($fp=fopen($dir, "w")){
                if(flock($fp, LOCK_EX)){
                    if(fwrite($fp, $data)){
                        return 1;
                    }else{
                        return 0;
                    }
                    flock($fp, LOCK_UN);
                }else{
                    return 0;
                }
                fclose($fp);
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}
?>