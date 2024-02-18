# CodeIgniter4-IonAuth4-GoogleOAuth2
CodeIgniter 4 with Ion Auth 4 &amp; Google OAuth authentication

## INFORMATION
* CodeIgniter 4.4.x
* Ion Auth 4
* Google OAuth2 
* Bootstrap 5

## PREREQUISITES
* PHP 8.1
* MySQL
* Composer

## INSTALLATION
1. Install CodeIgniter 4 using composer
```
$ composer create-project codeigniter4/appstarter <app-name>
```
2. Install Ion Auth 4 using composer
```
$ composer config minimum-stability dev
$ composer config repositories.ionAuth vcs git@github.com:benedmunds/CodeIgniter-Ion-Auth.git
$ composer require benedmunds/codeigniter-ion-auth:4.x-dev   
```
or read the manuals:
https://github.com/benedmunds/CodeIgniter-Ion-Auth/blob/4/INSTALLING.md

3. Create new database `(eg: db_ci4)` and insert/query with file inside `sql` folder 

4. Install Google OAuth API Service using composer
```
$ composer require google/apiclient:^2.12
```

5. Replace entire CodeIgniter `project folder (app, public)` 
6. Done

## Changelog:
* added Google OAuth2 credential in Config/App
* added custom Libraries to initialized Google OAuth2
* breakdown Bootstrap 5 templates into CI4 - Layouts

---
@antonius.arie
HappyCoding
