server {
   charset utf-8;
   client_max_body_size 128M;
   sendfile off;

   add_header Access-Control-Allow-Origin *;

   listen 80; ## listen for ipv4
   #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

   server_name healthcheck.relikt.monster;
   root        /var/www/healthcheck/web/;
   index       index.php;

   access_log  /var/www/healthcheck/vagrant/nginx/log/yii2basic.access.log;
   error_log   /var/www/healthcheck/vagrant/nginx/log/yii2basic.error.log;

   location / {
       # Redirect everything that isn't a real file to index.php
       try_files $uri $uri/ /index.php$is_args$args;
   }

   # uncomment to avoid processing of calls to non-existing static files by Yii
   #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
   #    try_files $uri =404;
   #}
   #error_page 404 /404.html;

   location ~ \.php$ {
       include fastcgi_params;
       fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
       #fastcgi_pass   127.0.0.1:9000;
       fastcgi_pass unix:/var/run/php/php7.3-fpm.sock;
       try_files $uri =404;
   }

   location ~ /\.(ht|svn|git) {
       deny all;
   }
}
