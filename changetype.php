<?php 
/*
    2020.5.31
    xcsoft
    用于转换sony walkman 歌词格式问题
*/

//获取目录
@$path = $argv[1];
if (empty($path)) {
    exit("请输入音乐目录");
} elseif (!is_dir($path)) {
    exit("该目录不存在");
} elseif (substr($path, strlen($path) - 1, strlen($path)) == "/") {
    $path = substr($path, 0, strlen($path) - 1);
}

$handler = opendir($path);
//打开目录
$name = array();
//初始化歌词文件
$lrc = $unknown =  0;
//chushiuhua 
while (($filename = readdir($handler)) !== false) {
    //打开文件
    if ($filename != "." && $filename != "..") {
        //排除上级目录
        if (!strpos($filename, ".") !== false) {
            //如果文件中不含有后缀名，则跳过
            continue;
        }
        $arr = explode(".", $filename);
        $filename = $arr[0];
        $suffix = strtolower($arr[1]);
        //获取文件后缀名
        if ($suffix == "lrc") {

            $lrc++;
            array_push($name,$filename . ".lrc");
            //加入数组
        }else{
            $unknown++;
            continue;
        }
        //获取文件类型
        //去除文件后缀
    }
}
echo "扫描完毕...\n----------\n共" . $lrc . "个\n----------\n";
if ($lrc == "0") {
    exit("扫描结束\n无结果\n----------\n");
}
echo "开始修改\n----------\n";
print_r($name);
for($i = 0; $i <= $lrc - 1;$i++){
    $filepath = $path . "/" . $name[$i];
    $content = file_get_contents($filepath);
    $data = f4(file_get_contents($filepath));
    $num = count($data[1]);
    for($j = 0;$j <= $num - 1;$j++)
    {
        if(strlen($data[1][$j]) == 9)
        {
            $s = $data[1][$j];
            $e = substr($data[1][$j],0,8);
            $content = str_replace($s,$e,$content);
        }
    }
    unlink($filepath);
    //删除文件
    file_put_contents ($filepath,$content);
    //写入文件
    echo $i+1 . "/" . $lrc .  " | " . $name[$i] . "->" . "修改完毕" . "\n";
}

function f4($str)
{
    $result = array();
    preg_match_all("/(?:\[)(.*)(?:\])/i",$str, $result);
    return $result;
}
//正则匹配

//处理毫秒数导致歌词不显示的问题
?>