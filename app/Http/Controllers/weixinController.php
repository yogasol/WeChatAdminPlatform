<?php
namespace App\Http\Controllers;


//define your token
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;



class weixinController extends Controller
{
    public function checkSignature(Request $request)
    {
//1.将timestamp(时间戳),nonce(随机数),token(),signature(微信加密签名，结合了开发者填写的token参数和请求中的timestamp,nonce参数)echostr(随机字符串) 按字典排序
        $timestamp = $request->get('timestamp');
        $nonce = $request->get('nonce');
        $token = 'weixin';
        $echostr = $request->get('echostr');
        $signature = $request->get('signature');
        $array = array($timestamp, $nonce, $token);
        sort($array);
        //2.将排序后的三个参数拼接之后用sha1加密
        $tmpStr = implode('', $array);
        $tmpStr = sha1($tmpStr);
        //3.将加密后的字符串与signture进行对比，判断该请求是否来自微信
        if ($tmpStr == $signature) {
            header('Content-type:text');
            //第一次接入微信接口时
            echo $echostr;
            exit;
        } else {
            $this->responseMsg($request);
        }
    }

    //接收事件推送并回复
    public function responseMsg(Request $request)
    {

        //1.获取到微信推送过来post数据（xml）格式 get post data, May be due to the different environments
        $postStr = $request->getContent();

        //2.处理消息类型，并回复类型和内容 extract post data
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        //判断该数据包是否是订阅的事件推送
        if (strtolower($postObj->MsgType) == 'event') {
            //如果是关注subscribe事件
            if (strtolower($postObj->Event) == 'subscribe') {
                //回复用户消息(纯文本格式)
                //回复用户信息
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                // $keyword = trim($postObj->Content);
                $time = time();
                $msgType = "text";
                $contentStr = "嘎!";
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;

            }
            //如果是重扫二维码
            if(strtolower($postObj->Event)=='scan'){
                //回复用户信息
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                // $keyword = trim($postObj->Content);
                $time = time();

                if(strtolower($postObj->EventKey)==2000){
                    //如果是临时二维码

                    $contentStr = "临时二维码你又来了!";
                }

                if(strtolower($postObj->EventKey)==3000){
                    //如果是永久二维码

                    $contentStr = "永久二维码你也来了!";
                }

                $msgType = "text";
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;

            }

            if (strtolower($postObj->Event) == 'click') {
                //如果是自定义菜单中的单点击事件
                if (strtolower($postObj->EventKey) == 'item1') {
                    $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";

                    $fromUser = $postObj->ToUserName;
                    $toUser = $postObj->FromUserName;
                    $time = time();
                    $content = 'gagaga';
                    $msgType = 'text';
                    $result = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                    echo $result;
                }
                if (strtolower($postObj->EventKey) == 'songs') {
                    $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";

                    $fromUser = $postObj->ToUserName;
                    $toUser = $postObj->FromUserName;
                    $time = time();
                    $content = 'gagaga';
                    $msgType = 'text';
                    $result = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                    echo $result;
                }

            }
        }

        //纯文本
//        if (strtolower($postObj->MsgType) == 'text'&&trim($postObj->Content)=='ga') {
//                $template = "<xml>
//                            <ToUserName><![CDATA[%s]]></ToUserName>
//                            <FromUserName><![CDATA[%s]]></FromUserName>
//                            <CreateTime>%s</CreateTime>
//                            <MsgType><![CDATA[%s]]></MsgType>
//                            <Content><![CDATA[%s]]></Content>
//                            </xml>";
//
//                $fromUser = $postObj->ToUserName;
//                $toUser = $postObj->FromUserName;
//                $time = time();
//                $content = 'gagaga';
//                $msgType = 'text';
//                $result = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
//                echo $result;
//
//        } else {
//            echo "gagag";
//        }


//回复图文信息
        if (strtolower($postObj->MsgType) == 'text' && trim($postObj->Content) == 'tuwen1') {
            $toUser = $postObj->FromUserName;
            $fromUser = $postObj->ToUserName;
            $arr = array(
                array(
                    'title' => '海绵宝宝官网',
                    'description' => "spongebob",
                    'picUrl' => '7http://static.googleadsserving.cn/pagead/imgad?id=CICAgKDr6ejW2QEQrAIY-gEyCEtjPYO_CxL',
                    'url' => 'http://www.nick-asia.com/?username=spongebob',
                ),
                array(
                    'title' => '海绵宝宝官网',
                    'description' => "spongebob",
                    'picUrl' => 'http://static.googleadsserving.cn/pagead/imgad?id=CICAgKDr6ejW2QEQrAIY-gEyCEtjPYO_Cx7L',
                    'url' => 'http://www.nick-asia.com/?username=spongebob',
                ),
                array(
                    'title' => '海绵宝宝官网',
                    'description' => "spongebob",
                    'picUrl' => 'http://static.googleadsserving.cn/pagead/imgad?id=CICAgKDr6ejW2QEQrAIY-gEyCEtjPYO_Cx7L',
                    'url' => 'http://www.nick-asia.com/?username=spongebob',
                ),
            );
            $template = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>" . count($arr) . "</ArticleCount>
						<Articles>";
            foreach ($arr as $k => $v) {
                $template .= "<item>
							<Title><![CDATA[" . $v['title'] . "]]></Title> 
							<Description><![CDATA[" . $v['description'] . "]]></Description>
							<PicUrl><![CDATA[" . $v['picUrl'] . "]]></PicUrl>
							<Url><![CDATA[" . $v['url'] . "]]></Url>
							</item>";
            }

            $template .= "</Articles>
						</xml> ";
            echo sprintf($template, $toUser, $fromUser, time(), 'news');


        } else {
            
            if (strtolower($postObj->MsgType) == 'text') {
//                switch( trim($postObj->Content) ) {
//                    case 'ga':
//                        $content = 'gagaga';
//                        break;
//                    case 'ha':
//                        $content = 'hahahaha';
//                        break;
//                    case 'wa':
//                        $content = '呜哇';
//                        break;
//                    case '海绵宝宝':
//                        $content = "<a href='http://tv.sohu.com/s2013/spongebobsquarepants/'>海绵宝宝</a>";
//                        break;
//                    default:
//                        $content='bye';
//                        break;
//                }


                $ch = curl_init();
                $url = "https://api.seniverse.com/v3/weather/now.json?key=hhxfejieestheoip&location=".urlencode($postObj->Content)."&language=zh-Hans&unit=c" ;     //获取数据的请求地址

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
                //执行HTTP请求
                curl_setopt($ch, CURLOPT_URL, $url);
                $res = curl_exec($ch);
                $arr = json_decode($res, true);
                $content = $arr['results']['0']['location']['name'] . "\n温度：" . $arr['results']['0']['now']['temperature'] . "\n数据更新时间：" . $arr['results']['0']['last_update'] . "\n天气状况：" . $arr['results']['0']['now']['text'];

                $template = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";

                $fromUser = $postObj->ToUserName;
                $toUser = $postObj->FromUserName;
                $time = time();
                //  $content = 'gagaga';
                $msgType = 'text';
                $result = sprintf($template, $toUser, $fromUser, $time, $msgType, $content);
                echo $result;
            }

        }

    }
//    public function http_curl(){
//        //获取
//        //1.初始化baidu
//        $ch=curl_init();
//        $url="http://www.baidu.com";
//        //2.设置curl的参数
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        //3.采集
//        $output=curl_exec($ch);
//        //4.关闭
//        curl_close($ch);
//        var_dump($output);
//    }
    /**
     *$url 接口url string
     *$type 请求类型string
     *$res 返回数据类型 string
     *$url post请求参数string
     */

