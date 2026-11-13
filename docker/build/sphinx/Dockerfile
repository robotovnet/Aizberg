FROM macbre/sphinxsearch:3.7.1

# Устанавливаем gettext для envsubst
RUN apk add --no-cache gettext

# Копируем entrypoint скрипт
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Используем наш entrypoint
ENTRYPOINT ["/entrypoint.sh"]
CMD ["searchd", "--nodetach", "--config", "/opt/sphinx/conf/sphinx.conf"]
