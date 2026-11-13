-- Fake-данные для ручного тестирования MarketplaceModules.
-- СУБД: MySQL 8.0+ / MariaDB 10.2+.
-- Скрипт рассчитан на однократное выполнение на тестовой БД.
-- Все созданные клиенты используют пароль: password
-- Исходные PNG находятся в каталоге ./icons рядом с этим SQL-файлом.
-- Для работы icon_url они должны быть скопированы на сервер в каталог:
-- storage/app/public/marketplace-modules/icons

SET NAMES utf8mb4;
START TRANSACTION;

SET @seed_started_at = NOW();
SET @seed_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

-- --------------------------------------------------------------------------
-- Клиенты: 10 авторов модулей и 20 пользователей маркетплейса.
-- --------------------------------------------------------------------------

INSERT INTO `clients` (
    `first_name`,
    `last_name`,
    `phone`,
    `email`,
    `telegram`,
    `telegram_date`,
    `permanent_type`,
    `is_blocked`,
    `password`,
    `remember_token`,
    `created_at`,
    `updated_at`,
    `deleted_at`
) VALUES
    ('Алексей',   'Волков',    '+79991000001', 'marketplace-seed-01@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Марина',    'Соколова',  '+79991000002', 'marketplace-seed-02@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Дмитрий',   'Козлов',    '+79991000003', 'marketplace-seed-03@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Ольга',     'Морозова',  '+79991000004', 'marketplace-seed-04@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Илья',      'Новиков',   '+79991000005', 'marketplace-seed-05@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Елена',     'Фёдорова',  '+79991000006', 'marketplace-seed-06@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Сергей',    'Орлов',     '+79991000007', 'marketplace-seed-07@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Анна',      'Макарова',  '+79991000008', 'marketplace-seed-08@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Никита',    'Зайцев',    '+79991000009', 'marketplace-seed-09@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Татьяна',   'Павлова',   '+79991000010', 'marketplace-seed-10@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Андрей',    'Семёнов',   '+79991000011', 'marketplace-seed-11@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Ирина',     'Голубева',  '+79991000012', 'marketplace-seed-12@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Максим',    'Виноградов','+79991000013', 'marketplace-seed-13@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Светлана',  'Богданова', '+79991000014', 'marketplace-seed-14@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Роман',     'Воробьёв',  '+79991000015', 'marketplace-seed-15@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Наталья',   'Фролова',   '+79991000016', 'marketplace-seed-16@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Кирилл',    'Михайлов',  '+79991000017', 'marketplace-seed-17@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Юлия',      'Беляева',   '+79991000018', 'marketplace-seed-18@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Виктор',    'Тарасов',   '+79991000019', 'marketplace-seed-19@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Ксения',    'Комарова',  '+79991000020', 'marketplace-seed-20@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Павел',     'Крылов',    '+79991000021', 'marketplace-seed-21@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Валерия',   'Лебедева',  '+79991000022', 'marketplace-seed-22@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Артур',     'Соловьёв',  '+79991000023', 'marketplace-seed-23@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Дарья',     'Ефимова',   '+79991000024', 'marketplace-seed-24@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Георгий',   'Ковалёв',   '+79991000025', 'marketplace-seed-25@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Полина',    'Ильина',    '+79991000026', 'marketplace-seed-26@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Михаил',    'Гусев',     '+79991000027', 'marketplace-seed-27@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Вероника',  'Титова',    '+79991000028', 'marketplace-seed-28@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Степан',    'Кузьмин',   '+79991000029', 'marketplace-seed-29@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL),
    ('Алина',     'Кудрявцева','+79991000030', 'marketplace-seed-30@example.test', NULL, NULL, 'N', 'N', @seed_password, NULL, @seed_started_at, @seed_started_at, NULL);

CREATE TEMPORARY TABLE `tmp_marketplace_seed_clients` (
    `seed_no` TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    `client_id` INT UNSIGNED NOT NULL
);

INSERT INTO `tmp_marketplace_seed_clients` (`seed_no`, `client_id`)
SELECT
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`email`, '@', 1), '-', -1) AS UNSIGNED),
    `id`
FROM `clients`
WHERE `email` REGEXP '^marketplace-seed-[0-9]{2}@example[.]test$'
  AND `created_at` = @seed_started_at;

