<?php
function getlyric($id,$name,$path){
  $data = file_get_contents("https://music.163.com/api/song/lyric?os=pc&id=" . $id . "&lv=-1&kv=-1&tv=-1");
  $data = json_decode($data,true);
  @$lyric = $data['lrc']['lyric'];
  if(!empty($lyric))
  {
    $file = fopen("$path/$name.lrc", "a");
    fwrite($file,$lyric);
    fclose($file);
    return true;
  }else{
    return false;
  }
  
}