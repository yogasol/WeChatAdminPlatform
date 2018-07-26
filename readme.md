# 微信公众平台开发


- 接受事件推送、回复响应信息（纯文本、单图、单图文、多图文）
- 第三方API在微信中的调用与事例
- 自定义菜单、事件推送
- 群发接口调用
- 网页授权接口调用
- 模板消息接口
- 生成二维码接口
- 微信JS-SDK（分享接口）
- 临时素材上传获取media_id

## 使用手册

 *langurage: php > 7.0   
 Framework:&ensp;laravel5.5*    

## 环境配置

#### Using composer to Install the laravel framework


1.Read the composer.json file from the current directory  

```
composer install
```
2.rename .env

```
cp  .env.example  .env
```

3.Generation key
```
php artisan key:generate
```
4.Use Artisan command migrate to run all unfinished migration
```
php artisan migrate
```
 
5.Configuring the.Env file
```
APP_ID=你申请的公众号id
APP_SECRET=对应的AppID密码

//若是测试号
TEST_APP_ID=测试号的id
TEST_APP_SECRET=对应的密码

TOUSER=公众号关注者的公开账号
TEMPLATE_ID=模板id
```

##测试

[更多内容请参考微信公众平台开发技术文档](https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1445241432)
