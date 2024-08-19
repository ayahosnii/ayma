# Use the official PHP image as the base image
FROM php:8.2

# Set working directory
WORKDIR /var/www/html

# Install Python and create a virtual environment
RUN apt-get update && \
    apt-get install -y python3 python3-venv && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Create a virtual environment and activate it
RUN python3 -m venv /opt/venv
ENV PATH="/opt/venv/bin:$PATH"

# Install the language-tool-python package
RUN pip install language-tool-python

# Copy the Laravel application files to the container
COPY . .

# Install other dependencies
RUN apt-get update && \
    apt-get install -y git zip unzip libpng-dev libjpeg-dev libfreetype6-dev default-mysql-client && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the wait-for-mysql script
COPY wait-for-mysql.sh /usr/local/bin/wait-for-mysql.sh
RUN chmod +x /usr/local/bin/wait-for-mysql.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 8000 for the Laravel development server
EXPOSE 8000

# Run Laravel development server with waiting for MySQL
CMD ["wait-for-mysql.sh", "php", "artisan", "serve", "--host=0.0.0.0", "--port=8056"]

# Add import statements for sys and logging
RUN echo "import sys\nimport logging" >> /var/www/html/python-scripts/correct_grammar.py
