<?php
function curl_request($url,$post = '',$cookie = '', $returnCookie = 0) {
  $ip_long = array(
    array('607649792', '608174079'), //36.56.0.0-36.63.255.255
    array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
    array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
    array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
    array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
    array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
    array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
    array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
    array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
    array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
  );
  $rand_key = mt_rand(0, 9);
  $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
  //随机ip
  $header = array(
    "CLIENT-IP: $ip",
    "X-FORWARDED-FOR: $ip",
    "X-Real-IP: $ip"
  );
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_USERAGENT, 'User-Agent, Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11');
  curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
  curl_setopt($curl, CURLOPT_REFERER, "https://music.163.com");
  if ($post) {
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
  }
  if ($cookie) {
    curl_setopt($curl, CURLOPT_COOKIE, $cookie);
  }
  curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
  curl_setopt($curl, CURLOPT_TIMEOUT, 10);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $data = curl_exec($curl);
  if (curl_errno($curl)) {
    return curl_error($curl);
  }
  curl_close($curl);
  if ($returnCookie) {
    list($header, $body) = explode("\r\n\r\n", $data, 2);
    preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
    $info['cookie'] = substr($matches[1][0], 1);
    $info['content'] = $body;
    return $info;
  } else {
    return $data;
  }
}
?>