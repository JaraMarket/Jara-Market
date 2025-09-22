FROM php:8.2-apache

# Enable Apache Rewrite Module
RUN a2enmod rewrite

# Install dependencies and PHP extensions
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
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip \
    && pecl install rdkafka redis \
    && docker-php-ext-enable rdkafka redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

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

# Expose Apache port
EXPOSE 80 6001

# Start container
CMD ["/start.sh"]
