<!DOCTYPE html>
<html lang="en">
  <!-- FUCK YOU ZACHARY STOP LOOKING  -->

<head>
<meta name="description" content="See Tow Bus">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />
<script>
window.addEventListener("load", function(){
window.cookieconsent.initialise({
  "palette": {
    "popup": {
      "background": "#000"
    },
    "button": {
      "background": "#f1d600"
    }
  },
  "position": "bottom-right"
})});
</script>  
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script>
  (adsbygoogle = window.adsbygoogle || []).push({
    google_ad_client: "ca-pub-9218984794093639",
    enable_page_level_ads: true
  });
</script>
<script>
function goBack() {
    window.history.back();
}
</script>
<style type="text/css">
.instagram-media{
  margin: 15px auto !important;
}
body {
}
footer {
    bottom: 0;
}
table {
table-layout:fixed
}
td {
overflow:hidden; white-space:nowrap
}
th {
overflow:hidden; white-space:nowrap
}
</style>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>See Tow Bus (Beta)</title>

  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://cdn.rawgit.com/Hi1307/ltabus/b896c394/flaticon.css" rel="stylesheet">
  <link href="https://cdn.rawgit.com/Hi1307/website/990ddbd1/seetow.me/htdocs/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="https://cdn.rawgit.com/Hi1307/website/73507e0d/seetow.me/htdocs/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
<div class="navbar-fixed">
  <nav class="white" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="/" class="brand-logo"><i class="material-icons">arrow_back</i></a>
      <ul class="right hide-on-med-and-down">
              <li><a href="#" onClick="window.location.reload()"><i class="material-icons">refresh</i></a></li>
      <li><a href="https://translate.google.com/translate?sl=en&tl=ja&js=y&prev=_t&hl=en&ie=UTF-8&u=bus.seetow.me&edit-text=&act=url" target="_blank"><i class="material-icons">translate</i></a></li>
      </ul>
  </nav>
</div>


  <div class="container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
<h3 class="center">
See Tow Bus (Beta)
</h3>
<?php
$busstop = $_GET['stop'];
if($busstop === '19089'){
$easteregg = 'true';
}elseif($busstop === '19081'){
$easteregg = 'true';
};
?>
<p class="center">Your selected bus stop ID is <? echo $busstop; ?>.</p>
<p class="center"><a target="_blank" href="https://google.com.sg/maps/search/stop+<? echo $busstop; ?>/">Where is my bus stop?</a></p>
<p class="center">These results were last updated on <? echo date("Y/m/d") ?> at <? echo date("h:i:sa") ?>.</p>
      <div class="switch center">
    <label>
      <input type="checkbox">
      <span class="lever changebg"></span>
      Dark Theme
    </label>
  </div>


<meta name="viewport" content="width=device-width, initial-scale=1">
      <table class="striped responsive-table" id="table">
          <thead>
            <th style=min-width:90px>Bus Number</th>
            <th style=min-width:90px>Time (1st)</th>
            <th style=min-width:90px>Features (1st)</th>
            <th style=min-width:90px>Time (2nd)</th>
            <th style=min-width:90px>Features (2nd)</th>
            <th style=min-width:90px>Time (3rd)</th>
            <th style=min-width:90px>Features (3rd)</th>
            <th style=min-width:90px>Location (1st)</th>
            <th style=min-width:90px>Space (1st)</th>
          </thead><tr>
<?php

