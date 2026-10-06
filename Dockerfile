FROM php:8.3-cli

WORKDIR /app
COPY saba/ ./

ENV PORT=8080
EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} router.php"]
