# Base image for the container
FROM php:8.1-cli

# Set the working directory inside the container
WORKDIR /app

# Copy all files from the current directory to the container's /app directory
COPY . /app

# Expose port 8080 for the application
EXPOSE 8080

# Run the PHP built-in server to serve your application
# CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
CMD ["php", "-S", "0.0.0.0:8080", "public/router.php"]
