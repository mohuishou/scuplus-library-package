# 图书馆的一些有用的链接

登录：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVUXBMSYYA-04659
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
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVUXBMSYYA-04693?func=bor-loan&adm_library=SCU50
method:get
```

借阅历史：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVUXBMSYYA-26163?func=bor-history-loan&adm_library=SCU50
method:get
```

续借全部：
```
url:http://opac.scu.edu.cn:8080/F/77K5KKPMFTPISI2TH6K9E6IELLAR71RH6F87UEQFVVUXBMSYYA-26951?func=bor-renew-all&adm_library=SCU50
```