$apicall = 'https://arrivelah.herokuapp.com/?id='.$busstop.'';
$extractInfo = file_get_contents($apicall);
$extractInfo = str_replace('},]',"}]",$extractInfo);
$showInfo = json_decode($extractInfo, true);
$bus0 = $showInfo['services']['0']['no'];
$arrtime0ms = $showInfo['services']['0']['next']['duration_ms'];
$arrtime0r = $arrtime0ms / '60000';
$arrtime0 = round($arrtime0r, 2);
$lat0 = $showInfo['services']['0']['next']['lat'];
$lon0 = $showInfo['services']['0']['next']['lng'];
$wb0r = $showInfo['services']['0']['next']['feature'];
$sd0r = $showInfo['services']['0']['next']['type'];
$arrtime0ms2 = $showInfo['services']['0']['next2']['duration_ms'];
$arrtime0r2 = $arrtime0ms2 / '60000';
$arrtime02 = round($arrtime0r2, 2);
$wb02r = $showInfo['services']['0']['next2']['feature'];
$sd02r = $showInfo['services']['0']['next2']['type'];
$arrtime0ms3 = $showInfo['services']['0']['next3']['duration_ms'];
$arrtime0r3 = $arrtime0ms3 / '60000';
$arrtime03 = round($arrtime0r3, 2);
$wb03r = $showInfo['services']['0']['next3']['feature'];
$sd03r = $showInfo['services']['0']['next3']['type'];
$seat0 = $showInfo['services']['0']['next']['load'];
if($arrtime0 > '1.5'){
echo '<td style=min-width:50px>'.$bus0.'</td><td style=min-width:50px>'.$arrtime0.'mins</td>';
}else{
echo '<td style=min-width:50px>'.$bus0.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd0r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd0r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb0r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
echo '<td style=min-width:50px>'.$arrtime02.'mins</td>';
if($sd02r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd02r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb02r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
echo '<td style=min-width:50px>'.$arrtime03.'mins</td>';
if($sd03r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd03r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb03r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat0.','.$lon0.'" target="_blank">Click here</a></td>';
if($seat0 === 'SEA'){
echo '<td style=min-width:50px>Seating</td>';
}elseif($seat0 === 'SDA'){
echo '<td style=min-width:50px>Standing</td>';
}else{
echo '<td style=min-width:50px>Limited</td>';
};
?>
</tr>


<tr>
<?php
$bus1 = $showInfo['services']['1']['no'];
$arrtime1ms = $showInfo['services']['1']['next']['duration_ms'];
$arrtime1r = $arrtime1ms / '60000';
$arrtime1 = round($arrtime1r, 2);
$lat1 = $showInfo['services']['1']['next']['lat'];
$lon1 = $showInfo['services']['1']['next']['lng'];
$wb1r = $showInfo['services']['1']['next']['feature'];
$sd1r = $showInfo['services']['1']['next']['type'];
$arrtime1ms2 = $showInfo['services']['1']['next2']['duration_ms'];
$arrtime1r2 = $arrtime1ms2 / '60000';
$wb12r = $showInfo['services']['1']['next2']['feature'];
$sd12r = $showInfo['services']['1']['next2']['type'];
$arrtime1ms3 = $showInfo['services']['1']['next3']['duration_ms'];
$arrtime1r3 = $arrtime1ms3 / '60000';
$arrtime13 = round($arrtime1r3, 2);
$wb13r = $showInfo['services']['1']['next3']['feature'];
$sd13r = $showInfo['services']['1']['next3']['type'];
$arrtime12 = round($arrtime1r2, 2);
$seat1 = $showInfo['services']['1']['next']['load'];
if($arrtime1 > '1.5'){
echo '<td style=min-width:50px>'.$bus1.'</td><td style=min-width:50px>'.$arrtime1.'mins</td>';
}elseif(!isset($arrtime1ms)){
  echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus1.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd12r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd12r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb1r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
if($arrtime12 > '0'){
echo '<td style=min-width:50px>'.$arrtime12.'mins</td>';
}else{};
if($sd12r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd12r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb12r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime13 > '0'){
echo '<td style=min-width:50px>'.$arrtime13.'mins</td>';
}else{};
if($sd13r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd13r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb13r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($lon1 > '1'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat1.','.$lon1.'" target="_blank">Click here</a></td>';
}else{};
if($seat1 === 'SEA'){
echo '<td style=min-width:50px>Seating</td>';
}elseif($seat1 === 'SDA'){
echo '<td style=min-width:50px>Standing</td>';
}else{
echo '<td style=min-width:50px>Limited</td>';
};
?>
</tr>


<tr>
<?php
$bus2 = $showInfo['services']['2']['no'];
$arrtime2ms = $showInfo['services']['2']['next']['duration_ms'];
$arrtime2r = $arrtime2ms / '60000';
$arrtime2 = round($arrtime2r, 2);
$lat2 = $showInfo['services']['2']['next']['lat'];
$lon2 = $showInfo['services']['2']['next']['lng'];
$wb2r = $showInfo['services']['2']['next']['feature'];
$sd2r = $showInfo['services']['2']['next']['type'];
$arrtime2ms2 = $showInfo['services']['2']['next2']['duration_ms'];
$arrtime2r2 = $arrtime2ms2 / '60000';
$wb22r = $showInfo['services']['2']['next2']['feature'];
$sd22r = $showInfo['services']['2']['next2']['type'];
$arrtime22 = round($arrtime2r2, 2);
$arrtime2ms3 = $showInfo['services']['2']['next3']['duration_ms'];
$arrtime2r3 = $arrtime2ms3 / '60000';
$arrtime23 = round($arrtime2r3, 2);
$wb23r = $showInfo['services']['2']['next3']['feature'];
$sd23r = $showInfo['services']['2']['next3']['type'];
$seat2 = $showInfo['services']['2']['next']['load'];
if($arrtime2 > '1.5'){
echo '<td style=min-width:50px>'.$bus2.'</td><td style=min-width:50px>'.$arrtime2.'mins</td>';
}elseif(!isset($arrtime2ms)){
  echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus2.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd2r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd2r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb2r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
if($arrtime22 > '0'){
echo '<td style=min-width:50px>'.$arrtime22.'mins</td>';
}else{};
if($sd22r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd22r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb22r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
if($arrtime23 > '0'){
echo '<td style=min-width:50px>'.$arrtime23.'mins</td>';
}else{};
if($sd23r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd23r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb23r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($lon2 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat2.','.$lon2.'" target="_blank">Click here</a></td>';
}else{};
if($seat2 === 'SEA'){
echo '<td style=min-width:50px>Seating</td>';
}elseif($seat2 === 'SDA'){
echo '<td style=min-width:50px>Standing</td>';
}else{
echo '<td style=min-width:50px>Limited</td>';
};
?>
</tr>



<tr>
<?php
$bus3 = $showInfo['services']['3']['no'];
$arrtime3ms = $showInfo['services']['3']['next']['duration_ms'];
$arrtime3r = $arrtime3ms / '60000';
$arrtime3 = round($arrtime3r, 2);
$lat3 = $showInfo['services']['3']['next']['lat'];
$lon3 = $showInfo['services']['3']['next']['lng'];
$wb3r = $showInfo['services']['3']['next']['feature'];
$sd3r = $showInfo['services']['3']['next']['type'];
$arrtime3ms2 = $showInfo['services']['3']['next2']['duration_ms'];
$arrtime3r2 = $arrtime3ms2 / '60000';
$wb32r = $showInfo['services']['3']['next2']['feature'];
$sd32r = $showInfo['services']['3']['next2']['type'];
$arrtime32 = round($arrtime3r2, 2);
if($arrtime3 > '1.5'){
echo '<td style=min-width:50px>'.$bus3.'</td><td style=min-width:50px>'.$arrtime3.'mins</td>';
}elseif(!isset($arrtime3ms)){
  echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus3.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd3r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd3r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb3r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '</td>';
};
if($arrtime32 > '0'){
echo '<td style=min-width:50px>'.$arrtime32.'mins</td>';
}else{};
if($sd32r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd32r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb32r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($lon3 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat3.','.$lon3.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus4 = $showInfo['services']['4']['no'];
$arrtime4ms = $showInfo['services']['4']['next']['duration_ms'];
$arrtime4r = $arrtime4ms / '60000';
$arrtime4 = round($arrtime4r, 1);
$lat4 = $showInfo['services']['4']['next']['lat'];
$lon4 = $showInfo['services']['4']['next']['lng'];
$wb4r = $showInfo['services']['4']['next']['feature'];
$sd4r = $showInfo['services']['4']['next']['type'];
$arrtime4ms2 = $showInfo['services']['4']['next2']['duration_ms'];
$arrtime4r2 = $arrtime4ms2 / '60000';
$wb42r = $showInfo['services']['4']['next2']['feature'];
$sd42r = $showInfo['services']['4']['next2']['type'];
$arrtime42 = round($arrtime4r2, 1);
if($arrtime4 > '1.5'){
echo '<td style=min-width:50px>'.$bus4.'</td><td style=min-width:50px>'.$arrtime4.'mins</td>';
}elseif($easteregg === 'true'){
echo '<td style=min-width:50px>14</td>';
}elseif(!isset($arrtime4ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus4.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd4r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd4r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}elseif($easteregg === 'true'){
echo '<td style=min-width:50px>Never</td>';
}else{
echo '<td style=min-width:50px>';
};
if($wb4r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}elseif($easteregg === 'true'){
echo '<td style=min-width:50px>None</td>';
}else{};
if($arrtime42 > '0'){
echo '<td style=min-width:50px>'.$arrtime42.'mins</td>';
}elseif($easteregg === 'true'){
echo '<td style=min-width:50px>Never</td>';
}else{};
if($sd42r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd42r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}elseif($easteregg === 'true'){
echo '<td style=min-width:50px>None</td>';
}else{
echo '<td style=min-width:50px>';
};
if($wb42r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}elseif($easteregg === 'true'){
echo '<td style=min-width:50px>Nowhere</td>';
}else{
echo '';
};
if($lon4 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat4.','.$lon4.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus5 = $showInfo['services']['5']['no'];
$arrtime5ms = $showInfo['services']['5']['next']['duration_ms'];
$arrtime5r = $arrtime5ms / '60000';
$arrtime5 = round($arrtime5r, 1);
$lat5 = $showInfo['services']['5']['next']['lat'];
$lon5 = $showInfo['services']['5']['next']['lng'];
$wb5r = $showInfo['services']['5']['next']['feature'];
$sd5r = $showInfo['services']['5']['next']['type'];
$arrtime5ms2 = $showInfo['services']['5']['next2']['duration_ms'];
$arrtime5r2 = $arrtime5ms2 / '60000';
$wb52r = $showInfo['services']['5']['next2']['feature'];
$sd52r = $showInfo['services']['5']['next2']['type'];
$arrtime52 = round($arrtime5r2, 1);
if($arrtime5 > '1.5'){
echo '<td style=min-width:50px>'.$bus5.'</td><td style=min-width:50px>'.$arrtime5.'mins</td>';
}elseif(!isset($arrtime5ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus5.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd5r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd5r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb5r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime52 > '0'){
echo '<td style=min-width:50px>'.$arrtime52.'mins</td>';
}else{};
if($sd52r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd52r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb52r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon5 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat5.','.$lon5.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus6 = $showInfo['services']['6']['no'];
$arrtime6ms = $showInfo['services']['6']['next']['duration_ms'];
$arrtime6r = $arrtime6ms / '60000';
$arrtime6 = round($arrtime6r, 1);
$lat6 = $showInfo['services']['6']['next']['lat'];
$lon6 = $showInfo['services']['6']['next']['lng'];
$wb6r = $showInfo['services']['6']['next']['feature'];
$sd6r = $showInfo['services']['6']['next']['type'];
$arrtime6ms2 = $showInfo['services']['6']['next2']['duration_ms'];
$arrtime6r2 = $arrtime6ms2 / '60000';
$wb62r = $showInfo['services']['6']['next2']['feature'];
$sd62r = $showInfo['services']['6']['next2']['type'];
$arrtime62 = round($arrtime6r2, 1);
if($arrtime6 > '1.5'){
echo '<td style=min-width:50px>'.$bus6.'</td><td style=min-width:50px>'.$arrtime6.'mins</td>';
}elseif(!isset($arrtime6ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus6.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd6r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd6r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb6r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime62 > '0'){
echo '<td style=min-width:50px>'.$arrtime62.'mins</td>';
}else{};
if($sd62r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd62r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb62r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon6 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat6.','.$lon6.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus7 = $showInfo['services']['7']['no'];
$arrtime7ms = $showInfo['services']['7']['next']['duration_ms'];
$arrtime7r = $arrtime7ms / '60000';
$arrtime7 = round($arrtime7r, 1);
$lat7 = $showInfo['services']['7']['next']['lat'];
$lon7 = $showInfo['services']['7']['next']['lng'];
$wb7r = $showInfo['services']['7']['next']['feature'];
$sd7r = $showInfo['services']['7']['next']['type'];
$arrtime7ms2 = $showInfo['services']['7']['next2']['duration_ms'];
$arrtime7r2 = $arrtime7ms2 / '60000';
$wb72r = $showInfo['services']['7']['next2']['feature'];
$sd72r = $showInfo['services']['7']['next2']['type'];
$arrtime72 = round($arrtime7r2, 1);
if($arrtime7 > '1.5'){
echo '<td style=min-width:50px>'.$bus7.'</td><td style=min-width:50px>'.$arrtime7.'mins</td>';
}elseif(!isset($arrtime7ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus7.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd7r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd7r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb7r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime72 > '0'){
echo '<td style=min-width:50px>'.$arrtime72.'mins</td>';
}else{};
if($sd72r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd72r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb72r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon7 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat7.','.$lon7.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus8 = $showInfo['services']['8']['no'];
$arrtime8ms = $showInfo['services']['8']['next']['duration_ms'];
$arrtime8r = $arrtime8ms / '60000';
$arrtime8 = round($arrtime8r, 1);
$lat8 = $showInfo['services']['8']['next']['lat'];
$lon8 = $showInfo['services']['8']['next']['lng'];
$wb8r = $showInfo['services']['8']['next']['feature'];
$sd8r = $showInfo['services']['8']['next']['type'];
$arrtime8ms2 = $showInfo['services']['8']['next2']['duration_ms'];
$arrtime8r2 = $arrtime8ms2 / '60000';
$wb82r = $showInfo['services']['8']['next2']['feature'];
$sd82r = $showInfo['services']['8']['next2']['type'];
$arrtime82 = round($arrtime8r2, 1);
if($arrtime8 > '1.5'){
echo '<td style=min-width:50px>'.$bus8.'</td><td style=min-width:50px>'.$arrtime8.'mins</td>';
}elseif(!isset($arrtime8ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus8.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd8r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd8r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb8r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime82 > '0'){
echo '<td style=min-width:50px>'.$arrtime82.'mins</td>';
}else{};
if($sd82r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd82r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb82r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon8 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat8.','.$lon8.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus9 = $showInfo['services']['9']['no'];
$arrtime9ms = $showInfo['services']['9']['next']['duration_ms'];
$arrtime9r = $arrtime9ms / '60000';
$arrtime9 = round($arrtime9r, 1);
$lat9 = $showInfo['services']['9']['next']['lat'];
$lon9 = $showInfo['services']['9']['next']['lng'];
$wb9r = $showInfo['services']['9']['next']['feature'];
$sd9r = $showInfo['services']['9']['next']['type'];
$arrtime9ms2 = $showInfo['services']['9']['next2']['duration_ms'];
$arrtime9r2 = $arrtime9ms2 / '60000';
$wb92r = $showInfo['services']['9']['next2']['feature'];
$sd92r = $showInfo['services']['9']['next2']['type'];
$arrtime92 = round($arrtime9r2, 1);
if($arrtime9 > '1.5'){
echo '<td style=min-width:50px>'.$bus9.'</td><td style=min-width:50px>'.$arrtime9.'mins</td>';
}elseif(!isset($arrtime9ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus9.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd9r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd9r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb9r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime92 > '0'){
echo '<td style=min-width:50px>'.$arrtime92.'mins</td>';
}else{};
if($sd92r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd92r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb92r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon9 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat9.','.$lon9.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus10 = $showInfo['services']['10']['no'];
$arrtime10ms = $showInfo['services']['10']['next']['duration_ms'];
$arrtime10r = $arrtime10ms / '60000';
$arrtime10 = round($arrtime10r, 1);
$lat10 = $showInfo['services']['10']['next']['lat'];
$lon10 = $showInfo['services']['10']['next']['lng'];
$wb10r = $showInfo['services']['10']['next']['feature'];
$sd10r = $showInfo['services']['10']['next']['type'];
$arrtime10ms2 = $showInfo['services']['10']['next2']['duration_ms'];
$arrtime10r2 = $arrtime10ms2 / '60000';
$wb102r = $showInfo['services']['10']['next2']['feature'];
$sd102r = $showInfo['services']['10']['next2']['type'];
$arrtime102 = round($arrtime10r2, 1);
if($arrtime10 > '1.5'){
echo '<td style=min-width:50px>'.$bus10.'</td><td style=min-width:50px>'.$arrtime10.'mins</td>';
}elseif(!isset($arrtime10ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus10.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd10r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd10r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb10r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime102 > '0'){
echo '<td style=min-width:50px>'.$arrtime102.'mins</td>';
}else{};
if($sd102r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd102r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px>';
};
if($wb102r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon10 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat10.','.$lon10.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus11 = $showInfo['services']['11']['no'];
$arrtime11ms = $showInfo['services']['11']['next']['duration_ms'];
$arrtime11r = $arrtime11ms / '60000';
$arrtime11 = round($arrtime11r, 1);
$lat11 = $showInfo['services']['11']['next']['lat'];
$lon11 = $showInfo['services']['11']['next']['lng'];
$wb11r = $showInfo['services']['11']['next']['feature'];
$sd11r = $showInfo['services']['11']['next']['type'];
$arrtime11ms2 = $showInfo['services']['11']['next2']['duration_ms'];
$arrtime11r2 = $arrtime11ms2 / '60000';
$wb112r = $showInfo['services']['11']['next2']['feature'];
$sd112r = $showInfo['services']['11']['next2']['type'];
$arrtime112 = round($arrtime11r2, 1);
if($arrtime11 > '1.5'){
echo '<td style=min-width:50px>'.$bus11.'</td><td style=min-width:50px>'.$arrtime11.'mins</td>';
}elseif(!isset($arrtime11ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus11.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd11r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd11r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px><img src="https://i.imgur.com/hA3vkSQ.png">';
};
if($wb11r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime112 > '0'){
echo '<td style=min-width:50px>'.$arrtime112.'mins</td>';
}else{};
if($sd112r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd112r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px><img src="https://i.imgur.com/hA3vkSQ.png">';
};
if($wb112r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon11 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat11.','.$lon11.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


<tr>
<?php
$bus12 = $showInfo['services']['12']['no'];
$arrtime12ms = $showInfo['services']['12']['next']['duration_ms'];
$arrtime12r = $arrtime12ms / '60000';
$arrtime12 = round($arrtime12r, 1);
$lat12 = $showInfo['services']['12']['next']['lat'];
$lon12 = $showInfo['services']['12']['next']['lng'];
$wb12r = $showInfo['services']['12']['next']['feature'];
$sd12r = $showInfo['services']['12']['next']['type'];
$arrtime12ms2 = $showInfo['services']['12']['next2']['duration_ms'];
$arrtime12r2 = $arrtime12ms2 / '60000';
$wb122r = $showInfo['services']['12']['next2']['feature'];
$sd122r = $showInfo['services']['12']['next2']['type'];
$arrtime122 = round($arrtime12r2, 1);
if($arrtime12 > '1.5'){
echo '<td style=min-width:50px>'.$bus12.'</td><td style=min-width:50px>'.$arrtime12.'mins</td>';
}elseif(!isset($arrtime12ms)){
echo '<td style=min-width:50px>-</td>';
}else{
echo '<td style=min-width:50px>'.$bus12.'</td><td style=min-width:50px>Arriving</td>';
};
if($sd12r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd12r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px><img src="https://i.imgur.com/hA3vkSQ.png">';
};
if($wb12r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{};
if($arrtime122 > '0'){
echo '<td style=min-width:50px>'.$arrtime122.'mins</td>';
}else{};
if($sd122r === 'DD'){
echo '<td style=min-width:50px><i class="flaticon-101-double-decker-bus"></i>  ';
}elseif($sd122r === 'SD'){
echo '<td style=min-width:50px><i class="flaticon-056-bus-3"></i>  ';
}else{
echo '<td style=min-width:50px><img src="https://i.imgur.com/hA3vkSQ.png">';
};
if($wb122r === 'WAB'){
echo '<i class="material-icons">accessible</i></td>';
}else{
echo '';
};
if($lon12 > '0'){
echo '<td style=min-width:50px><a href="https://www.google.com.sg/maps/search/'.$lat12.','.$lon12.'" target="_blank">Click here</a></td>';
}else{};
?>
</tr>


</table>
<br>
<p class="center">This is in beta.</p>
<p class="center">The buses may be 30 seconds (or more during the peak) early or late.</p>
<p class="center">Due to limits on the number of bus slots, 800+ and 900+ buses may be cut off.</p>

    </div>
  </div>
 </div>



  <footer class="page-footer black">

    <div class="footer-copyright">
      <div class="container">
      © 2015-<?php echo date("Y");?> <a class="orange-text text-lighten-3" href="https://seetow.me">See Tow.</a> All Rights Reserved. <a class="orange-text text-lighten-3" href="/en/attribution">Attribution</a>. <? echo $_SERVER["HTTP_CF_CONNECTING_IP"]; ?>
      <div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'ja,ko,ms,ta,zh-CN', layout: google.translate.TranslateElement.FloatPosition.TOP_LEFT}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<div id="outdated"></div>
      </div>
    </div>
  </footer>

  <!--  Scripts-->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdn.rawgit.com/Hi1307/website/990ddbd1/seetow.me/htdocs/js/materialize.js"></script>
  <script src="https://cdn.rawgit.com/Hi1307/seetowcdn/903f5ebd/init.js"></script>
  <script>
jQuery(".changebg").click(function(){
  
  if ($(this).hasClass("checked")){
  $("body").css("background-color","white");
  $("table.striped > tbody > tr:nth-child(odd)").css("background-color","#f2f2f2");
  $("nav").removeClass("grey");
  $("nav").addClass("white");
  $("body").css("color","black");
  $(this).removeClass("checked");
  }
  
  else
    
    {

      $("body").css("background-color","black");
      $("table.striped > tbody > tr:nth-child(odd)").css("background-color","black");
      $("nav").removeClass("white");
      $("nav").addClass("grey");
      $("body").css("color","white");
      $(this).addClass("checked")}
});
</script>

  </body>
</html>
