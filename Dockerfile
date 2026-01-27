FROM php:8.2-apache

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Install dependencies and PHP extensions
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    supervisor \
    librdkafka-dev \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP Core extensions
RUN docker-php-ext-install pdo pdo_mysql zip pcntl

# Install Redis and RdKafka from source (bypass PECL)
RUN git clone --depth 1 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis \
 && git clone --depth 1 https://github.com/arnaud-lb/php-rdkafka.git /usr/src/php/ext/rdkafka \
 && docker-php-ext-install redis rdkafka

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . /var/www/html

# Copy Supervisor config
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy start script
COPY ./start.sh /start.sh
RUN chmod +x /start.sh

# Set working directory
WORKDIR /var/www/html

# Expose ports (Apache + Reverb)
EXPOSE 80 6001

# Start container
CMD ["/start.sh"]
