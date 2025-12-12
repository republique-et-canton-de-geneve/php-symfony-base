# A Symfony bundle to add headers to your HTTP response.

A Symfony bundle to easily send headers in your HTTP response

### Usage
You define one or more headers response in your yaml configuration, for exemple:

```
---
#config/packages/response_header.yml
response_headers:

  headers:
    X-XSS-Protection: value: 1; mode=block

    Referrer-Policy: strict-origin

    Content-Security-Policy:
      - default-src 'none'; 
      - script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - img-src 'self' data: localhost *.mydite.com;

    X-Frame-Options:
      value: SAMEORIGIN
      condition: "'%env(APP_SERVER_TYPE)%' == 'local'"  
      replace: false
      
    Expires:
      value: 0
      condition: request.getPathInfo() matches '^/admin'  
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
   condition: request.getPathInfo()  matches '^/admin'
   condition: response.getStatusCode() == 200
```  

#### Header values ​​in array format
For very long headers, it is possible to use a table format. The header value will be reduced to a single line.

```
response_headers:

  headers:
    Content-Security-Policy:
      - default-src 'none';
      - script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - img-src 'self' data: localhost *.mysite.com;
```

The 'Content-Security-Policy' header will have the following value:
Content-Security-Policy: default-src 'none';script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';img-src 'self' data: localhost *.mysite.com

## Installation
The bundle should be automatically enabled by Symfony Flex. If you don't use Flex, you'll need to enable it manually as explained in the docs.


```
composer require republique-et-canton-de-geneve/response-headers-bundle
```



License
Released under the MIT License