    public function http_curl($url, $type = 'get', $res = 'json', $arr = '')
    {
        //1.初始化baidu
        $ch = curl_init();
        //2.设置curl的参数

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        if ($type == 'post') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
//
//        curl_setopt($ch, CURLOPT_SAFE_UPLOAD, TRUE);    //将CURL_SAFE_UPLOAD设置为TRUE
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        }
        //3.采集
        $output = curl_exec($ch);
        //4.关闭

        if ($res == 'json') {
            //            return json_decode($output,true);
            if (curl_errno($ch)) {
                //请求失败，返回错误信息
                return curl_error($ch);
            } else {
                //请求成功
                return json_decode($output, true);
            }

        }
        curl_close($ch);
    }


    //获取


    //获取AccessToken
//    function getWxAccessToken(){
//        //1.请求url地址
//         $appid = config('app.appid');
//            $appsecret = config('app.appsecret');
//        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
//        //2初始化
//        $ch = curl_init();
//        //3.设置参数
//        curl_setopt($ch , CURLOPT_URL, $url);
//        curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
//        //4.调用接口
//        $res = curl_exec($ch);
//
//        if( curl_errno($ch) ){
//            var_dump( curl_error($ch) );
//        }
//        //5.关闭curl
//        curl_close( $ch );
//        $arr = json_decode($res, true);
//        var_dump( $arr );
//    }

