# hyperf-helper
hyperf-helper 工具助手，自动生成service、api控制器、验证器
#常用方法
````
Redis()          #获取redis实例
Logger()         #获取日志实例
getClientIp()    #获取客户端ip
getContainer()   #获取DI容器
verifyIp()       #验证IP有效性
p()              #打印调试日志到控制台
uuid(32)         #生成不重复的字符串
filterEmoji（）   #filterEmoji表情过滤
convertHump()    #数组驼峰key 转下划线key
session()        #设置Session管理
getSession（）    #获取session实例
sessionDestroy() #销毁当前Session实例
````

### 工具1 QueryHelper
第一步 引入 
```php
use GetQueryHelper;
```

第二步使用：
```php
        //获得模型
        $userModel = User::query();
        //（1）可以再模型设置条件，join 之类的
        $userModel->where('id', 1)->where('status', 1);

        //引入QueryHelper  第一个参数传模型对象，第二个参数传 数组分页查找
        $query = $this->QueryHelper($userModel, $data);

        //如果不用（1）方法也可以在引入引入QueryHelper后再对模型进行
        $query->query->where('id', 1);
        $query->query->join('','','','');
        //...等等所有模型操作

        // 切入url参数，接收 username 和 keywords 并使用 like 查询，接收 user_id is_top 使用 eq 查询
        $query->equal('user_id,is_top');
        $query->like('username,keywords');
        //用法1
        $query->timeBetween('字段');
        $query->timeBetween([
            '开始时间字段',
            '结束时间字段'
        ]);
        $query->dateBetween('日期字段');
        $query->dateBetween([
            '开始日期字段',
            '结束日期字段'
        ]);
        //启用自动分页 paginate 第一个参数可选，列表返回字段，第二个参数可以传一个匿名函数 可以再里面修改返回列表集合
        return $query->paginate();

``` 

#工具2 自动生成工具依赖
## gen:api
自动生成api文件，自动把swagger注解，和常用代码给生成出来
#####参数1：类名
#####参数2：当前模块名字（swagger上展示用）
#####参数3：绑定哪个服务（不写为当前类名+Service)
#####参数4：当前模块介绍（swagger上展示用）
例如生成api User控制器(支持多级目录)：
```shell script
php bin/hyperf.php gen:api User 用户控制器 UserService 介绍
执行后会在 App/Controller里生成User.php 类

#多级目录
php bin/hyperf.php gen:api Api/v1/User 用户控制器 UserService 介绍
执行后会在 App/Controller/api/v1/下生成User.php 类
```

## gen:service
自动生成service文件，把常用代码给生成出来

```shell script
php bin/hyperf.php gen:service User
执行后会在 App\Serivce 里生成UserService.php 类

```

## gen:validate
自动生成验证器文件，把常用规则给生成出来

```shell script
php bin/hyperf.php gen:validate User
执行后会在 App\Validate 里生成UserValidation.php 类并把默认规则填入
```