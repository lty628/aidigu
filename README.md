## **PHP开发的开源微博系统**

1.采用PHP + MySql开发，框架采用ThinkPHP5.1

2.用户登录后拥有专属id

3.支持表情、关注用户、转发、评论、以及消息提醒

4.支持图片上传,视频上传

### **安装方式**

1.克隆源码，导入数据库即可

2.配置 config/app.php和config/database.php

3.配置nginx伪静态

4.修改route/route.php，改为自己的域名绑定（或者通过config/app.php中修改：'url_domain_root'=> 'weibo.test'）

### **建议的环境：**

CentOS7+PHP7+Mysql5.6以上

### **页面如下**

### 登录页：

![img](https://oscimg.oschina.net/oscnet/up-b63d0ee26da0c7632e46ab7a2ade9b02840.png)

### 注册页：

![img](https://oscimg.oschina.net/oscnet/up-d2ca48baedd8bddaaca645d1cc4667d119a.png)

### 首页：

 ![img](https://oscimg.oschina.net/oscnet/up-1433f57267bfb7c8a89ea02de9ff71d9a3a.png)


## 版权信息

MIT