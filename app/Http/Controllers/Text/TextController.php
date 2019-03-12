<?php

namespace App\Http\Controllers\Text;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\WeixinUser;
use GuzzleHttp\Client;

class TextController extends Controller
{
    //aaa
    protected $redis_weixin_access_token='str:weixin_access_token';
    protected $redis_weixin_userinfo='str:weixin_userinfo';

    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }
    public function getAccessToken(){
        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APP_ID').'&secret='.env('WX_SECRET');
            $data = json_decode(file_get_contents($url),true);
            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;
    }
    //获取用户信息
    public function getUserInfo($openid){
            $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->getAccessToken().'&openid='.$openid.'&lang=zh_CN';
            $data=json_decode(file_get_contents($url),true);
            $id=$data['openid'];
            $user_info=[
                'openid'=>$data['openid'],
                'nickname'=>$data['openid'],
                'sex'=>$data['sex'],
                'headimgurl'=>$data['headimgurl'],
                'subscribe_time'=>$data['subscribe_time']
            ];
            Redis::set($this->redis_weixin_userinfo.$id,$user_info);
            Redis::setTimeout($this->redis_weixin_userinfo.$id,3600);
        return $data;
    }
    public function wxEvent(){
        $data = file_get_contents("php://input");
        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/text_event.log',$log_str,FILE_APPEND);
        $event = $xml->Event;                       //事件类型
        //var_dump($xml);echo '<hr>';
        $openid = $xml->FromUserName;               //用户openid
        $sub_time = $xml->CreateTime;               //扫码关注时间
        if(isset($xml->MsgType)){
            if($xml->MsgType=='event') {
                if ($event == 'subscribe') {
                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);
                    //保存用户信息
                    $u = WeixinUser::where(['openid' => $openid])->first();
                    if ($u) {       //用户不存在
                        echo '用户已存在';
                    } else {
                        $user_data = [
                            'openid' => $openid,
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                            'type'=>1
                        ];
                        $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[欢迎首次关注！]]></Content></xml>';
                        echo $xml_response;
                    }
                }
            }
        }
    }
    public function userList(){
        $arr=WeixinUser::where(['type'=>1])->get()->toArray();
        $data=[
            'list'=>$arr
        ];
        return view('text.text',$data);
    }
    public function getTag(){
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$this->getAccessToken().'';
        $data=[
            'tag'=>['name'=>'山东']
        ];
        $client=new Client();
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
    }
    public function lahei($id){
        $data=[
            'type'=>2
        ];
        $rs=WeixinUser::where(['id'=>$id])->update($data);
        if($rs){
            echo '添加成功';
        }else{
            echo '添加失败';
        }
    }
    public function taglist(){
        $url='https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$this->getAccessToken().'';
        $data=json_decode(file_get_contents($url),true);
        return $data;
    }
    public function getusertag(){
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->getAccessToken().'';
        $arr=WeixinUser::where(['type'=>1])->get()->toArray();
        $id=array_column($arr,'openid');
        $iid=implode($id,',');
        print_r($iid);
        $data=[
            'openid_list'=>$iid,
            'tagid'=>101
        ];
        $client=new Client();
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo '添加成功';
        }else{
            echo "添加失败";
        }
    }
}
