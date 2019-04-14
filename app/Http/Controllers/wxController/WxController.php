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
    public function Wxevent()
    {
        //接收微信推送
        $content = file_get_contents("php://input");
        $time=date('Y-m-d H:i:s');
        $str=$time.$content."\n";
        //var_dump($content);exit;
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        //echo "Success";
        $data=simplexml_load_string($content);
        //公众号的id
        $wx_id=$data->ToUserName;   
        //用户的opid
        $openid=$data->FromUserName; 
        //事件
        $event=$data->Event;  
        //扫码判断
        if($event=='subscribe'){
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
                $id=WxUserModel::insertGetId($niu_fo);
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $u['nickname'] .']]></Content></xml>';
            }
        }
        
    }
    
    //获取微信的accesstoken
    public function accesstoken()
    {
        //查看是否有缓存
        $key='access_token';
        $token=Redis::get($key);
        if($token){

        }else{
            $appid="wxf45738393e3e870a";
            $secret="04c57ee962b7bf78d85050ce9d213833";
            $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
            $response = file_get_contents($url);
            $arr=json_decode($response,true);
            
            //缓存
            Redis::get($key,$arr['access_token']);
            Redis::expire($key,10); //缓存时间10秒
            $token=$arr['access_token'];
        }
        return $token; 
    }

    public function test()
    {
        $access_token=$this->accesstoken();
        echo 'token:'.$access_token;echo '</br>';
    }

    public function UserInfo()
    {
        
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->accesstoken().'&openid='.$openid.'&lang=zh_CN';
    }
   
}