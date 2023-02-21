<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://modworkshop.net/mws/assets/images/mws_logo_white.svg" width="200"></a></p>

## ModWorkshop Backend

This repository contains the source code for the backend side of ModWorkshop-V3+
The backend uses the Laravel framework.

## Installation

### Linux
Guide is written for Debian based distros.

1. Install PHP 8.1+ and Composer
    1. ```bash
        sudo apt-get install php php-pear php-cli php-dev
        ```
    2. ```bash
        php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
        php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
        php composer-setup.php
        php -r "unlink('composer-setup.php');"```
2. Install the apfd extension:
*This is ncecessary due to a very annoying ignored issue by the PHP devs https://bugs.php.net/bug.php?id=55815*
    1. `pecl install apfd`
    2. Enable it in php.ini by adding `extension=apfd` around line 950.
3. Install libvips (Image compression and how we convert them efficiently to webp, including gifs)
```bash
sudo apt-get install --no-install-recommends libvips42
```
4. Install postgresql
    1. ```bash
        sudo apt install postgresql postgresql-contrib php-pgsql
        ```
    2. Create a database named mws by running: 
        ```bash
            sudo -u postgres createdb mws
        ```
5. Copy .env.example to .env and fill the main information as bare minimum.
6. Run on the directory of the backend the following command:
    ```bash
        php artisan initial-setup
    ```

The backend should be ready to use now.

Optional (but necessary in production):
1. Install Redis:
    1. ```bash
        sudo apt-get install redis-server php-redis
        ```
    2. ```bash
        sudo pecl install redis
        ```
    3. Set CACHE_DRIVER to 'redis' in the .env.
2. Setup CloudFlare R2 https://dash.cloudflare.com. Fill all the data needed in the .env and set FILESYSTEM_DRIVER to r2.
3. Set a strong password for Postgres. https://bitwarden.com/password-generator/
3. Setup all social logins (Just visit each site linked in the .env)

### Windows
Will be done in the future. There are currently a few issues with Windows and some of the packages we use.


## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.