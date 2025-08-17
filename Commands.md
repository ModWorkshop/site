* See Laravel errors on Production
`docker exec backend cat ./storage/logs/laravel.log`

* Reload nginx without killing the site
`docker exec backend nginx -s reload`