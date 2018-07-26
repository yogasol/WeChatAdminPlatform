<!doctype html>
<html>
<head>
    <title>微信js分享接口</title>
    <meta name='viewpoint' content="initial-scale=1.0;width=device-width"/>
    <!--<meta http-equiv='content' content='text/html;charset=utf-8'/>-->
    <script type="text/javascript" src="{{ URL::asset('//res.wx.qq.com/open/js/jweixin-1.2.0.js') }}"></script>
</head>
<body>
<script>
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: 'wxf627f7ad26a37212', // 必填，公众号的唯一标识
        timestamp: '{{$timestamp}}', // 必填，生成签名的时间戳
        nonceStr: '{{$noncestr}}', // 必填，生成签名的随机串
        signature: '{{$signature}}',// 必填，签名
        jsApiList: [
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'chooseImage',
            'scanQRCode'
        ] // 必填，需要使用的JS接口列表
    });

    wx.ready(function(){
        wx.onMenuShareTimeline({
            title: 'test1', // 分享标题
            link: 'http://www.baidu.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.baidu.com/img/bd_logo1.png', // 分享图标
            success: function () {
                // 用户点击了分享后执行的回调函数
            }
        });

        wx.onMenuShareAppMessage({
            title: 'test1', // 分享标题
            desc: 'test baidu', // 分享描述
            link: 'http://www.baidu.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'https://www.baidu.com/img/bd_logo1.png', // 分享图标
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户点击了分享后执行的回调函数
                alert('分享成功');
            },
            cancel:function(){
                alert('请重新分享');
            }
        });


    });
    function show() {
        wx.chooseImage({
            count: 1, // 默认9
            sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
            sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
            success: function (res) {
                var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            }
        });
    }
    
    function scan() {
        wx.scanQRCode({
            needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
            scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
            success: function (res) {
                var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
            }
        });
    }

        wx.error(function(res){
            // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
''
        });

</script>

<button onclick="show()">imooc</button>
<button onclick="scan()">scan</button>
</body>
</html>