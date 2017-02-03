# php to get Score from SCUT jw2005
用于获取SCUT教务系统的成绩
隐去验证码识别的服务，可以选择网上的API或者自己写识别功能，或者交给用户输入
写成单php文件接口调用形式

>入口URL(ip从110.65.10.231~110.65.10.238)

```php
$verify_code_url = "http://110.65.10.231/CheckCode.aspx"; //验证码地址
$url="http://110.65.10.231/".$url_c."/default2.aspx";  //教务处地址
```

#### 注意事项
从主页获取代替cookie的随机地址
即http://ip/(随机地址)/default2.aspx地址中获取包含括号的内容，作为登录凭证
后面的所有操作URL地址都要相应变化
每次POST提交的参数会在上一次获取的页面中
所以使用正则提取参数