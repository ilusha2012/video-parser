Video parser by URL
============================

The simple Yii2 application that parsing URL of the video and find title, description and prewire image of videos.

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------
~~~
PHP >= 5.6.
Composer 1.5.2
Docker >= 1.13.0+
Docker-compose >= 1.10.0
~~~

INSTALLATION
------------

~~~bash
$ mkdir project_name
$ cd project_name

$ git clone https://github.com/ilusha2012/video-parser .

#Start containers
$ docker-compose up -d

#Install Composer
$ docker-compose exec php-fpm php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
$ docker-compose exec php-fpm php  composer-setup.php
$ docker-compose exec php-fpm php -r "unlink('composer-setup.php');"

#Install vendors
$ php composer.phar global require "fxp/composer-asset-plugin:^1.3.1"
$ php composer.phar install

$ chmod 777 web/assets/
~~~


START
-------------

### Run migrations
```bash
$ docker-compose exec php-fpm php yii migrate
```
Application be available from
```
http://127.0.0.1:1234
```

