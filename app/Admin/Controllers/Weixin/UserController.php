<?php

namespace App\Admin\Controllers\Weixin;

use App\Model\WxUserModel;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use GuzzleHttp\Client;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WxUserModel);

        $grid->id('Id');
        $grid->openid('Openid');
        $grid->add_time('Add time')->display(function($time){
            return date('Y-m-d H:i:s',$time);
        });
        $grid->nickname('Nickname');
        $grid->sex('Sex')->display(function($sex){
            if($sex==1){
                return '男';
            }else if($sex==2){
                return '女';
            }else{
                return '待定';
            }
        });
        $grid->headimgurl('Headimgurl')->display(function($img){
            return '<img src='.$img.'>';
        });
        $grid->subscribe_time('Subscribe time');
        $grid->unionid('Unionid');
        $grid->uid('Uid');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxUserModel::findOrFail($id));

        $show->id('Id');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');
        $show->unionid('Unionid');
        $show->uid('Uid');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WxUserModel);

        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->text('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');
        $form->text('unionid', 'Unionid');
        $form->switch('uid', 'Uid');

        return $form;
    }
    public function msg(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body(view('admin.weixin.sendmsg'));
    }
    public function sendmsg()
    {
        $msg=$_POST['msg'];
        $arr=WxUserModel::all()->toArray();
        $openid=array_column($arr,'openid');
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.WxUserModel::getAccessToken().'';
        $data=[
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$msg
            ]
        ];
        $client=new Client();
        $r=$client->request('post',$url,['body'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
        //解析接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        var_dump($response_arr);
        if($response_arr['errcode']==0){
            echo "群发成功";
        }else{
            echo "群发失败，请重试";
            echo "<br/>";
        }
    }
    public function menu(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body(view('admin.weixin.menu'));
    }
    public function domenu(){
        $one=$_POST['one'];
        $two=$_POST['two'];
        $url=' https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.WxUserModel::getAccessToken().'';
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data = [
            "button"    => [
                [
                    "type"  => "click",      // view类型 跳转指定 URL
                    "name"  => "客服",
                    "key"   => "kefu"
                ],
                [
                    "name"=>"菜单",
                    "sub_button"=>[
                        [
                            "type"=>"view",
                            "name"=>"搜索",
                            "url"=>"http://www.soso.com/"
                        ],
                        [
                            "type"=>"view",
                            "name"=>"QQ音乐",
                            "url"=>"http://www.qqmusic.com/"
                        ]
                    ],
                ],
                [
                    "type"  => "view",      // view类型 跳转指定 URL
                    "name"  => "项目",
                    "url"   => "https://qi.tactshan.com"
                ]
            ]
        ];
        $r=$client->request('POST', $url, [
            'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);
        // 3 解析微信接口返回信息

        $response_arr = json_decode($r->getBody(),true);
        //echo '<pre>';print_r($response_arr);echo '</pre>';die;

        if($response_arr['errcode'] == 0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";echo '</br>';
            echo $response_arr['errmsg'];

        }
    }
    public function wxevent(){

    }
}
