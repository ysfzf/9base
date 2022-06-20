
### Laravel
````
 cp .env.example  .env
 composer install
 php artisan key:generate

````

### Dcat Admin
https://learnku.com/docs/dcat-admin/2.x
````
php artisan admin:install
````
 

### JWT
https://jwt-auth.readthedocs.io/en/develop/auth-guard/#multiple-guards

````
php artisan jwt:secret
````

### Laravel-s
https://www.bookstack.cn/read/LaravelS/spilt.1.d7f5aa7b24640641.md

````
#publish
php artisan laravels publish

#start or stop
php ./bin/laravels start|stop|status
````

### Horizon
https://learnku.com/docs/laravel/8.x/horizon/9419
````
# install
php artisan horizon:install

# run
php artisan horizon

# other
php artisan horizon:pause
php artisan horizon:continue
php artisan horizon:status
php artisan horizon:terminate
````

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
