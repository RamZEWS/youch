Documentation
===============================
API Url: http://api.youch.me/
Content-Type: application/json

Authorization
-------------------------------
http://api.youch.me/oauth2/token
Method: POST
Params:
```
{
    "grant_type": "password",
    "username": "username",
    "password": "password",
    "client_id": "testclient",
    "client_secret": "testpass"
}
```
Answer without errors:
```
{
    "access_token": "d5bf9c2c22efd84d560adf4cbe4807802efbefa8",
    "expires_in": 86400,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "99787116a9a1fa01a24c6be6a14d019108f0de16"
}
```
Possible errors:
```
{
    "name": "Bad Request"
    "message": "Grant type "password" not supported"
    "code": 0
    "status": 400
    "type": "filsh\yii2\oauth2server\exceptions\HttpException"
}

{
    "name": "Bad Request"
    "message": "The client credentials are invalid"
    "code": 0
    "status": 400
    "type": "filsh\yii2\oauth2server\exceptions\HttpException"
}

{
    "name": "Unauthorized"
    "message": "Invalid username and password combination"
    "code": 0
    "status": 401
    "type": "filsh\yii2\oauth2server\exceptions\HttpException"
}

{
    "name": "Bad Request"
    "message": "Missing parameters: "username" and "password" required"
    "code": 0
    "status": 400
    "type": "filsh\yii2\oauth2server\exceptions\HttpException"
}
```

Refresh Token
-------------------------------
http://api.youch.me/oauth2/token
Method: POST
Params:
```
{
    "grant_type": "refresh_token",
    "refresh_token": "your_token",
    "client_id": "testclient",
    "client_secret": "testpass"
}
```
Answer without errors:
```
{
    "access_token": "d5bf9c2c22efd84d560adf4cbe4807802efbefa8",
    "expires_in": 86400,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "99787116a9a1fa01a24c6be6a14d019108f0de16"
}
```

Registration
===============================
Step 1
-------------------------------
http://api.youch.me/register/step1
Method: POST
Params:
```
{
    "name": "Last and First names",
    "email": "email",
    "type": "tourist | company"
    "password": "password",
    "confirm_password": "confirm_password"
}
```
Answer without errors:
```
{
    "access_token": "d5bf9c2c22efd84d560adf4cbe4807802efbefa8",
    "expires_in": 86400,
    "token_type": "Bearer",
    "scope": null,
    "refresh_token": "99787116a9a1fa01a24c6be6a14d019108f0de16"
}
```

Step 2 (need Authorization header with access_token from step 1)
-------------------------------
http://api.youch.me/register/step2
Method: POST
Params:
```
{
    "username": "User Login",
}
```
Answer without errors:
```
{
    "id": 1,
    "username": "User Login",
    "auth_key": "CyiLRThXu5UOwcQhISKiYAg-XXaazTAB",
    "password_hash": "$2y$13$DUfc2kZAqkfgd9Hhqqy27.ZRE8YrxYEgRxkMTj9aMVN5gZBO.vQbm",
    "password_reset_token": "wizZKMn495YYG0FfKcVEXgfXUR2yvC6c_1492259103",
    "email": "ramzes.2007.90@gmail.com",
    "status": 10,
    "created_at": 1492259103,
    "updated_at": 1492260639,
    "name": "Roman Lukoyanov",
    "about": null,
    "city_id": null
}
```

Step 3 (need Authorization header with access_token from step 1)
-------------------------------
http://api.youch.me/register/step3
Method: POST
Params:
```
{
    "city_id": city_id,
}
```
Answer without errors:
```
{
    "id": 1,
    "username": "User Login",
    "auth_key": "CyiLRThXu5UOwcQhISKiYAg-XXaazTAB",
    "password_hash": "$2y$13$DUfc2kZAqkfgd9Hhqqy27.ZRE8YrxYEgRxkMTj9aMVN5gZBO.vQbm",
    "password_reset_token": "wizZKMn495YYG0FfKcVEXgfXUR2yvC6c_1492259103",
    "email": "ramzes.2007.90@gmail.com",
    "status": 10,
    "created_at": 1492259103,
    "updated_at": 1492260639,
    "name": "Roman Lukoyanov",
    "about": null,
    "city_id": 3
}
```

Step 4 (need Authorization header with access_token from step 1)
-------------------------------
http://api.youch.me/register/step4
Method: POST
Params:
```
{
    "about": "Tell about yourself!",
}
```
Answer without errors:
```
{
    "id": 1,
    "username": "User Login",
    "auth_key": "CyiLRThXu5UOwcQhISKiYAg-XXaazTAB",
    "password_hash": "$2y$13$DUfc2kZAqkfgd9Hhqqy27.ZRE8YrxYEgRxkMTj9aMVN5gZBO.vQbm",
    "password_reset_token": "wizZKMn495YYG0FfKcVEXgfXUR2yvC6c_1492259103",
    "email": "ramzes.2007.90@gmail.com",
    "status": 10,
    "created_at": 1492259103,
    "updated_at": 1492260639,
    "name": "Roman Lukoyanov",
    "about": "Tell about yourself!",
    "city_id": 3
}
```

Password recovery
===============================
Reset pass
-------------------------------
http://api.youch.me/auth/reset
Method: POST
Params:
```
{
    "email": "email",
}
```
Answer without errors:
```
{
    "msg": "Инструкции по восстановлению пароля отправлены на указанный адрес электронной почты."
}
```