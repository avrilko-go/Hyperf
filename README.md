<h1 align="center">
  <a href="http://doc.cms.7yue.pro/">
  <img src="http://doc.cms.7yue.pro/left-logo.png" width="250"/></a>
  <br>
  Lin-CMS-Hyperf
</h1>

<h4 align="center">一个简单易用的CMS后端项目 | <a href="" target="_blank">Lin-CMS-Hyperf</a></h4>
<blockquote align="center">
  <em>Lin-CMS-hyperf</em> 使用hyperf适配lin-cms-vue的一个后端服务 
</blockquote>

## 快速开始 

### 克隆项目
```git clone git@github.com:hb475721797/Hyperf.git```
### composer安装
```cd Hyperf && composer install```
### 配置信息
```cp .env.example .env```

### 执行数据库迁移
```php bin/hyperf.php migrate```

### 进行数据填充
```php bin/hyperf db:seed```

### 启动
```php bin.hyperf start```








错误码

cms 1开头
   
10002  权限不足，请联系管理员
10020  账户不存在 
10030  密码错误，请重新输入 参数错误
10040  Token已过期或无效Token | 令牌签名不正确，请确认令牌有效性或令牌类型 | 令牌尚未生效 | 尝试获取的Token变量不存在
10050  令牌已过期，刷新浏览器重试
10070  用户组不存在
10090  不支持该登录验证方式
40001  日志信息不能为空