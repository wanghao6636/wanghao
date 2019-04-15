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
        $xml_str=file_get_contents('php://input');
        $log_str=date('Y-d-m H:i:s');
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
        $xml_obj=simplexml_load_string($xml_str);

        $msg_type=$xml_obj->MsgType;
        if($msg_type=='image'){
            //获取文件扩展
            $url=$xml_obj->PicUrl;
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
        }

    }
    public function accesstoken()
    {
        $redis_key='access_token';
        $token=Redis::get($redis_key);
        if($token){

        }else{
            $appid="wxf45738393e3e870a";
            $secret="04c57ee962b7bf78d85050ce9d213833";
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=.$appid.&secret=.$secret.";
            $json_str=file_get_contents($url);
            $arr=json_decode($json_str,true);
            Redis::get($redis_key,$arr['access_token']);
            Redid::expire($redis_key,3600);
            $token=$arr['access_token'];
        }
    }
}