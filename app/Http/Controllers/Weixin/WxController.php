<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\WxUserModel;
use GuzzleHttp\Client;
class WxController extends Controller
{


    /**
     * 获取用户信息
     * @param $openid
     * @return mixed
     */
    public function getUserInfo($openid)
    {
        $access_token = WxUserModel::getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $data = json_decode(file_get_contents($url),true);
        //echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }
    public function getTag(){
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.WxUserModel::getAccessToken().'';
        $data=[
            'tag'=>['name'=>'山西']
        ];
        $client=new Client();
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
    }
    public function test(){
        $url='http://www.api.com/a.php';
        $client=new Client();
        $response=$client->request('GET',$url);
        $info=$response->getBody();
        echo $info;
    }
}
