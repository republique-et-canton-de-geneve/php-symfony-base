[![php-cs-fixer](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/php-cs-fixer.yml)

[![twig-cs-fixer](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/twig-cs-fixer.yml/badge.svg)](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/twig-cs-fixer.yml)

[![phpstan](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/phpstan.yml/badge.svg)](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/phpstan.yml)

[![rector](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/rector.yml/badge.svg)](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/rector.yml)

[![symfony](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/symfony.yml/badge.svg)](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/symfony.yml)

[![behat and code coverage](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/behat.yml/badge.svg)](https://github.com/republique-et-canton-de-geneve/php-symfony-base/actions/workflows/behat.yml)

# Application Template – Symfony Framework 7.x


## License
License: **AGPL v3**

## Purpose
This archive is designed to serve as a foundation for developing an application based on the **Symfony** framework.  
By cloning the repository and following the provided instructions, you can run this application independently.

## Symfony
[https://symfony.com/](https://symfony.com/)

## Features
The application includes the following components and functionalities:

- **Database Management**
  - Integration with **Doctrine** ORM.

- **Authentication**
  - Support for **SAML2** authentication.
  - Environment variable–based authentication for development environments.

- **Security**
  - Role-based access control using Symfony’s **Security Voter** system.

- **Assets Management**
  - **Webpack** integration for building and managing assets (CSS, JavaScript).

- **User Interface**
  - Templates rendered with **Twig**.
  - Styling with **Bootstrap**.

- **Logging**
  - Application-level logging.

- **Configuration**
  - Parameter management for flexible configuration.

- **Administration Tools**
  - Log visualization.
  - Application parameter management.
  - Environment and server health checks.

- **Messaging**
  - **Flash messages** for user feedback.
  - Temporary information or maintenance messages.

- **Navigation**
  - A simple and efficient menu system.

- **Error Handling**
  - Predefined error pages: **403**, **404**, **500**, and **503**.

- **Rich Text Editing**
  - Integration of **CKEditor**.
- 
- **Test**
  - **Behat**

## Requirement et installation
 
### Requirement
- Web Server :
    - appache server php > 8.3
- Command line
  - php > 8.3
  - git
  - composer
  - nodejs
  - yarn

### Installation
From the console

    git clone 
    cd your_project

    composer install
    yarn install
    
    yarn encore prod
    cd public
    ln -s . build/build
    cd ..
###  configuration
Configure your .env file
Authentification is made by SAML2, the application needs a Identity provider(IDP).

### Version
The file release.properties contains the application revision number


### Test without SAML2
- Create a new env.local file and write the content :

      # Use to know the type of server 'prod', 'rec', 'dev' or 'local'
      APP_SERVER_TYPE=local
      APP_URL=http://localhost/symfony-demo/htdocs/public

      APP_AUTH_FROM_ENV_VAR=true
      APP_USER_LOGIN="bond007"
      APP_USER_ROLES=UTILISATEUR|ADMIN
      APP_USER_EMAIL=James.Bond@mydomain.com
      APP_USER_FIRSTNAME=James
      APP_USER_NAME=James
      APP_USER_FULLNAME="James Bond (MI6)"


### Use and configure an other database

- Edit file .env the APP_DATABASE_URL define the database.
- Run the command : php bin/console doctrine:migrations:migrate



### commands line
Some usefull shell command 

For example :
>cmd/update_composer

- Code Analyse
    - cmd/check -> check the code with many tools
    - cmd/make-doc  -> update the doc in the directory doc
    - cmd/php-cs  -> code analyse php code sniffer
    - cmd/php-cs-fix  -> code fix php code sniffer
    - cmd/twig-cs  -> code analyse php code sniffer
    - cmd/twig-cs-fix  -> code fix php code sniffer
    - cmd/eslint    -> javascript  code analyse ESLINT
    - cmd/eslint-fixer    -> code fixe javascript analyse ESLINT
    - cmd/phpstan   -> code analyse PHPStan
    - cmd/symfony-check   -> check yaml config file, twig file and constructor injection parameters
    - cmd/behat     -> run behat test
    - cmd/behat-coverage -> run behat test with code coverage
    - cmd/make-doc  -> make a doc information on symfony system
    - cmd/rector-check  -> check the php code need a refactoring
    - cmd/rector-exec  -> refactor the php code
- Code maintenance
    - cmd/clear  -> clear the cache and temporary files
- Update lib
    - cmd/update-composer -> update php librairies
    - cmd/make-autoload -> optimise the composer class loading ( to do when you move or suppress class file)
    - cmd/update-asset -> update nodejs librairies
    - cmd/update-all -> update all, composer and node js
- Asset
    - cmd/make-prod -> create a webpack package for production
    - cmd/make-asset -> create a webpack package for dev
    - cmd/watch-asset -> create a webpack package for dev in watch mode
    - cmd/make-symlink -> recreate symbolic link for the public asset


