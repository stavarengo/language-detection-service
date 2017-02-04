FROM stavarengo/cld2-php-language-detection-library:latest

# Clone our repository
RUN cd /var/www/html && \
    git clone https://github.com/stavarengo/language-detection-service.git --progress

# Install Composer
RUN cd /var/www/html/language-detection-service && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('SHA384', 'composer-setup.php') === '55d6ead61b29c7bdee5cccfb50076874187bd9f21f65d8991d46ec5cc90518f447387fb9f76ebae1fbbacf329e583e30') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');"

# Install Composer dependencies
RUN DEBIAN_FRONTEND=noninteractive apt-get install zip unzip -y

RUN cd /var/www/html/language-detection-service && \
    php composer.phar install --no-dev

COPY Docker/language-detection-service.conf /etc/apache2/sites-available/

RUN rm -rf /etc/apache2/sites-enabled/* && \
    a2ensite language-detection-service.conf && \
    a2enmod rewrite && \
    service apache2 restart

WORKDIR /var/www/html/language-detection-service

CMD ["apache2ctl", "-D", "FOREGROUND"]
