## **PHP开发的开源微博系统**

QQ群：434615423

1.测试地址 http://t.aidigu.cn

2.采用PHP + MySQL开发，框架采用ThinkPHP5.1

3.用户登录后拥有专属ID

4.支持表情、关注用户，网盘分享等功能

5.支持图片上传,视频上传,网盘存储分享

### **安装方式**

1.克隆源码，导入数据库即可

2.复制 example_env 为 .env 并修改.env相关配置

3.配置Web服务器的伪静态  

*Nginx伪静态配置（nginx.conf）*  

    location / {
    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php?s=/$1 last;
        break;
          }
    }

*Apache伪静态配置（.htaccess文件）*

    <IfModule mod_rewrite.c>
      Options +FollowSymlinks -Multiviews
      RewriteEngine On
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteRule ^(.*)$ index.php?s=$1 [QSA,PT,L]
    </IfModule>

*IIS伪静态规则配置（web.config）*

    <?xml version="1.0" encoding="UTF-8"?>
      <configuration>
        <system.webServer>
          <rewrite>
             <rules>
                <rule name="OrgPage" stopProcessing="true">
                   <match url="^(.*)$" ></match>
                      <conditions logicalGrouping="MatchAll">
                         <add input="{HTTP_HOST}" pattern="^(.*)$" ></add>
                            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" ></add>
                       <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" ></add>
                    </conditions>
                 <action type="Rewrite" url="index.php/{R:1}" ></action>
              </rule>
            </rules>
         </rewrite>
      </system.webServer>
    </configuration>
    
### **建议的环境：**

**Linux系统**：CentOS7+/Debian9+/Ubuntu20.04+PHP7+Mysql5.6以上  
**PHP版本需求**：建议PHP7.2+  
**MySQL版本需求**：MySQL5.6+

## 版权信息

**本项目遵循** ***MIT License***
