<?php
$head_httpdvb = "http://httpdvb.slave.shuliyun.com:13164/playurl";

function getinfo_json($chnlid,$token){
    $i_url = 'http://slave.shuliyun.com/media/channel/get_info?chnlid=4200000'.$chnlid.'&accesstoken='.$token;
    $i_result = file_get_contents($i_url);
    return json_decode($i_result);
}

$accesstoken='G8E222BE4V21307D1TBC062A6JF7E607ABM7B61CDEDID068875K98BDB0';
$id=isset($_GET['id'])?$_GET['id']:'001';

$json = getinfo_json($id,$accesstoken);
$playtoken = isset($json->play_token)?$json->play_token:'ABCDEFGHI';
$playurl=$head_httpdvb.'?playtype=live&protocol=hls&accesstoken='.$accesstoken.'&programid=4200000'.$id.'&playtoken='.$playtoken;
header("location: ".$playurl);
?>