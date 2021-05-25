<?php
$t = time();
$date = date('d/n/Y H:i:s', $t);
$ip = getenv('HTTP_CF_CONNECTING_IP');
$userAgent = getenv('HTTP_USER_AGENT');

if(isset($_POST['name']) && $_POST['name'] != "See Tow Shiun Hou") {
    $data = $_POST['name'] . ',' . $_POST['email'] . ',' . $_POST['location'] . ',' . $date . ',' . $ip . ',"' .  $userAgent . '"' ."\n";
    $ret = file_put_contents('data.txt', $data, FILE_APPEND | LOCK_EX);
    if($ret === false) {
        die('There was an error writing this file');
    }
    else {
        echo "$ret bytes written to file";
    }
}
else {
   die('Nothing to see here.');
}
?>