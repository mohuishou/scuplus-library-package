# 图书馆的一些有用的链接

登录：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVBNBMSYYA-04659
method:post
param:{
    func:login-session
    login_source:bor-info
    bor_id: //学号
    bor_verification: //密码
    bor_library:SCU50
}
```

当前外借：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVBNBMSYYA-04693?func=bor-loan&adm_library=SCU50
method:get
```

借阅历史：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVBNBMSYYA-26163?func=bor-history-loan&adm_library=SCU50
method:get
```

续借全部：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVBNBMSYYA-26951
method:get 
param:{
    func:bor-renew-all
    adm_library:SCU50
}
```

部分续借：
```
url:http://opac.scu.edu.cn:8080/F/H3HJ67YH32B1RF1JXHGILB4IA6PR8SKPRN1S44YIXCEQN8YJK8-44634
method:get 
param:{
    func:bor-renew-all
    renew_selected:Y
    adm_library:SCU50
    c001256684000020:Y
}
```