-- --------------------------------------------------------------------------
-- Модули.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_modules` (
    `client_id`,
    `display_name`,
    `install_slug`,
    `website_url`,
    `source_code_url`,
    `license_type`,
    `license_url`,
    `changelog_url`,
    `pricing_type`,
    `author_display_name`,
    `short_description`,
    `category`,
    `detailed_description`,
    `icon_path`,
    `ratings_count`,
    `aggregate_rating`,
    `views_count`,
    `is_archived`,
    `is_blocked`,
    `blocked_at`,
    `blocked_by_client_id`,
    `is_favorite`,
    `created_at`,
    `updated_at`,
    `deleted_at`
) VALUES
    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 1),
     'SEO Toolkit Pro', 'seed-seo-toolkit-pro',
     'https://seo-toolkit.example', 'https://github.com/example/seo-toolkit-pro',
     'MIT', 'https://github.com/example/seo-toolkit-pro/blob/main/LICENSE',
     'https://github.com/example/seo-toolkit-pro/blob/main/CHANGELOG.md', 'free_trial',
     'WebCraft Studio', 'Управление метатегами, sitemap.xml и микроразметкой без правки шаблонов.',
     'SEO',
     'Модуль проводит технический SEO-аудит, формирует карту сайта, настраивает canonical URL и шаблоны метатегов для разделов каталога.',
     'marketplace-modules/icons/seed-seo-toolkit-pro.png', 0, NULL, 1842, 0, 0, NULL, NULL, 1,
     DATE_SUB(@seed_started_at, INTERVAL 240 DAY), DATE_SUB(@seed_started_at, INTERVAL 8 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 2),
     'Cloud Backup Manager', 'seed-cloud-backup-manager',
     'https://cloud-backup.example', 'https://github.com/example/cloud-backup-manager',
     'Apache 2.0', 'https://github.com/example/cloud-backup-manager/blob/main/LICENSE',
     'https://github.com/example/cloud-backup-manager/releases', 'free_trial',
     'North Cloud', 'Резервные копии базы данных и файлов в S3-совместимые хранилища.',
     'Инструменты',
     'Создаёт резервные копии по расписанию, шифрует архивы, проверяет их целостность и отправляет уведомления о результате выполнения.',
     'marketplace-modules/icons/seed-cloud-backup-manager.png', 0, NULL, 1365, 0, 0, NULL, NULL, 1,
     DATE_SUB(@seed_started_at, INTERVAL 210 DAY), DATE_SUB(@seed_started_at, INTERVAL 14 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 3),
     'Sales Analytics Dashboard', 'seed-sales-analytics-dashboard',
     'https://sales-analytics.example', 'https://github.com/example/sales-analytics-dashboard',
     'GPLv3', 'https://github.com/example/sales-analytics-dashboard/blob/main/LICENSE',
     'https://github.com/example/sales-analytics-dashboard/blob/main/CHANGELOG.md', 'free',
     'Metric Lab', 'Дашборды продаж, воронки и когортный анализ для интернет-магазина.',
     'Аналитика',
     'Собирает показатели заказов и клиентов, строит интерактивные отчёты и позволяет выгружать агрегированные данные в CSV и XLSX.',
     'marketplace-modules/icons/seed-sales-analytics-dashboard.png', 0, NULL, 2214, 0, 0, NULL, NULL, 1,
     DATE_SUB(@seed_started_at, INTERVAL 180 DAY), DATE_SUB(@seed_started_at, INTERVAL 3 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 4),
     'Smart Checkout', 'seed-smart-checkout',
     'https://smart-checkout.example', 'https://github.com/example/smart-checkout',
     'MIT', 'https://github.com/example/smart-checkout/blob/main/LICENSE',
     'https://github.com/example/smart-checkout/releases', 'free_trial',
     'Checkout Factory', 'Быстрое оформление заказа с автозаполнением адреса и онлайн-оплатой.',
     'Продажи',
     'Упрощает форму оформления заказа, поддерживает промокоды, несколько платёжных сценариев и восстановление незавершённой корзины.',
     'marketplace-modules/icons/seed-smart-checkout.png', 0, NULL, 975, 0, 0, NULL, NULL, 0,
     DATE_SUB(@seed_started_at, INTERVAL 150 DAY), DATE_SUB(@seed_started_at, INTERVAL 21 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 5),
     'Telegram Notifications', 'seed-telegram-notifications',
     'https://telegram-notify.example', 'https://github.com/example/telegram-notifications',
     'BSD 3-Clause', 'https://github.com/example/telegram-notifications/blob/main/LICENSE',
     'https://github.com/example/telegram-notifications/blob/main/CHANGELOG.md', 'free',
     'Notify Team', 'Уведомления о заказах и системных событиях в Telegram.',
     'Коммуникации',
     'Подключает Telegram-бота, поддерживает разные чаты для менеджеров и администраторов, шаблоны сообщений и кнопки быстрых действий.',
     'marketplace-modules/icons/seed-telegram-notifications.png', 0, NULL, 3108, 0, 0, NULL, NULL, 1,
     DATE_SUB(@seed_started_at, INTERVAL 130 DAY), DATE_SUB(@seed_started_at, INTERVAL 5 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 6),
     'Image Optimizer', 'seed-image-optimizer',
     'https://image-optimizer.example', 'https://github.com/example/image-optimizer',
     'LGPLv3', 'https://github.com/example/image-optimizer/blob/main/LICENSE',
     'https://github.com/example/image-optimizer/releases', 'free',
     'Pixel Works', 'Сжатие изображений и генерация WebP-превью при загрузке.',
     'Медиа',
     'Оптимизирует JPEG и PNG без заметной потери качества, создаёт WebP-копии и повторно обрабатывает существующую медиатеку очередями.',
     'marketplace-modules/icons/seed-image-optimizer.png', 0, NULL, 1679, 0, 0, NULL, NULL, 0,
     DATE_SUB(@seed_started_at, INTERVAL 105 DAY), DATE_SUB(@seed_started_at, INTERVAL 11 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 7),
     'Security Audit', 'seed-security-audit',
     'https://security-audit.example', 'https://github.com/example/security-audit',
     'AGPLv3', 'https://github.com/example/security-audit/blob/main/LICENSE',
     'https://github.com/example/security-audit/blob/main/CHANGELOG.md', 'free_trial',
     'SafeCode', 'Аудит настроек безопасности, журнал входов и двухфакторная защита.',
     'Безопасность',
     'Проверяет права доступа и конфигурацию приложения, фиксирует подозрительные действия и поддерживает одноразовые коды для входа.',
     'marketplace-modules/icons/seed-security-audit.png', 0, NULL, 744, 0, 0, NULL, NULL, 0,
     DATE_SUB(@seed_started_at, INTERVAL 90 DAY), DATE_SUB(@seed_started_at, INTERVAL 18 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 8),
     'CRM Sync Connector', 'seed-crm-sync-connector',
     'https://crm-sync.example', 'https://github.com/example/crm-sync-connector',
     'Apache 2.0', 'https://github.com/example/crm-sync-connector/blob/main/LICENSE',
     'https://github.com/example/crm-sync-connector/releases', 'free_trial',
     'Integrate Hub', 'Двусторонняя синхронизация клиентов и заказов с CRM.',
     'Интеграции',
     'Передаёт лиды, покупателей и статусы заказов во внешнюю CRM, принимает изменения через webhook и ведёт журнал синхронизации.',
     'marketplace-modules/icons/seed-crm-sync-connector.png', 0, NULL, 1288, 0, 0, NULL, NULL, 0,
     DATE_SUB(@seed_started_at, INTERVAL 75 DAY), DATE_SUB(@seed_started_at, INTERVAL 7 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 9),
     'Delivery Calculator', 'seed-delivery-calculator',
     'https://delivery-calculator.example', 'https://github.com/example/delivery-calculator',
     'MIT', 'https://github.com/example/delivery-calculator/blob/main/LICENSE',
     NULL, 'free',
     'Logistics Dev', 'Расчёт сроков и стоимости доставки по городу и индексу.',
     'Доставка',
     'Тестовый новый модуль без вопросов, оценок, отзывов, тегов и истории версий для проверки пустых состояний интерфейса.',
     'marketplace-modules/icons/seed-delivery-calculator.png', 0, NULL, 93, 0, 0, NULL, NULL, 0,
     DATE_SUB(@seed_started_at, INTERVAL 20 DAY), DATE_SUB(@seed_started_at, INTERVAL 20 DAY), NULL),

    ((SELECT `client_id` FROM `tmp_marketplace_seed_clients` WHERE `seed_no` = 10),
     'Content Scheduler', 'seed-content-scheduler',
     'https://content-scheduler.example', 'https://github.com/example/content-scheduler',
     'Unlicense', 'https://github.com/example/content-scheduler/blob/main/LICENSE',
     NULL, 'free',
     'Editorial Tools', 'Отложенная публикация статей и автоматическое снятие материалов.',
     'Контент',
     'Тестовый новый модуль без связанных данных. Используется для проверки карточек и списков с отсутствующими пользовательскими активностями.',
     'marketplace-modules/icons/seed-content-scheduler.png', 0, NULL, 27, 0, 0, NULL, NULL, 0,
     DATE_SUB(@seed_started_at, INTERVAL 6 DAY), DATE_SUB(@seed_started_at, INTERVAL 6 DAY), NULL);

CREATE TEMPORARY TABLE `tmp_marketplace_seed_modules` (
    `module_no` TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    `module_id` BIGINT UNSIGNED NOT NULL,
    `question_count` TINYINT UNSIGNED NOT NULL,
    `rating_count` TINYINT UNSIGNED NOT NULL,
    `review_count` TINYINT UNSIGNED NOT NULL,
    `tag_count` TINYINT UNSIGNED NOT NULL,
    `version_count` TINYINT UNSIGNED NOT NULL
);

INSERT INTO `tmp_marketplace_seed_modules` (
    `module_no`, `module_id`, `question_count`, `rating_count`,
    `review_count`, `tag_count`, `version_count`
)
SELECT
    CASE `install_slug`
        WHEN 'seed-seo-toolkit-pro' THEN 1
        WHEN 'seed-cloud-backup-manager' THEN 2
        WHEN 'seed-sales-analytics-dashboard' THEN 3
        WHEN 'seed-smart-checkout' THEN 4
        WHEN 'seed-telegram-notifications' THEN 5
        WHEN 'seed-image-optimizer' THEN 6
        WHEN 'seed-security-audit' THEN 7
        WHEN 'seed-crm-sync-connector' THEN 8
        WHEN 'seed-delivery-calculator' THEN 9
        WHEN 'seed-content-scheduler' THEN 10
    END AS `module_no`,
    `id`,
    CASE `install_slug`
        WHEN 'seed-seo-toolkit-pro' THEN 12
        WHEN 'seed-cloud-backup-manager' THEN 15
        WHEN 'seed-sales-analytics-dashboard' THEN 10
        WHEN 'seed-smart-checkout' THEN 18
        WHEN 'seed-telegram-notifications' THEN 14
        WHEN 'seed-image-optimizer' THEN 20
        WHEN 'seed-security-audit' THEN 11
        WHEN 'seed-crm-sync-connector' THEN 16
        ELSE 0
    END AS `question_count`,
    CASE `install_slug`
        WHEN 'seed-seo-toolkit-pro' THEN 8
        WHEN 'seed-cloud-backup-manager' THEN 5
        WHEN 'seed-sales-analytics-dashboard' THEN 10
        WHEN 'seed-smart-checkout' THEN 3
        WHEN 'seed-telegram-notifications' THEN 7
        WHEN 'seed-image-optimizer' THEN 1
        WHEN 'seed-security-audit' THEN 6
        WHEN 'seed-crm-sync-connector' THEN 4
        ELSE 0
    END AS `rating_count`,
    CASE `install_slug`
        WHEN 'seed-seo-toolkit-pro' THEN 4
        WHEN 'seed-cloud-backup-manager' THEN 3
        WHEN 'seed-sales-analytics-dashboard' THEN 5
        WHEN 'seed-smart-checkout' THEN 2
        WHEN 'seed-telegram-notifications' THEN 4
        WHEN 'seed-image-optimizer' THEN 3
        WHEN 'seed-security-audit' THEN 2
        WHEN 'seed-crm-sync-connector' THEN 4
        ELSE 0
    END AS `review_count`,
    CASE WHEN `install_slug` IN (
        'seed-delivery-calculator', 'seed-content-scheduler'
    ) THEN 0 ELSE 4 END AS `tag_count`,
    CASE `install_slug`
        WHEN 'seed-seo-toolkit-pro' THEN 5
        WHEN 'seed-cloud-backup-manager' THEN 4
        WHEN 'seed-sales-analytics-dashboard' THEN 5
        WHEN 'seed-smart-checkout' THEN 3
        WHEN 'seed-telegram-notifications' THEN 4
        WHEN 'seed-image-optimizer' THEN 3
        WHEN 'seed-security-audit' THEN 4
        WHEN 'seed-crm-sync-connector' THEN 3
        ELSE 0
    END AS `version_count`
FROM `marketplace_modules`
WHERE `install_slug` IN (
    'seed-seo-toolkit-pro',
    'seed-cloud-backup-manager',
    'seed-sales-analytics-dashboard',
    'seed-smart-checkout',
    'seed-telegram-notifications',
    'seed-image-optimizer',
    'seed-security-audit',
    'seed-crm-sync-connector',
    'seed-delivery-calculator',
    'seed-content-scheduler'
);

CREATE TEMPORARY TABLE `tmp_marketplace_seed_numbers` (
    `n` TINYINT UNSIGNED NOT NULL PRIMARY KEY
);

INSERT INTO `tmp_marketplace_seed_numbers` (`n`) VALUES
    (1), (2), (3), (4), (5), (6), (7), (8), (9), (10),
    (11), (12), (13), (14), (15), (16), (17), (18), (19), (20);

-- --------------------------------------------------------------------------
-- Теги: по 4 тега у модулей 1-8; модули 9-10 остаются без тегов.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_module_tags` (
    `marketplace_module_id`, `tag`, `created_at`, `updated_at`
)
SELECT
    `tm`.`module_id`,
    CASE
        WHEN `tm`.`module_no` = 1 AND `n`.`n` = 1 THEN 'seo'
        WHEN `tm`.`module_no` = 1 AND `n`.`n` = 2 THEN 'sitemap'
        WHEN `tm`.`module_no` = 1 AND `n`.`n` = 3 THEN 'metadata'
        WHEN `tm`.`module_no` = 1 AND `n`.`n` = 4 THEN 'microdata'
        WHEN `tm`.`module_no` = 2 AND `n`.`n` = 1 THEN 'backup'
        WHEN `tm`.`module_no` = 2 AND `n`.`n` = 2 THEN 'storage'
        WHEN `tm`.`module_no` = 2 AND `n`.`n` = 3 THEN 's3'
        WHEN `tm`.`module_no` = 2 AND `n`.`n` = 4 THEN 'automation'
        WHEN `tm`.`module_no` = 3 AND `n`.`n` = 1 THEN 'analytics'
        WHEN `tm`.`module_no` = 3 AND `n`.`n` = 2 THEN 'reports'
        WHEN `tm`.`module_no` = 3 AND `n`.`n` = 3 THEN 'dashboard'
        WHEN `tm`.`module_no` = 3 AND `n`.`n` = 4 THEN 'ecommerce'
        WHEN `tm`.`module_no` = 4 AND `n`.`n` = 1 THEN 'checkout'
        WHEN `tm`.`module_no` = 4 AND `n`.`n` = 2 THEN 'payments'
        WHEN `tm`.`module_no` = 4 AND `n`.`n` = 3 THEN 'billing'
        WHEN `tm`.`module_no` = 4 AND `n`.`n` = 4 THEN 'cart'
        WHEN `tm`.`module_no` = 5 AND `n`.`n` = 1 THEN 'telegram'
        WHEN `tm`.`module_no` = 5 AND `n`.`n` = 2 THEN 'notifications'
        WHEN `tm`.`module_no` = 5 AND `n`.`n` = 3 THEN 'bot'
        WHEN `tm`.`module_no` = 5 AND `n`.`n` = 4 THEN 'alerts'
        WHEN `tm`.`module_no` = 6 AND `n`.`n` = 1 THEN 'images'
        WHEN `tm`.`module_no` = 6 AND `n`.`n` = 2 THEN 'optimization'
        WHEN `tm`.`module_no` = 6 AND `n`.`n` = 3 THEN 'webp'
        WHEN `tm`.`module_no` = 6 AND `n`.`n` = 4 THEN 'media'
        WHEN `tm`.`module_no` = 7 AND `n`.`n` = 1 THEN 'security'
        WHEN `tm`.`module_no` = 7 AND `n`.`n` = 2 THEN 'audit'
        WHEN `tm`.`module_no` = 7 AND `n`.`n` = 3 THEN '2fa'
        WHEN `tm`.`module_no` = 7 AND `n`.`n` = 4 THEN 'monitoring'
        WHEN `tm`.`module_no` = 8 AND `n`.`n` = 1 THEN 'integration'
        WHEN `tm`.`module_no` = 8 AND `n`.`n` = 2 THEN 'crm'
        WHEN `tm`.`module_no` = 8 AND `n`.`n` = 3 THEN 'webhook'
        WHEN `tm`.`module_no` = 8 AND `n`.`n` = 4 THEN 'synchronization'
    END AS `tag`,
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 4 + `n`.`n`) DAY),
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 4 + `n`.`n`) DAY)
FROM `tmp_marketplace_seed_modules` AS `tm`
JOIN `tmp_marketplace_seed_numbers` AS `n`
  ON `n`.`n` <= `tm`.`tag_count`;

