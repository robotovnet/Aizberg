#!/bin/sh
set -e

# Проверяем наличие шаблона конфигурации
if [ -f /opt/sphinx/conf/sphinx.conf.template ]; then
    echo "Генерация конфигурации Sphinx из шаблона..."
    envsubst '${SPHINX_DB_HOST} ${SPHINX_DB_USER} ${SPHINX_DB_PASSWORD} ${SPHINX_DB_NAME} ${SPHINX_DB_PORT}' \
        < /opt/sphinx/conf/sphinx.conf.template \
        > /opt/sphinx/conf/sphinx.conf
    echo "Конфигурация Sphinx успешно сгенерирована"
fi

# Запуск оригинальной команды
exec "$@"