//返回access_token *session解决方法 存mysql memcache
    public function getWxAccessToken()
    {
        //将access_token 存在session/cookie中
        if (Session::put('access_token') && Session::put('exprire_time') > time()) {
            //如果access_token存在session/cookie中
            return Session::get('access_token');
        } else {
            //如果access_token不存在或已经过期，重新取access_token
            $appid = config('app.testappid');
            $appsecret = config('app.testappsecret');
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
            $res = $this->http_curl($url, 'get', 'json');
            $access_token = $res['access_token'];
            //将重新获取到的accss_token存到session
            Session::put('access_token', $access_token);
            Session::put('expire_time', time() + 7000);

            return $access_token;
        }
    }

    public function definedItem()
    {
        //创建微信菜单
        //目前微信接口的调用方式都是通过curl post/get
        header('content-type:text/html;charset=utf-8');
        $access_token = $this->getWxAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $access_token;
        $postArr = array(
            'button' => array(
                array(
                    'name' => urlencode('菜单一'),
                    'type' => 'click',
                    'key' => 'item1',
                ),//第一个一级菜单
                array(
                    'name' => urlencode('菜单二'),
                    'sub_button' => array(
                        array(
                            'name' => urlencode('歌曲'),
                            'type' => 'click',
                            'key' => 'songs',
                        ), //第一个二级菜单
                        array(
                            'name' => urlencode('电影'),
                            'type' => 'view',
                            'url' => 'http://www.baidu.com',
                        ),//第二个二级菜单
                    )
                ),//第二个一级菜单
                array(
                    'name' => urlencode('菜单三'),
                    'type' => 'view',
                    'url' => 'http://www.qq.com',

                ),//第三个一级菜单
            ),

        );
        echo "<hr />";
        var_dump($postArr);
        echo "<hr />";
        echo $postJson = urldecode(json_encode($postArr));


        $res = $this->http_curl($url, 'post', 'json', $postJson);
        dd($res);
    }


    //获取ip地址(判断ip是否在微信服务器ip地址列表里，用于安全性检测)
    function getWxServerIp()
    {
        $accessToken = "11_sFdnJKHtAocKlSo1H1Nn6U90sq4QO8aWdwc1jz4fW1h0UdwSjkeH4vYPirw-xe0PyLUl6rB6986Tdohci3Z8VUsIX34aCPOT6YLEZb8Y_8cQWVy6f9Tw5__DMXVRTfeFZaHQjnQJAOAkIUhGHZQaACATLC";
        $url = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=" . $accessToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            var_dump(curl_error($ch));
        }
        curl_close($ch);
        $arr = json_decode($res, true);
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
    }