-- --------------------------------------------------------------------------
-- История версий: 3-5 версий у модулей 1-8; модули 9-10 без истории.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_module_versions` (
    `marketplace_module_id`, `version`, `release_date`, `created_at`, `updated_at`
)
SELECT
    `tm`.`module_id`,
    CASE `n`.`n`
        WHEN 1 THEN '0.8.0'
        WHEN 2 THEN '0.9.0'
        WHEN 3 THEN '1.0.0'
        WHEN 4 THEN '1.1.0'
        WHEN 5 THEN '1.2.0'
    END AS `version`,
    DATE_SUB(
        @seed_started_at,
        INTERVAL ((`tm`.`version_count` - `n`.`n`) * 45 + `tm`.`module_no` * 3) DAY
    ) AS `release_date`,
    DATE_SUB(
        @seed_started_at,
        INTERVAL ((`tm`.`version_count` - `n`.`n`) * 45 + `tm`.`module_no` * 3) DAY
    ) AS `created_at`,
    DATE_SUB(
        @seed_started_at,
        INTERVAL ((`tm`.`version_count` - `n`.`n`) * 45 + `tm`.`module_no` * 3) DAY
    ) AS `updated_at`
FROM `tmp_marketplace_seed_modules` AS `tm`
JOIN `tmp_marketplace_seed_numbers` AS `n`
  ON `n`.`n` <= `tm`.`version_count`;

-- --------------------------------------------------------------------------
-- Вопросы: от 10 до 20 у модулей 1-8; модули 9-10 без вопросов.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_module_questions` (
    `marketplace_module_id`, `client_id`, `question_text`, `created_at`, `updated_at`
)
SELECT
    `tm`.`module_id`,
    `sc`.`client_id`,
    CASE MOD(`n`.`n` + `tm`.`module_no`, 12)
        WHEN 0 THEN CONCAT('Поддерживает ли «', `m`.`display_name`, '» PHP 8.2 и PHP 8.3?')
        WHEN 1 THEN CONCAT('Есть ли подробная инструкция по установке «', `m`.`display_name`, '»?')
        WHEN 2 THEN CONCAT('Можно ли использовать «', `m`.`display_name`, '» на нескольких сайтах?')
        WHEN 3 THEN CONCAT('Как перенести настройки «', `m`.`display_name`, '» с тестового сайта на рабочий?')
        WHEN 4 THEN CONCAT('Совместим ли модуль «', `m`.`display_name`, '» с последней версией Aizberg?')
        WHEN 5 THEN CONCAT('Насколько «', `m`.`display_name`, '» влияет на скорость загрузки страниц?')
        WHEN 6 THEN CONCAT('Можно ли отключить отдельные функции в «', `m`.`display_name`, '»?')
        WHEN 7 THEN CONCAT('Как часто выходят обновления для «', `m`.`display_name`, '»?')
        WHEN 8 THEN CONCAT('Предусмотрен ли импорт настроек для «', `m`.`display_name`, '»?')
        WHEN 9 THEN CONCAT('Есть ли журнал ошибок и событий у «', `m`.`display_name`, '»?')
        WHEN 10 THEN CONCAT('Работает ли «', `m`.`display_name`, '» в окружении Docker?')
        WHEN 11 THEN CONCAT('Куда обращаться за технической поддержкой по «', `m`.`display_name`, '»?')
    END AS `question_text`,
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 30 + `n`.`n`) HOUR),
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 30 + `n`.`n`) HOUR)
FROM `tmp_marketplace_seed_modules` AS `tm`
JOIN `marketplace_modules` AS `m`
  ON `m`.`id` = `tm`.`module_id`
