# A Symfony bundle to add headers to your HTTP response.


## Config
You define one or more header response in your yaml configuration, for exemple:

```
---
#config/packages/response_header.yml
response_headers:

  headers:

       #shorter defintion for header and value
    headername1: value1

       #defintion for header and value
    headername2:
      value: value2

       #defintion for header and array format value
    headername3:
       - 'value31 '
       - 'value32 '
       - 'value33 '
       - 'value34 '

       #defintion for a header with a condition
    headername4:
      value: value4
      condtion: request.getPathInfo() matches '^/admin'

        #defintion for a header with a condition and array format value
    headername5:
      value:
       - value51;
       - value52;
       - value53
      condition: "'%env(APP_END)%' == 'dev'"

       # header that should not replace a previous similar header but add a second header of the same type
    headername6:
      value: value6
      replace: false
...
```


#### Conditonal header
The conditional is made with symfony expression language, the available variables are:


```
response_headers:
  headers:
    X-Frame-Options:
      value: SAMEORIGIN
      condition: "'%env(APP_SERVER_TYPE)%' == 'local'"
```
The 'X-Frame-Option' header will be inclued in the HTTP response if the 'APP_SERVER_TYPE' environment variable is equal to 'local'.

```
  %env(name)%  : a value from environement
  request: An instance of the class Symfony\Component\HttpFoundation\Request class
  response: An instance of the class Symfony\Component\HttpFoundation\Response class
```

  Example:
```
   condition: "request.getPathInfo()  matches '^/admin'"
   condition: "response.getStatusCode() == 200"
   condition: "false"
```


##### line format
```
response_headers:

  headers:
    Content-Security-Policy:
      - default-src 'none';
      - script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - img-src 'self' data: localhost *.mysite.com;
      format: string
```
This is the default format
##### Result:
```
Content-Security-Policy: default-src 'none';script-src 'self' data: 'unsafe-inline';img-src 'self' data: localhost *.mysite.com
```

##### mutliple format

But it's possible to have one more than one HTTP header with the same name

```
response_headers:
  headers:
    Accept:
      - application/json
      - application/xml
      format: array
```

##### Result:
```
Accept: application/json
Accept: application/xml
```

