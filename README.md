<p align="center">
  <a href="https://aizberg.ru"><img src="docs/images/logo.svg" alt="Aizberg" width="220"></a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/license-GPL--3.0-blue" alt="Лицензия GPL-3.0">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Laravel-8-FF2D20" alt="Laravel 8">
</p>

# Aizberg

**Aizberg — платформа для интернет-магазинов.** Открытый движок, админ-панель. Запускайте и развивайте магазин на своих условиях.

> **Этот репозиторий — только API и бэкенд.** Админ-панель (Aizberg-front) — отдельный открытый проект в собственном репозитории; ссылка появится здесь после его публикации.

## Содержание

- [Возможности](#возможности)
- [Работает на Aizberg](#работает-на-aizberg)
- [Архитектура](#архитектура)
- [Быстрый старт](#быстрый-старт)
- [Конфигурация](#конфигурация)
- [Структура проекта](#структура-проекта)
- [API](#api)
- [Разработка модулей](#разработка-модулей)
- [Обслуживание](#обслуживание)
- [Тесты](#тесты)
- [Лицензия](#лицензия)

## Возможности

Бэкенд собран из независимых модулей — каждый отвечает за свою часть магазина.

| Область | Что есть | Модули |
|---|---|---|
| Каталог | Товары, разделы, фильтры, характеристики, бренды, фотографии, цены, остатки, похожие и сопутствующие товары | `Catalog`, `PriceType`, `Currency`, `Qa` |
| Продажи | Заказы, заказ в один клик, бонусная программа с уровнями | `Order`, `OrderOneClick`, `BonusProgram` |
| Магазины и города | Несколько магазинов и городов на поддоменах с общим API, транспортные компании, сервисные центры | `Shop`, `City`, `Country`, `TransportCompany`, `ServiceCenter` |
| Покупатели и персонал | Регистрация и профиль покупателя, типы клиентов, сотрудники, группы и права доступа, меню панели управления | `Client`, `ClientType`, `Staff`, `DashboardMenu` |
| Контент | Текстовые материалы и содержимое страниц: тексты подключаются в шаблонах в нужных местах сайта или выводятся как отдельные страницы | `Material` |
| Маркетинг | YML-выгрузки для торговых площадок, статистика Яндекс Вордстат, розыгрыши, формы обратной связи | `Ad`, `YandexWordstat`, `Lottery`, `Forms` |
| Уведомления | Email через Mailgun, Yandex Cloud Postbox и RuSender; SMS через ProntoSMS | `Email`, `Sms` |
| Сообщество | Маркетплейс модулей, блоги, витрина сайтов на Aizberg, конструктор API-документации | `MarketplaceModules`, `Blogs`, `Showcase`, `RealProjects`, `ApiDocumentation` |
| Служебное | Настройки, языки, часовые пояса, маски ввода, загрузка изображений | `Settings`, `Language`, `Timezone`, `Mask`, `ImageUploader` |

Полный список модулей — в каталоге [app/src/Modules](app/src/Modules).

## Работает на Aizberg

- [tehnocentr.ru](https://tehnocentr.ru)
- [azerty.ru](https://azerty.ru)
- [honglong.ru](https://honglong.ru)
- [aizberg.ru](https://aizberg.ru)

## Архитектура

Проект разворачивается через Docker Compose. Все сервисы описаны в [docker-compose.yml](docker-compose.yml).

| Сервис | Назначение |
|---|---|
| `app` | PHP 8.2 (FPM) с приложением на Laravel 8 |
| `webserver` | Nginx: домены магазинов, домен API, прокси к панели управления и Adminer |
| `db` | MariaDB (LTS) |
| `sphinx` | Sphinx Search 3.7 — поисковый индекс товаров |
| `memcached` | Кеш |
| `queue_server` | Beanstalkd — сервер очередей |
| `cp` | Панель управления, собирается из исходников Aizberg-front |
| `adminer` | Веб-интерфейс к базе данных |

Приложение — модульный монолит на [nwidart/laravel-modules](https://github.com/nWidart/laravel-modules). Публичный сайт магазина и панель управления — отдельные приложения, которые работают с этим бэкендом по HTTP API.

## Быстрый старт

### Требования

- Linux-сервер или рабочая машина с Docker и Docker Compose v2.
- Свободные порты `80`, `443`, `8443`, `81` (меняются в `.env`).

Пути в [docker-compose.yml](docker-compose.yml) и [crontab](crontab) рассчитаны на размещение проекта в `/srv/Aizberg`, а исходников панели управления — в `/srv/Aizberg-front`.

### Установка

1. Клонируйте репозиторий:

   ```bash
   git clone https://github.com/robotovnet/Aizberg.git /srv/Aizberg
   cd /srv/Aizberg
   ```

2. Создайте файлы окружения и заполните их (см. [Конфигурация](#конфигурация)):

   ```bash
   cp .env.example .env
   cp app/src/.env.example app/src/.env
   ```

   Для локального запуска укажите в `.env` значение `BASIC_AUTH_ENABLED=off`.

3. Пока репозиторий Aizberg-front не опубликован, отключите панель управления: закомментируйте сервис `cp` в [docker-compose.yml](docker-compose.yml) и блок `server` с `listen 8443` в [laravel_ssl.conf.template](services/configs/nginx/laravel_ssl.conf.template). Без этого сборка завершится ошибкой, а Nginx не запустится.

4. Соберите и запустите контейнеры:

   ```bash
   docker compose up -d --build
   ```

5. Установите PHP-зависимости. Composer в образ `app` не входит, поэтому используйте официальный образ Composer:

   ```bash
   docker run --rm -u "$(id -u):$(id -g)" -v "$PWD/app/src:/app" \
     composer:2 install --ignore-platform-reqs --no-scripts
   docker compose exec app php artisan package:discover
   ```

6. Сгенерируйте ключ приложения, создайте таблицы и заполните их начальными данными:

   ```bash
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate
   docker compose exec app php artisan module:seed
   ```

7. Постройте поисковый индекс:

   ```bash
   docker compose exec sphinx indexer --config /opt/sphinx/conf/sphinx.conf --rotate --all
   ```

После установки магазин и API доступны на порту `WEBSERVER_PORT_SSL`, Adminer — на порту `ADMINER_PORT`.

### Первый вход

В окружениях разработки и тестирования (`APP_ENV` равно `local`, `dev`, `test` и подобным) сидер модуля `Staff` создаёт сотрудника с полными правами:

- email: `root@aizberg.ru`
- пароль: `password`

Смените пароль сразу после первого входа. В остальных окружениях эта учётная запись не создаётся.

### HTTPS

По умолчанию Nginx отдаёт сайт без шифрования: директивы SSL в [шаблонах конфигурации](services/configs/nginx) закомментированы. Для боевого сервера положите сертификаты в `services/ssl` (имена файлов — в секции `webserver` файла [docker-compose.yml](docker-compose.yml)) и раскомментируйте директивы `listen 443 ssl` и `ssl_*`. Файл `services/ssl/.htpasswd` нужен, только если включена базовая авторизация.

## Конфигурация

Настройки разделены на два файла:

- [.env.example](.env.example) → `.env` в корне — параметры Docker Compose; они передаются в контейнеры как переменные окружения.
- [app/src/.env.example](app/src/.env.example) → `app/src/.env` — параметры Laravel, которые не зависят от инфраструктуры: ключ приложения, сессии, очереди, почтовый драйвер.

Основные переменные корневого `.env`:

| Переменные | Назначение |
|---|---|
| `APP_NAME`, `APP_URL`, `APP_ENV`, `APP_DEBUG` | Название, адрес и режим работы приложения |
| `WEBSERVER_HOST` | Основной домен магазина |
| `SHOP_HOSTS` | Дополнительные домены магазинов через пробел, например поддомены городов |
| `API_HOST` | Домен API |
| `SESSION_DOMAIN` | Домен cookie сессии; с точкой в начале — общий для поддоменов |
| `REDIRECT_HOST` | Домен, с которого настраивается перенаправление на основной |
| `WEBSERVER_PORT`, `WEBSERVER_PORT_SSL`, `CP_PORT`, `ADMINER_PORT` | Внешние порты сайта, панели управления и Adminer |
| `BASIC_AUTH_ENABLED` | Базовая авторизация Nginx: `RestrictedArea` — включена, `off` — выключена |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_ROOT_PASSWORD` | База данных |
| `HOST_USER_ID`, `HOST_GROUP_ID` | Пользователь и группа хоста, от имени которых работают контейнеры |
| `STOREFRONT_*` | Адрес публичного сайта, шаблон ссылки на товар, город и телефон по умолчанию — для ссылок в ответах API и письмах |
| `CATALOG_*_PHOTO_*` | Размеры уменьшенных копий фотографий товаров |
| `MAIL_FROM`, `MAIL_REPLY_TO` | Адрес отправителя и адрес для ответов — общие для всех почтовых сервисов |
| `MAILGUN_*`, `YANDEX_CLOUD_POSTBOX_*`, `RUSENDER_API_KEY` | Доступы почтовых сервисов; сервис выбирается в настройках панели управления |
| `PRONTOSMS_*` | Доступы SMS-шлюза |
| `XMLRIVER_*` | Доступ к статистике Яндекс Вордстат через xmlriver.com |

## Структура проекта

```
├── app/src/                Приложение Laravel
│   ├── Modules/            Модули платформы
│   ├── app/                Ядро: провайдеры, middleware, хелперы
│   ├── config/             Конфигурация
│   ├── public/open_api/    Общая спецификация OpenAPI
│   ├── tests/              Тесты
│   └── modules_statuses.json   Включённые модули
├── docker/build/           Dockerfile для PHP, Sphinx и панели управления
├── services/
│   ├── configs/            Конфигурация Nginx, PHP, MariaDB, Sphinx
│   ├── backups/            Скрипт и файлы резервных копий
│   ├── data/               Данные сервисов (не в git)
│   ├── logs/               Логи сервисов (не в git)
│   └── ssl/                Сертификаты (не в git)
├── docker-compose.yml
├── crontab                 Пример расписания для сервера
└── .env.example
```

## API

Спецификации в формате OpenAPI 3:

- общая — [app/src/public/open_api/openapi.yaml](app/src/public/open_api/openapi.yaml), после запуска доступна по адресу `/open_api/openapi.yaml` на домене API;
- по модулям — [MarketplaceModules](app/src/Modules/MarketplaceModules/openapi.yaml), [Blogs](app/src/Modules/Blogs/openapi.yaml), [Showcase](app/src/Modules/Showcase/openapi.yaml), [RealProjects](app/src/Modules/RealProjects/openapi.yaml), [ApiDocumentation](app/src/Modules/ApiDocumentation/openapi.yaml).

У модулей [ApiDocumentation](app/src/Modules/ApiDocumentation/README.md) и [ImageUploader](app/src/Modules/ImageUploader/README.md) есть собственные описания.

Авторизация построена на cookie-сессиях Laravel с двумя независимыми типами пользователей:

- `auth:client` — покупатели и авторы модулей;
- `auth:staff` — сотрудники магазина; доступ к действиям ограничен правами групп.

Чтобы при ошибке авторизации получить JSON, а не перенаправление, отправляйте заголовок `Accept: application/json`.

## Разработка модулей

Модуль — самостоятельный пакет в `app/src/Modules/<Имя>` со своими маршрутами, миграциями, сидерами и провайдером.

```
Modules/Example/
├── Config/              Конфигурация модуля
├── Database/
│   ├── Migrations/
│   └── Seeders/
├── Entities/            Модели Eloquent
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Providers/           Сервис-провайдер и провайдер маршрутов
├── Repositories/        Работа с данными
├── Routes/api.php       Маршруты API
├── Services/            Бизнес-логика
├── module.json          Описание модуля
└── openapi.yaml         Спецификация API модуля
```

Создать каркас модуля:

```bash
docker compose exec app php artisan module:make Example
```

Что нужно знать:

- **Описание и зависимости.** В `module.json` указываются название, версия, провайдеры и список `requires` — модули, без которых ваш не работает (например, `["Client"]`).
- **Включение.** Модуль активен, если в [modules_statuses.json](app/src/modules_statuses.json) для него стоит `true`. То же делают команды `module:enable` и `module:disable`.
- **База данных.** Миграции модуля применяются командой `php artisan module:migrate Example`, сидеры — `php artisan module:seed Example`.
- **Права и меню.** Модули с разделами в панели управления добавляют права сотрудников и пункты меню сидерами `StaffPermissionsSeeder` и `DashboardMenus` — см. пример в [Modules/Catalog/Database/Seeders](app/src/Modules/Catalog/Database/Seeders).
- **Документация.** Положите в корень модуля `openapi.yaml` с описанием его API.

Хорошие образцы для изучения — [Blogs](app/src/Modules/Blogs) (компактный модуль только с API) и [Catalog](app/src/Modules/Catalog) (большой модуль с правами, сидерами и консольными командами).

Каталог модулей, карточки, версии, отзывы и управление собственными модулями продавца реализованы в модуле `MarketplaceModules` — его API описан в [спецификации](app/src/Modules/MarketplaceModules/openapi.yaml).

## Обслуживание

В файле [crontab](crontab) — пример расписания для сервера:

- каждую минуту — планировщик Laravel (`schedule:run`);
- каждые 6 часов — перестроение поискового индекса Sphinx;
- ежедневно в 04:15 — резервная копия базы данных скриптом [mysql-backup.sh](services/backups/mysql/mysql-backup.sh). Копии складываются в `services/backups/mysql` и хранятся 7 дней.

Перед установкой расписания замените в нём доступы к базе данных и путь к проекту на свои.

Логи сервисов лежат в `services/logs`, логи приложения — в `services/logs/laravel`.

## Тесты

```bash
docker compose exec app vendor/bin/phpunit
```

## Лицензия

Aizberg распространяется по лицензии [GNU General Public License v3.0](LICENSE).
