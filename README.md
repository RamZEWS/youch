Documentation
===============================
API Url: http://api.youch.me/
Content-Type: application/json

Authorization
-------------------------------
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
    "message": "Grant type "passwo1rd" not supported"
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