JOIN `tmp_marketplace_seed_numbers` AS `n`
  ON `n`.`n` <= `tm`.`question_count`
JOIN `tmp_marketplace_seed_clients` AS `sc`
  ON `sc`.`seed_no` = 11 + MOD(`tm`.`module_no` * 3 + `n`.`n` - 1, 20);

-- --------------------------------------------------------------------------
-- Ответы: псевдослучайно примерно на 80% вопросов; часть остаётся без ответа.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_module_answers` (
    `marketplace_module_question_id`, `client_id`, `answer_text`, `created_at`, `updated_at`
)
SELECT
    `q`.`id`,
    `sc`.`client_id`,
    CASE MOD(`q`.`id` + `tm`.`module_no`, 8)
        WHEN 0 THEN 'Да, текущая версия это поддерживает. После установки рекомендуем очистить кеш приложения.'
        WHEN 1 THEN 'Подробная инструкция находится в README репозитория. Если возникнут сложности, приложите версию PHP и текст ошибки.'
        WHEN 2 THEN 'Можно. Настройки для каждого сайта хранятся отдельно и не конфликтуют между собой.'
        WHEN 3 THEN 'Экспортируйте конфигурацию на тестовом сайте и импортируйте полученный файл в рабочем окружении.'
        WHEN 4 THEN 'Совместимость проверена с актуальным стабильным релизом Aizberg. Старые версии могут потребовать обновления зависимостей.'
        WHEN 5 THEN 'Основные операции выполняются через очередь, поэтому влияние на время ответа страниц минимально.'
        WHEN 6 THEN 'Да, функции включаются независимо в настройках модуля. После изменения параметров сохраните конфигурацию.'
        WHEN 7 THEN 'Обновления выпускаются по мере исправления ошибок и появления новых возможностей. История доступна на вкладке версий.'
    END AS `answer_text`,
    DATE_ADD(`q`.`created_at`, INTERVAL (2 + MOD(`q`.`id`, 36)) HOUR),
    DATE_ADD(`q`.`created_at`, INTERVAL (2 + MOD(`q`.`id`, 36)) HOUR)
FROM `marketplace_module_questions` AS `q`
JOIN `tmp_marketplace_seed_modules` AS `tm`
  ON `tm`.`module_id` = `q`.`marketplace_module_id`
JOIN `tmp_marketplace_seed_clients` AS `sc`
  ON `sc`.`seed_no` = CASE
      WHEN MOD(`q`.`id`, 4) = 0 THEN 11 + MOD(`q`.`id` + `tm`.`module_no`, 20)
      ELSE `tm`.`module_no`
  END
WHERE MOD(`q`.`id` * 7 + `tm`.`module_no` * 3, 10) < 8;

-- --------------------------------------------------------------------------
-- Рейтинги: 1-10 оценок у модулей 1-8; модули 9-10 без оценок.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_module_ratings` (
    `marketplace_module_id`, `client_id`, `value`, `created_at`, `updated_at`
)
SELECT
    `tm`.`module_id`,
    `sc`.`client_id`,
    CASE MOD(`n`.`n` + `tm`.`module_no`, 6)
        WHEN 0 THEN 1
        WHEN 1 THEN 3
        WHEN 2 THEN 3
        ELSE 5
    END AS `value`,
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 7 + `n`.`n`) DAY),
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 7 + `n`.`n`) DAY)
FROM `tmp_marketplace_seed_modules` AS `tm`
JOIN `tmp_marketplace_seed_numbers` AS `n`
  ON `n`.`n` <= `tm`.`rating_count`
