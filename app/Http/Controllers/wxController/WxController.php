<?php
namespace App\Http\Controllers\wxController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;   
use App\Model\User\WxUserModel;
use GuzzleHttp\Client;
class WxController extends Controller{

    public function valid()
    {
        echo $_GET['echostr'];
    } 
    public function wxEvent()
    {
        $xml_str=file_get_contents("php://input");
        $log_str=date('Y-m-d H:i:s');
        $str=$xml_str.$log_str.'\n';
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $xml_obj=simplexml_load_string($xml_str);
       // var_dump($xml_obj);exit;
        $open_id=$xml_obj->FromUserName;  //open的id
        $app=$xml_obj->ToUserName;  
        $msg_type=$xml_obj->MsgType;
        if($msg_type=='image'){
            //获取文件扩展
            
            $url=$xml_obj->PicUrl;
            //var_dump($url);exit;
            $response=$client->get(new Uri($url));
            $img=file_get_contents($xml_obj->PicUrl);
            $file_time=time().md_brand(11111,99999).'.jpg';
            $re=file_put_contents('wx/images/'.$file_name,$img);
        }elseif($msg_type=='voice'){
            $media_id=$xml_obj->MediaId;
            $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID';
            file_get_contents($url);
            $file_name=time().md_rand(11111,99999).'.amr';
            $rr=file_put_contents('wx/voice/'.$file_name,$amr);
        }elseif($msg_type=='text'){
            //自动回复
            if(strpos($xml_obj->Content,'+天气')){
            //echo 111;exit;
            //先获取城市名
            $city=explode('+',$xml_obj->Content)[0];
            $url="https://free-api.heweather.net/s6/weather/now?location=$city&key=HE1904170859031675";
            //var_dump($url);exit;
            $arr=json_decode(file_get_contents($url),true);
            $fl=$arr['HeWeather6'][0]['now']['fl'];  //是摄氏度
            $win_dir=$arr['HeWeather6'][0]['now']['wind_dir'];  //风向
            $wind_sc=$arr['HeWeather6'][0]['now']['wind_sc'];  //风力
            $hum=$arr['HeWeather6'][0]['now']['hum'];		//湿度
            $data='温度:'.$fl."\n".'风向:'.$win_dir."\n".'风力:'.$wind_sc."\n".'湿度:'.$hum."\n"; 
            $time=time();
            $response_xml=
            "<xml>
            <ToUserName><![CDATA['.$open_id.']]></ToUserName>
            <FromUserName><![CDATA['.$app.']]></FromUserName>
            <CreateTime>'.$time.'</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA['.$data.']]></Content>
            </xml>";
            //var_dump($response_xml);exit;
            
       
        }else{
            $response_xml=
            "<xml>
            <ToUserName><![CDATA['.$open_id.']]></ToUserName>
            <FromUserName><![CDATA['.$app.']]></FromUserName>
            <CreateTime>'.$time.'</CreateTime>
            <MsgType><![CDATA[text]]></MsgType>
            <Content><![CDATA[城市名称不正确]]></Content>
            </xml>";
        }
    }
        


       // echo $response_xml;







        /* if($event=='subscribe'){
            //判断用户是否存在
            $luser=WxUserModel::where(['openid'=>$openid])->first();
            //之前的用户关注
            if($luser){
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎回来 '. $local_user['nickname'] .']]></Content></xml>';
            }else{
                //得到用户信息
                $niu=$this->UserInfo($openid);
                //信息入数据库
                $niu_fo=[
                    'openid'=>$niu['openid'],
                    'nickname'=>$niu['nickname'],
                    'sex'=>$niu['sex'],
                    'headimgur'=>$niu['headimgur'],
                ];
           
            }
        } */
        

    }
    public function accesstoken()
    {
        $redis_key='access_token';
        
        $token=Redis::get($redis_key);
        
        if($token){

        }else{
            $appid="wxf45738393e3e870a";
            $secret="04c57ee962b7bf78d85050ce9d213833";
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            
            $json_str=file_get_contents($url);
            $arr=json_decode($json_str,true);
            //svar_dump($arr);exit;
            Redis::get($redis_key,$arr['access_token']);
            Redis::expire($redis_key,3600);
            $token=$arr['access_token'];
            
       }
       return $token;
    }
    


}