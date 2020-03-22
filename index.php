<?php
@$path = $argv[1];
if (empty($path)) {
    exit("请输入音乐目录");
} elseif (!is_dir($path)) {
    exit("该目录不存在");
} elseif (substr($path, strlen($path) - 1, strlen($path)) == "/") {
    $path = substr($path, 0, strlen($path) - 1);
}
require_once "curl.php";
require_once "getlyric.php";
$handler = opendir($path);
//打开目录
$name = array();
$ape = $flac = $mp3 = $m4a = $unknown =  0;
//初初始化
while (($filename = readdir($handler)) !== false) {
    if ($filename != "." && $filename != "..") {
        if (!strpos($filename, ".") !== false) {
            continue;
        }
        $arr = explode(".", $filename);
        $filename = $arr[0];
        $suffix = strtolower($arr[1]);
        //判断文件类型
        if ($suffix == "ape") {
            $ape++;
        } else if ($suffix == "flac") {
            $flac++;
        } else if ($suffix == "m4a") {
            $m4a++;
        } else if ($suffix == "mp3") {
            $mp3++;
        } else {
            $unknown++;
            continue;
        }
        //获取文件类型
        //去除文件后缀
        array_push($name, $filename);
    }
}
//print_r($name);
echo "扫描完毕...\n----------\nmp3  -> $mp3" .  "个\nflac -> $flac" . "个\nape  -> $ape" . "个\nm4a  -> $m4a" . "个\n未知 -> $unknown" . "个\n----------\n";
echo "开始获取id\n----------\n";
//结果
//通过api获取歌曲名称对应的id
$num = count($name);
//获取歌曲数目
$j = 0;
//有效歌曲数目
for ($i = 0; $i <= $num - 1; $i++) {
    $url = "http://music.163.com/api/search/get/";
    $post = array(
        "s" => $name[$i],
        "limit" => 1,
        "type" => 1,
        "offset" => 0
    );
    $data = curl_request($url, $post, "", "");
    $data = json_decode($data, true);
    if (in_array("songs", $data)) {
        echo $i + 1 . "/" . $num . " | " . $name[$i] . " -> 未找到结果\n";
    } else {
        $id = $data['result']['songs'][0]['id'];
        echo $i + 1 . "/" . $num . " | " . $name[$i] . " -> " . $id . "\n";
        $info[$j] = array(
            "name" => $name[$i],
            "id" => $id
        );
        $j++;
    }
}
//获取id
//print_r($info);
echo "----------\n开始下载歌词\n----------\n";
$suc = 0;
$lyric_num = count($info);
for ($i = 0; $i <= $lyric_num - 1; $i++) {
    if (getlyric($info[$i]['id'], $info[$i]['name'], $path)) {
        echo $i + 1 . "/" . $lyric_num . " | " . $info[$i]['name'] . " -> 成功\n";
        $suc++;
    } else {
        echo $i + 1 . "/" . $lyric_num . " | " . $info[$i]['name'] . " -> 失败\n";
    }
}
$error = $lyric_num - $suc;
echo "----------\n下载完毕\n成功 -> $suc" . "个\n失败 -> $error"  . "个\n----------\n";