JOIN `tmp_marketplace_seed_clients` AS `sc`
  ON `sc`.`seed_no` = 10 + `n`.`n`;

-- Обновляем денормализованные агрегаты marketplace_modules.
UPDATE `marketplace_modules` AS `m`
JOIN `tmp_marketplace_seed_modules` AS `tm`
  ON `tm`.`module_id` = `m`.`id`
LEFT JOIN (
    SELECT
        `marketplace_module_id`,
        COUNT(*) AS `ratings_count`,
        ROUND(AVG(`value`), 2) AS `aggregate_rating`
    FROM `marketplace_module_ratings`
    GROUP BY `marketplace_module_id`
) AS `r`
  ON `r`.`marketplace_module_id` = `m`.`id`
SET
    `m`.`ratings_count` = COALESCE(`r`.`ratings_count`, 0),
    `m`.`aggregate_rating` = `r`.`aggregate_rating`;

-- --------------------------------------------------------------------------
-- Отзывы: 2-5 у модулей 1-8; модули 9-10 без отзывов.
-- --------------------------------------------------------------------------

INSERT INTO `marketplace_module_reviews` (
    `marketplace_module_id`, `client_id`, `review_text`, `created_at`, `updated_at`
)
SELECT
    `tm`.`module_id`,
    `sc`.`client_id`,
    CASE MOD(`n`.`n` + `tm`.`module_no`, 6)
        WHEN 0 THEN CONCAT('Используем «', `m`.`display_name`, '» уже несколько недель. Установка простая, документация понятная, всё работает стабильно.')
        WHEN 1 THEN CONCAT('Модуль «', `m`.`display_name`, '» решил нашу задачу без доработок. Особенно понравились понятные настройки.')
        WHEN 2 THEN CONCAT('Хороший модуль. Для настройки «', `m`.`display_name`, '» потребовалось немного времени, зато результат полностью устраивает.')
        WHEN 3 THEN CONCAT('После обновления «', `m`.`display_name`, '» работает заметно быстрее. Поддержка ответила на вопрос в тот же день.')
        WHEN 4 THEN CONCAT('Функций у «', `m`.`display_name`, '» достаточно для рабочего проекта. Хотелось бы больше примеров в документации.')
        WHEN 5 THEN CONCAT('Проверили «', `m`.`display_name`, '» на тестовом окружении и перенесли в production. Ошибок пока не обнаружили.')
    END AS `review_text`,
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 5 + `n`.`n`) DAY),
    DATE_SUB(@seed_started_at, INTERVAL (`tm`.`module_no` * 5 + `n`.`n` - 1) DAY)
