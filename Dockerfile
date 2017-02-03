FROM stavarengo/cld2-php-language-detection-library:latest

RUN cd /var/www/html && \
    git clone https://github.com/stavarengo/language-detection-service.git --progress && \
    sed -i "s|DocumentRoot /var/www/html|DocumentRoot /var/www/html/language-detection-service|" /etc/apache2/sites-enabled/000-default.conf && \
    service apache2 restart

