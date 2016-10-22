# scuplus-library-package
scuplus 四川大学图书馆扩展组件

## Todo

- [ ] 续借全部
- [ ] 部分续借


## 使用/安装
```php
git coloe / composer require mohuishou/scuplus-library-package
composer install

require_once 'vendor/autoload.php';
$lib=new \Mohuishou\Lib\Library(学号，图书馆密码);
```

## 接口

### 当前借阅信息
```php
方法： loanNow();
返回：
Array
(
    [1] => Array
        (
            [review_id] => c001256684000020 //续借id
            [author] => 桑德斯 
            [title] => Learning PHP设计模式 
            [end_day] => 20160912 //到期时间
            [money] =>  //超期欠费
            [address] => 江安馆理工图书 //分馆信息
            [book_id] => TP312PH/7724 //索书号
        )
    ······

```

### 历史借阅信息
```php
方法：loanHistory()
返回：
Array
(
    [1] => Array
        (
            [author] => 程正务
            [title] => 信号与系统简明教程
            [start_day] => 20160715 //借书日期
            [end_day] => 20160628 //还书日期
            [end_time] => 14:31 //还书时间
            [money] =>  //超期欠费
            [address] => 江安馆理工图书
        )
    ······
```

## Author
@莫回首