FROM `tmp_marketplace_seed_modules` AS `tm`
JOIN `marketplace_modules` AS `m`
  ON `m`.`id` = `tm`.`module_id`
JOIN `tmp_marketplace_seed_numbers` AS `n`
  ON `n`.`n` <= `tm`.`review_count`
JOIN `tmp_marketplace_seed_clients` AS `sc`
  ON `sc`.`seed_no` = 11 + MOD(`tm`.`module_no` * 2 + `n`.`n` - 1, 20);

COMMIT;

-- --------------------------------------------------------------------------
-- Контрольная выборка: показывает количество созданных связанных записей.
-- --------------------------------------------------------------------------

SELECT
    `m`.`id`,
    `m`.`display_name`,
    `m`.`install_slug`,
    (SELECT COUNT(*) FROM `marketplace_module_questions` AS `q`
      WHERE `q`.`marketplace_module_id` = `m`.`id`) AS `questions_count`,
    (SELECT COUNT(*)
       FROM `marketplace_module_answers` AS `a`
       JOIN `marketplace_module_questions` AS `q` ON `q`.`id` = `a`.`marketplace_module_question_id`
      WHERE `q`.`marketplace_module_id` = `m`.`id`) AS `answers_count`,
    (SELECT COUNT(*) FROM `marketplace_module_ratings` AS `r`
      WHERE `r`.`marketplace_module_id` = `m`.`id`) AS `ratings_count`,
    (SELECT COUNT(*) FROM `marketplace_module_reviews` AS `r`
      WHERE `r`.`marketplace_module_id` = `m`.`id`) AS `reviews_count`,
    (SELECT COUNT(*) FROM `marketplace_module_tags` AS `t`
      WHERE `t`.`marketplace_module_id` = `m`.`id`) AS `tags_count`,
    (SELECT COUNT(*) FROM `marketplace_module_versions` AS `v`
      WHERE `v`.`marketplace_module_id` = `m`.`id`) AS `versions_count`,
    `m`.`aggregate_rating`
FROM `marketplace_modules` AS `m`
WHERE `m`.`install_slug` LIKE 'seed-%'
ORDER BY `m`.`id`;

DROP TEMPORARY TABLE IF EXISTS `tmp_marketplace_seed_numbers`;
DROP TEMPORARY TABLE IF EXISTS `tmp_marketplace_seed_modules`;
DROP TEMPORARY TABLE IF EXISTS `tmp_marketplace_seed_clients`;