//群发消息（测试号一天只有100次啊，不要乱搞）
public  function sendMsgAll(){
        //1.获取全局access_token
        echo $access_token=$this->getWxAccessToken();
        echo "<hr/>";
        $url="https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$access_token;
        //2.组装群发接口数据array
    //单图文
//    $array=array(
//        'touser'=>'o7jRy0WYciVYzpwmolaVeaddb3o0',
//        'image'=>array('media_id'=>'wzMrjhmfx5rufKx_71XxJzjMPkiy-8k_WkZPPdiF4TmfPjqAwQiQVuS00W1yofc5'),
//        'msgtype'=>'image',
//    );
//    //单文本2
        $array=array(
            'touser'=>'o7jRy0WYciVYzpwmolaVeaddb3o0',//微信用户的openid
            'text'=>array('content'=>'ga'),//文本内容
            'msgtype'=>'text',//消息类型
         );
        //3.将array->json
        $postJson=json_encode($array);
        var_dump($postJson);
        echo "<hr/>";
        //4.调用curl
        $res=$this->http_curl($url,'post','json',$postJson);
        var_dump($res);

     }


     //基础授权登陆
    //获取用户的openid
    function getBaseInfo(){
        //1.获取到code
        $appid=config('app.testappid');
        $redirect_uri=urlencode("http://wechat.yogasol.xin/getUserOpenId");
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('location:'.$url);
        exit();
    }

    function getUserOpenId(Request $request){
        //2.获取到网页授权的access_token
        $appid=config('app.testappid');
        $appsecret=config('app.testappsecret');
        $code=$request->get('code');
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
        //3.拉取用户的openid
        $res=$this->http_curl($url,'get');
        var_dump($res);
     }

    function getUserDetail(){
        //1.获取到code
        $appid=config('app.testappid');
        $redirect_uri=urlencode("http://wechat.yogasol.xin/getUserInfo");
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
        header('location:'.$url);
        exit();

    }

    function getUserInfo(Request $request){
        //2.获取到网页授权的access_token
        $appid=config('app.testappid');
        $appsecret=config('app.testappsecret');
        $code=$request->get('code');
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$appsecret."&code=".$code."&grant_type=authorization_code";
        $res=$this->http_curl($url,'get');
        $access_token=$res['access_token'];
        //3.拉取用户的openid
        $openid=$res['openid'];
        //拉取用户的详细信息
        $url=" https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res=$this->http_curl($url,'get');
        var_dump($res);

    }

    //分享接口实现一



    //模板消息，用于用户下单，消费等
    public function sendTemplateMsg(){
        //1.获取到access_token
        //一个账号当日模板消息不超过100000次
        $access_token=$this->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
        //2.组装数组
        $array=array(
            'touser'=>config('app.touser'),
            'template_id'=>config('app.template_id'),
            'url'=>'http://static.googleadsserving.cn/pagead/imgad?id=CICAgKDr6ejW2QEQrAIY-gEyCEtjPYO_CxL',
            'data'=>array(
                'name'=>array('value'=>'商品名称：spongebob','color'=>'#173177'),
                'money'=>array('value'=>'商品价格：100','color'=>'#173177'),
                'date'=>array('value'=>'购买时间：'.date('Y-m-d H:i:s'),'color'=>'#173177')
            ),
        );
        //3.将数组->json
        $postJson=json_encode($array);
        //4.调用curl函数
        $res=$this->http_curl($url,'post','json',$postJson);
        dd($res);
    }


    //生成临时二维码
    function getTimeOrCode(){
        //1.获取ticket票据
        //全局票据access_token 网页授权access_token 微信js-SDK jsapi_ticket
        $access_token=$this->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        //{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr=array(
            'expire_seconds'=>604800, //24*60*60*7
            'action_name'=>"QR_SCENE",
            'action_info'=>array(
                'scene'=>array('scene_id'=>2000),
            ),
        );
        $postJson=json_encode($postArr);
        $res=$this->http_curl($url,'post','json',$postJson);
        $ticket=$res['ticket'];
        //2.使用ticket来获取二维码图片
        $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        // $res=$this->http_curl($url,'get');
        echo "<img src='".$url."'/>";

    }

    //生成永久二维码
    public function getForeverOrCode(){
        //1.获取ticket票据
        //全局票据access_token 网页授权access_token 微信js-SDK jsapi_ticket
        $access_token=$this->getWxAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        //{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr=array(
            'action_name'=>"QR_LIMIT_SCENE",
            'action_info'=>array(
                'scene'=>array('scene_id'=>3000),
            ),
        );
        $postJson=json_encode($postArr);
        $res=$this->http_curl($url,'post','json',$postJson);
        $ticket=$res['ticket'];
        //2.使用ticket来获取二维码图片
        $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);
        // $res=$this->http_curl($url,'get');
        echo "<img src='".$url."'/>";
    }

