<?php
namespace App\Http\Controllers\wxController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\User\WxUserModel;
use GuzzleHttp\Client;
class WxController extends Controller{

   /*  public function valid()
    {
        echo $_GET['echostr'];
    } */
    public function Wxevent()
    {
        //接收微信推送
        $content=file_get_contents("php://input");
        $time=date('Y-m-d H:i:s');
        $str=$time.$content."\n";
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        echo "Success";
    }
}