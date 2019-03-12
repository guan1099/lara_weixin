<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
class WxUserModel extends Model
{
    //
    public $table='p_wx_users';
    public $timestamps=false;

    protected static $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    //
    /**
     * 获取access_token
     * @return mixed
     */
    public static function getAccessToken(){
        //获取缓存
        $token = Redis::get(self::$redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APP_ID').'&secret='.env('WX_SECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set(self::$redis_weixin_access_token,$token);
            Redis::setTimeout(self::$redis_weixin_access_token,3600);
        }
        return $token;
    }

}
