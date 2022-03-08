<?php
$head = "http://kid.hfcatv.com.cn:13164";

function getinfo_json($chnlid,$token){
    $i_url = 'http://kid.hfcatv.com.cn:13164/media/channel/get_info?chnlid=4200000'.$chnlid.'&accesstoken='.$token.'&verifycode=2000000576';
    $i_result = file_get_contents($i_url);
    return json_decode($i_result);
}

$accesstoken='R5F15C63AU30947030K77359BACI93B1EF78P8M2FAF228V0Z53085W162382CFCB4DC7EC';

$id=isset($_GET['id'])?$_GET['id']:'124';
$type=isset($_GET['type'])?$_GET['type']:'live';

if($type=='live'){
    //直播
    $json = getinfo_json($id,$accesstoken);
    $playtoken = isset($json->play_token)?$json->play_token:'ABCDEFGHI';
    $playurl=$head.'/playurl?playtype=live&protocol=hls&verifycode=2000000576&accesstoken='.$accesstoken.'&programid=4200000'.$id.'&playtoken='.$playtoken;
    header("location: ".$playurl);
}else if($type=='epg'){
    //节目单
    $date=isset($_GET['date'])?$_GET['date']:date('Y-m-d');
    $time = time();
    $json = getinfo_json($id,$accesstoken);
    echo $json->chnl_name." ".$date." 节目单<br/>";
    $epg_url='http://kid.hfcatv.com.cn:13164/media/event/get_list?chnlid=4200000'.$id.'&pageidx=1&vcontrol=0&attachdesc=1&repeat=1&accesstoken='.$accesstoken.'&starttime='.strtotime($date).'&endtime='.strtotime('+1 day',strtotime($date)).'&pagenum=100&flagposter=0&verifycode=2000000576';
    $epg_result = file_get_contents($epg_url);
    $epg_json = json_decode($epg_result);
    $event_list=$epg_json->event_list;
    $php_Self = substr($_SERVER['PHP_SELF'],strripos($_SERVER['PHP_SELF'],"/")+1);
    for ($x=0; $x<count($event_list); $x++) {
        $url=$php_Self.'?type=back&start='.date('YmdHis',$event_list[$x]->start_time).'&end='.date('YmdHis',$event_list[$x]->end_time).'&event_id='.$event_list[$x]->event_id;
        $n=date('H:i',$event_list[$x]->start_time).' '.$event_list[$x]->event_name;
        if(number_format($time)>number_format($event_list[$x]->end_time)){
            echo "<a href='{$url}' title=''>$n</a><br/>";
        }else{
            echo $n."<br/>";
        }
    }
}else if($type=='back'){
    //回看
    $start=$_GET['start'];
    $end=$_GET['end'];
    if(isset($_GET['event_id'])){
        $eventid=$_GET['event_id'];
        $url=$head.'/media/event/get_info?accesstoken='.$accesstoken.'&eventid='.$eventid.'&verifycode=2000000576';
        $result = file_get_contents($url);
        $json = json_decode($result);
        $playtoken = $json->play_token;
    }else{
        $json = getinfo_json($id,$accesstoken);
        $eventid=$json->pf_info[0]->id;
        $playtoken = isset($json->play_token)?$json->play_token:'ABCDEFGHI';
    }
    $playurl=$head.'/playurl?playtype=lookback&protocol=hls&verifycode=2000000576&starttime='.$start.'&endtime='.$end.'&accesstoken='.$accesstoken.'&programid='.$eventid.'&playtoken='.$playtoken;
    header("location: ".$playurl);
}
?>