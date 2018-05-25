FROM yoshz/apache-php-dev:7.0
MAINTAINER Vu Nguyen <vunt@nal.vn>

RUN export LANG=en_US.UTF-8 && \
    export DEBIAN_FRONTEND="noninteractive" && \
    apt-get update && \
    apt-get -yq install --no-install-recommends \
        imagemagick \
        php-imagick && \

    add-apt-repository -y ppa:ondrej/php && \
    apt-get update && \
    apt-get -yq install --no-install-recommends \
        php7.0-mbstring \
        php7.0-bz2 && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

RUN composer self-update && \
    curl -LsS http://codeception.com/codecept.phar -o /usr/local/bin/codecept && \
    chmod a+x /usr/local/bin/codecept && \
    echo "alias codecept='php /usr/local/bin/codecept'" >> ~/.bashrc && \
    ## PHP loc
    curl -LsS https://phar.phpunit.de/phploc.phar -o /usr/local/bin/phploc && \
    chmod a+x /usr/local/bin/phploc && \
    ## PHP depend
    curl -LsS http://static.pdepend.org/php/latest/pdepend.phar -o /usr/local/bin/pdepend && \
    chmod a+x /usr/local/bin/pdepend && \
    ## PHPmd
    curl -LsS http://static.phpmd.org/php/latest/phpmd.phar -o /usr/local/bin/phpmd && \
    chmod a+x /usr/local/bin/phpmd && \
    ## PHPCS
    curl -LsS https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar -o /usr/local/bin/phpcs && \
    chmod a+x /usr/local/bin/phpcs && \
    ## PHPCPD
    curl -LsS https://phar.phpunit.de/phpcpd.phar -o /usr/local/bin/phpcpd && \
    chmod a+x /usr/local/bin/phpcpd

SHELL ["/bin/bash", "-c", "source ~/.bashrc"]





