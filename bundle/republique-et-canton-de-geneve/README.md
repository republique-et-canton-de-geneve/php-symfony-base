# A Symfony bundle to add headers to your HTTP response.

##
A simple easy way to send header in your http response

## Usage
You define one or more header response in your yaml configuration, for exemple:

```
---
#config/packages/response_header.yml
response_headers:

  headers:
        #a header X-XSS-Protection with value 1; mode=block 
    X-XSS-Protection: 
      value: 1; mode=block
    
        #a shorter description for header Referrer-Policy with value strict-origin
    Referrer-Policy: strict-origin
      
      
        #a header with value as a array 
    Content-Security-Policy:
      - default-src 'none'; 
      - script-src 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - script-src-elem 'self' data: 'unsafe-inline' 'unsafe-hashes' 'unsafe-eval';
      - img-src 'self' data: ge.ch *.ge.ch *.etat-ge.ch ;
    
        #a conditional header, the header is send if env var APP_SERVER_TYPE is 'local'
    X-Frame-Options:
      value: SAMEORIGIN
      condition: "'%env(APP_SERVER_TYPE)%' == 'local'"  

        #a condtional in function of uri request   
    Expires:
      value: 0
      condition: request.getPathInfo() matches '^/admin'  
      

        #a confitional header with a array value
    headername:  
      value:
        - elem1
        - elem2
        - elem3
      condition: "'%env(APP_END)%' == 'dev'"  
...      
```
### Conditonal header
The conditional is made with symfony expression language, the available var are:

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


## Installation
The bundle should be automatically enabled by Symfony Flex. If you don't use Flex, you'll need to enable it manually as explained in the docs.


```
composer require republique-et-canton-de-geneve/headers-bundle
```



License
Released under the MIT License