//获取jsapi_ticket全局票据
    function getJsApiTicket(){
        //如果session中保存有效的jsapi_ticket
        if (Session::put('jsapi_ticket') && Session::put('jsapi_ticket_expire_time') > time()) {
            $jsapi_ticket=Session::get('jsapi_ticket');
        }else{
            $access_token=$this->getWxAccessToken();
            $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
            $res=$this->http_curl($url);
            $jsapi_ticket=$res['ticket'];
            Session::put('jsapi_ticket',$jsapi_ticket);
            Session::put('jsapi_ticket_expire_time', time() + 7000);
         }
        return $jsapi_ticket;

    }

    //获取16位随机码
    public function getRandCode($num=16)
    {
        // $array = array_merge(range("a", "z"),range("A", "Z"),range(0, 9));
        // shuffle($array);
        // $str = substr(join("", $array), mt_rand(0,12),16);
        // return $str;
        $array =array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'a','b','c','d','e','f','g','h','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
            '0','1','3','4','5','6','7','8','9'
        );
        $tmpstr='';
        $max=count($array);
        for($i=1;$i<=$num;$i++){
            $key=rand(0,$max-1);//'A'->$array[0]
            $tmpstr .= $array[$key];

        }
        return $tmpstr;
    }

    //分享接口（朋友圈）
    function shareWx(Request $request){
//        $data=$request->all();
//        $name=$data['name'];
//        $timestamp=$data['timestamp'];
//        $noncestr=$data['noncestr'];
//        $signature=$data['signature'];
        //获取jsapi_ticket票据
        $name='123';
        $jsapi_ticket=$this->getJsApiTicket();

        $timestamp=time();

        $noncestr=$this->getRandCode();

        $url="http://wechat.yogasol.xin/shareWx";
        //获取signature
        //jsapi_ticket=".$jsapi_ticket



        $signature="jsapi_ticket=".$jsapi_ticket."&noncestr=".$noncestr."&timestamp=".$timestamp."&url=".$url;
   //   dd($signature);
        $signature=sha1($signature);



        return view("weixin.share",compact("timestamp","noncestr","signature"));

//        return view('weixin.share',[
//            "name"=>$name,
//           "timestamp"=>$timestamp,//生成签名时间戳
//            "noncestr"=>$noncestr,//生成签名的随机串
//            "signature"=>$signature//签名
//        ]);
    }


    //上传临时素材
    public function uploadTimeImage(Request $request){

        $file = $request->file('img');//获取文件名称

    //    $filePath = [];  // 定义空数组用来存放图片路径

//        if (is_array($file)) {
        //遍历数组，把 赋值给$key,数组的值赋值给$value

            //文件是否上传成功
            if (!$file->isValid()) {
                exit("上传图片出错，请重试！");
            }
            //扩展名
            $originaName = $file->getClientOriginalName();
            //上传文件后缀
            $ext = $file->getClientOriginalExtension();
            //MimeType
            $type = $file->getClientMimeType();
            //临时绝对路径
            $realPath = $file->getRealPath();


            //设置支持的文件格式
            $allowed_extensions = ['png', 'jpg','jpeg','gif','png'];

            //判断图片文件格式是否支持
            if ($ext && !in_array($ext, $allowed_extensions)) {
                return ['error' => 'Uploaded picture formats do not support(Only support png,jpg,jpeg,gif,png)'];
            }


            //设置时间名为文件名
            $filename = date("y-m-d-H-i-s") . '-' . uniqid() . '.' . $ext;


            //保存到磁盘（本地）
            $tec = Storage::disk('uploads')->put($filename, file_get_contents($realPath));


            $filePath = storage_path()."/app/public/uploads/".$filename;


            $access_token=$this->getWxAccessToken();

            $url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";
            $postarr=array(
                //new \CURLFile(realpath($img),'image/jpg')
                'media'=>new \CURLFile($filePath),
            );

            $res=$this->http_curl($url,'post','json',$postarr);
            dd($res);




    }


//测试
    public function test()
    {

        $app = config('app.appid');

    }

}


