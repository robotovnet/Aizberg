-- 10 синтетических модулей категории «Маркетплейсы» для MarketplaceModules.
-- MySQL 8.0+ / MariaDB 10.2+. Сначала примените все миграции MarketplaceModules.
-- Выполните ВЕСЬ файл в выбранной БД, в одном соединении (например, импортом).
-- При ошибке остановите выполнение и выполните ROLLBACK в том же соединении.
--
-- Создаются 10 авторов и 8 покупателей с адресами @marketplace-category.example.test.
-- Пароль тестовых клиентов: password. ID существующих клиентов не требуются.
-- Все названия студий, репозитории, описания и показатели вымышлены.
-- Ссылки на сайты и GitHub заполнены для интерфейса, реальные проекты не подразумеваются.
--
-- На каждый модуль: 6 тегов, 3 версии с changes, 3 скриншота, 3 вопроса
-- с ответами, 8 оценок, 3 отзыва с голосами, 6 добавлений в избранное, 8 покупок.
-- Все модули опубликованы: is_archived = 0, is_blocked = 0, deleted_at = NULL.
-- У free/free_trial цена и валюта NULL, как при создании через API.
--
-- Повторный запуск пропускает уже существующие install_slug этого набора целиком:
-- не дублирует и не перезаписывает модули и их связанные данные.
-- Старый marketplace_modules_fake_data.sql и его данные не затрагиваются.
-- Не запускайте два экземпляра этого файла одновременно.
--
-- Медиа: повторно используются пути из существующих модулей (сначала из этой
-- категории). SQL не создаёт файлы и не проверяет их наличие в хранилище.
-- Если подходящих путей нет, используются следующие демонстрационные пути:
--   marketplace-modules/icons/seed-mp-category-placeholder.png
--   marketplace-modules/screenshots/seed-mp-category-01.png (также 02 и 03).
-- Для отображения таких заглушек положите свои PNG в соответствующие каталоги
-- storage/app/public. При необходимости задайте свои пути в переменных ниже.

SET NAMES utf8mb4;
START TRANSACTION;

SET @mp_category_now = NOW();
SET @mp_category_password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';

SET @mp_category_icon = COALESCE((
    SELECT `icon_path` FROM `marketplace_modules`
    WHERE NULLIF(`icon_path`, '') IS NOT NULL AND `deleted_at` IS NULL
      AND `is_blocked` = 0 AND `is_archived` = 0
      AND `install_slug` NOT LIKE 'seed-mp-category-%'
    ORDER BY (`category` = 'Маркетплейсы') DESC, `id`
    LIMIT 1
), 'marketplace-modules/icons/seed-mp-category-placeholder.png');

SET @mp_category_screen_1 = COALESCE((
    SELECT `s`.`path` FROM `marketplace_module_screenshots` AS `s`
    JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`marketplace_module_id`
    WHERE NULLIF(`s`.`path`, '') IS NOT NULL AND `m`.`deleted_at` IS NULL
      AND `m`.`is_blocked` = 0 AND `m`.`is_archived` = 0
      AND `m`.`install_slug` NOT LIKE 'seed-mp-category-%'
    ORDER BY (`m`.`category` = 'Маркетплейсы') DESC, `m`.`id`, `s`.`position`, `s`.`id`
    LIMIT 1 OFFSET 0
), 'marketplace-modules/screenshots/seed-mp-category-01.png');

SET @mp_category_screen_2 = COALESCE((
    SELECT `s`.`path` FROM `marketplace_module_screenshots` AS `s`
    JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`marketplace_module_id`
    WHERE NULLIF(`s`.`path`, '') IS NOT NULL AND `m`.`deleted_at` IS NULL
      AND `m`.`is_blocked` = 0 AND `m`.`is_archived` = 0
      AND `m`.`install_slug` NOT LIKE 'seed-mp-category-%'
    ORDER BY (`m`.`category` = 'Маркетплейсы') DESC, `m`.`id`, `s`.`position`, `s`.`id`
    LIMIT 1 OFFSET 1
), 'marketplace-modules/screenshots/seed-mp-category-02.png');

SET @mp_category_screen_3 = COALESCE((
    SELECT `s`.`path` FROM `marketplace_module_screenshots` AS `s`
    JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`marketplace_module_id`
    WHERE NULLIF(`s`.`path`, '') IS NOT NULL AND `m`.`deleted_at` IS NULL
      AND `m`.`is_blocked` = 0 AND `m`.`is_archived` = 0
      AND `m`.`install_slug` NOT LIKE 'seed-mp-category-%'
    ORDER BY (`m`.`category` = 'Маркетплейсы') DESC, `m`.`id`, `s`.`position`, `s`.`id`
    LIMIT 1 OFFSET 2
), 'marketplace-modules/screenshots/seed-mp-category-03.png');

-- Клиенты. Адреса этого набора не пересекаются с общим SQL-набором.
CREATE TEMPORARY TABLE `tmp_mp_category_clients` (
    `seed_no` TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    `first_name` VARCHAR(60) NOT NULL,
    `last_name` VARCHAR(60) NOT NULL,
    `client_id` INT UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tmp_mp_category_clients` (`seed_no`, `first_name`, `last_name`) VALUES
    (1, 'Алексей', 'Громов'), (2, 'Марина', 'Лебедева'),
    (3, 'Илья', 'Соколов'), (4, 'Ольга', 'Беляева'),
    (5, 'Денис', 'Крылов'), (6, 'Елена', 'Тихонова'),
    (7, 'Михаил', 'Романов'), (8, 'Анна', 'Лазарева'),
    (9, 'Павел', 'Егоров'), (10, 'Виктория', 'Кузнецова'),
    (11, 'Артём', 'Мельников'), (12, 'Ирина', 'Захарова'),
    (13, 'Николай', 'Белов'), (14, 'Светлана', 'Власова'),
    (15, 'Роман', 'Данилов'), (16, 'Юлия', 'Котова'),
    (17, 'Максим', 'Никитин'), (18, 'Дарья', 'Фомина');

INSERT INTO `clients` (
    `first_name`, `last_name`, `phone`, `email`, `telegram`, `telegram_date`,
    `permanent_type`, `is_blocked`, `password`, `remember_token`,
    `created_at`, `updated_at`, `deleted_at`
)
SELECT `c`.`first_name`, `c`.`last_name`, CONCAT('+79990021', LPAD(`c`.`seed_no`, 3, '0')),
    CONCAT('user-', LPAD(`c`.`seed_no`, 2, '0'), '@marketplace-category.example.test'),
    NULL, NULL, 'N', 'N', @mp_category_password, NULL,
    DATE_SUB(@mp_category_now, INTERVAL 500 DAY),
    DATE_SUB(@mp_category_now, INTERVAL 500 DAY), NULL
FROM `tmp_mp_category_clients` AS `c`
WHERE NOT EXISTS (
    SELECT 1 FROM `clients` AS `existing`
    WHERE `existing`.`email` = CONCAT('user-', LPAD(`c`.`seed_no`, 2, '0'), '@marketplace-category.example.test')
);

-- email в clients индексирован, но не уникален: при совпадении берём один ID.
UPDATE `tmp_mp_category_clients` AS `c`
SET `c`.`client_id` = (
    SELECT MIN(`existing`.`id`) FROM `clients` AS `existing`
    WHERE `existing`.`email` = CONCAT('user-', LPAD(`c`.`seed_no`, 2, '0'), '@marketplace-category.example.test')
);

CREATE TEMPORARY TABLE `tmp_mp_category_numbers` (
    `n` TINYINT UNSIGNED NOT NULL PRIMARY KEY
) ENGINE=InnoDB;
INSERT INTO `tmp_mp_category_numbers` (`n`) VALUES (1), (2), (3), (4), (5), (6), (7), (8);

-- Данные модулей и индивидуальные тексты для истории версий и сообщества.
CREATE TEMPORARY TABLE `tmp_mp_category_modules` (
    `module_no` TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    `module_id` BIGINT UNSIGNED NULL,
    `is_new` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `display_name` VARCHAR(255) NOT NULL,
    `install_slug` VARCHAR(255) NOT NULL UNIQUE,
    `author` VARCHAR(255) NOT NULL,
    `license_type` VARCHAR(64) NOT NULL,
    `pricing_type` VARCHAR(32) NOT NULL,
    `price` DECIMAL(12,2) NULL,
    `currency` CHAR(3) NULL,
    `views_count` INT UNSIGNED NOT NULL,
    `short_description` TEXT NOT NULL,
    `detailed_description` LONGTEXT NOT NULL,
    `tags` JSON NOT NULL,
    `feature` TEXT NOT NULL,
    `improvement` TEXT NOT NULL,
    `question` TEXT NOT NULL,
    `answer` TEXT NOT NULL,
    `review_positive` TEXT NOT NULL,
    `review_caveat` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tmp_mp_category_modules` (
    `module_no`, `display_name`, `install_slug`, `author`, `license_type`,
    `pricing_type`, `price`, `currency`, `views_count`, `short_description`,
    `detailed_description`, `tags`, `feature`, `improvement`, `question`, `answer`,
    `review_positive`, `review_caveat`
) VALUES
    (1, 'Ozon Seller Connect', 'seed-mp-category-ozon-seller-connect', 'Север Интеграции', 'Other',
     'paid', 14900.00, 'RUB', 18420,
     'Синхронизация каталога, цен и остатков с Ozon. Заказы FBS, этикетки и статусы отправлений в панели магазина.',
     'Ozon Seller Connect объединяет работу интернет-магазина и кабинета продавца Ozon. Модуль сопоставляет товары по артикулу, передаёт цены и доступные остатки, загружает заказы и связывает их с заказами Aizberg.

Возможности: несколько кабинетов продавца, отдельные правила цен по складам, резервирование остатков, сборочные листы, печать этикеток и повторная отправка неуспешных операций. Менеджер видит причину ошибки и время последнего успешного обмена для каждого товара.

Для подключения нужны учётные данные API продавца, настроенная очередь задач и расписание фонового обмена. Начальную загрузку можно ограничить одной группой товаров; предварительный просмотр показывает изменения до отправки.

Лицензия рассчитана на один магазин и три кабинета продавца. В стоимость включены обновления и помощь с настройкой в течение 12 месяцев. Данные передаются напрямую между магазином и кабинетом продавца.',
     JSON_ARRAY('Ozon', 'FBS', 'остатки', 'заказы', 'этикетки', 'синхронизация'),
     'Добавлена пакетная печать этикеток для отправлений с нескольких складов.',
     'Исправлена повторная загрузка заказа при задержке ответа кабинета продавца.',
     'Можно подключить два кабинета Ozon к одному каталогу и разделить остатки по складам?',
     'Да. Для каждого кабинета укажите свои учётные данные и сопоставьте склады. В правилах обмена можно задать отдельный страховой остаток и исключить товары из конкретного кабинета.',
     'Подключили два кабинета и около 4200 артикулов. Заказы теперь попадают в общую очередь сборки, а этикетки печатаем сразу на смену. Первичное сопоставление заняло вечер, дальше обмен работает по расписанию.',
     'Обмен остатками стабильный. Для нестандартных комплектов пришлось вручную настроить соответствия, хотелось бы больше примеров таких настроек в документации.'),

    (2, 'Wildberries: склад и заказы', 'seed-mp-category-wildberries-stock-orders', 'Складовой Контур', 'Other',
     'paid', 17900.00, 'RUB', 22360,
     'Управление остатками, сборочными заданиями и поставками Wildberries с журналом обмена и контролем резервов.',
     'Модуль переносит ежедневные операции продавца Wildberries в Aizberg: загружает сборочные задания, обновляет остатки и помогает подготовить товары к отгрузке. Подходит магазинам с несколькими точками хранения и общим товарным каталогом.

Возможности: сопоставление номенклатуры по штрихкодам, резервы под заказы, группировка заданий по складу, лист подбора, печать стикеров и контроль завершения сборки. Ошибки обмена доступны в отдельном журнале с фильтрами по товару и периоду.

Перед запуском укажите токен кабинета, сопоставьте склады и проверьте штрихкоды. Очередь обрабатывает большие каталоги порциями; расписание синхронизации и размер страхового остатка настраиваются отдельно.

Поставка включает лицензию на один магазин, подключение до пяти складов и 12 месяцев обновлений. При временной недоступности внешнего сервиса задания сохраняются и отправляются повторно.',
     JSON_ARRAY('Wildberries', 'склад', 'поставки', 'штрихкоды', 'FBS', 'сборка'),
     'Добавлены листы подбора с группировкой по ячейкам хранения.',
     'Ускорено сопоставление штрихкодов при загрузке большого каталога.',
     'Как избежать продажи последней единицы одновременно в магазине и на Wildberries?',
     'Настройте резервирование при получении заказа и страховой остаток для внешнего склада. Например, при страховом остатке 2 модуль передаст доступное количество за вычетом двух единиц и активных резервов.',
     'Используем на складе с 1800 SKU. Сборщикам удобно работать по листу подбора, перестали отдельно переписывать номера заданий. После настройки страхового остатка стало меньше отмен из-за пересечения продаж.',
     'Основной сценарий работает хорошо. При первом подключении пришлось привести штрихкоды к единому виду. В журнале обмена хотелось бы сохранять выбранные фильтры между сеансами.'),

    (3, 'Яндекс Маркет — кабинет продавца', 'seed-mp-category-yandex-market-seller', 'Линия Обмена', 'Apache 2.0',
     'free_trial', NULL, NULL, 12680,
     'Выгрузка предложений на Яндекс Маркет, обработка заказов и управление ценами для нескольких кампаний.',
     'Модуль связывает каталог Aizberg с кабинетами Яндекс Маркета и объединяет работу с заказами разных кампаний. Менеджер управляет ассортиментом и ценами из привычной панели, а результаты обмена видит по каждому предложению.

Возможности: правила включения товаров в выгрузку, сопоставление категорий и характеристик, передача остатков, загрузка заказов и обновление статусов. Для разных кампаний можно задать свои наценки, склад и время обработки заказов.

Встроенная проверка выявляет незаполненные обязательные характеристики до отправки. Настройка начинается с подключения API, выбора кампании и пробной выгрузки небольшой группы товаров.

Пробный период — 14 дней с доступом ко всем функциям. Модуль поддерживает несколько кампаний одного магазина, ведёт историю обмена и позволяет повторить только операции с ошибками.',
     JSON_ARRAY('Яндекс Маркет', 'кампании', 'каталог', 'цены', 'заказы', 'API'),
     'Добавлены независимые правила цен для нескольких кампаний.',
     'Уточнены сообщения об отсутствии обязательных характеристик товара.',
     'Можно ли отправлять разные цены в две кампании из одной карточки товара?',
     'Да. В настройках каждой кампании выберите базовый тип цены и задайте правило наценки или округления. Перед публикацией результат можно проверить в предварительном просмотре выгрузки.',
     'За время пробного периода перенесли две кампании. Понравилась проверка характеристик до отправки: сразу видно, какие карточки нужно дополнить. Отдельные правила цен избавили от ручной подготовки таблиц.',
     'С заказами всё в порядке, но первоначальное сопоставление категорий занимает время. Для нашего ассортимента автоподбор предложил верные значения примерно в восьми случаях из десяти.'),

    (4, 'Мегамаркет Каталог', 'seed-mp-category-megamarket-catalog', 'Каталогика', 'MIT',
     'free', NULL, NULL, 7460,
     'Подготовка товарного фида для Мегамаркета: характеристики, изображения, доступность и проверка ошибок выгрузки.',
     'Мегамаркет Каталог формирует товарный фид из каталога Aizberg и помогает поддерживать предложения в актуальном состоянии. Модуль рассчитан на магазины, которым нужна регулярная выгрузка ассортимента без ручного редактирования файлов.

Возможности: отбор товаров по группам и брендам, сопоставление характеристик, выбор основного изображения, правила округления цен и исключение отсутствующих позиций. Перед публикацией проверяются обязательные поля, ссылки на изображения и дубли артикулов.

Фид обновляется по расписанию и доступен по постоянной ссылке. В журнале сохраняются длительность формирования, число предложений и список пропущенных карточек с пояснениями.

Модуль бесплатный, исходный код доступен по лицензии MIT. Для больших каталогов рекомендуется фоновая генерация через очередь; размер порции и срок хранения журналов задаются в настройках.',
     JSON_ARRAY('Мегамаркет', 'YML', 'фид', 'характеристики', 'каталог', 'экспорт'),
     'Добавлен отчёт по карточкам, исключённым из фида из-за ошибок.',
     'Снижено потребление памяти при формировании больших выгрузок.',
     'Можно выгружать только товары определённых брендов с положительным остатком?',
     'Да. В профиле выгрузки выберите бренды и включите фильтр доступного остатка. Дополнительно можно исключить отдельные группы или товары, даже если они подходят под основные условия.',
     'Используем для каталога из 9600 предложений. Фид собирается ночью, менеджер утром проверяет отчёт по пропущенным карточкам. Для бесплатного модуля набор настроек очень хороший.',
     'Выгрузка подходит для стандартного каталога. Для одной нестандартной характеристики понадобилось своё правило преобразования. Было бы удобно копировать правила между профилями.'),

    (5, 'Авито Pro: объявления из каталога', 'seed-mp-category-avito-pro', 'Витрина Софт', 'Other',
     'paid', 8900.00, 'RUB', 9840,
     'Автозагрузка объявлений на Авито с шаблонами описаний, настройками регионов и контролем результатов публикации.',
     'Авито Pro превращает товары каталога Aizberg в объявления с едиными правилами оформления. Решение подходит магазинам запчастей, техники и оборудования, которые размещают много однотипных предложений в нескольких регионах.

Возможности: шаблоны заголовков и описаний с подстановкой характеристик, выбор фотографий, адреса размещения и индивидуальные цены. Отсутствующие товары исключаются из следующей выгрузки, а менеджер видит результат обработки каждого объявления.

Можно создать несколько профилей для разных групп товаров и регионов. Перед отправкой модуль показывает готовые объявления и предупреждает о пустых обязательных полях, слишком длинных заголовках и повторяющихся внешних идентификаторах.

Лицензия действует для одного магазина и включает пять профилей выгрузки. Для публикации нужен подходящий доступ к автозагрузке в кабинете площадки; параметры кабинета вводятся в настройках модуля.',
     JSON_ARRAY('Авито', 'объявления', 'автозагрузка', 'регионы', 'шаблоны', 'фотографии'),
     'Добавлен предварительный просмотр объявления с выбранным шаблоном.',
     'Исправлено экранирование специальных символов в описаниях выгрузки.',
     'Как разместить одну товарную группу в нескольких городах с разными ценами?',
     'Создайте отдельный профиль для каждого города, выберите нужный адрес и настройте правило цены. Внешние идентификаторы объявлений формируются с учётом профиля, поэтому объявления разных городов не смешиваются.',
     'Перенесли в модуль размещение запчастей для трёх городов. Шаблоны с характеристиками заметно сократили подготовку описаний. Удобно проверить объявления до отправки и увидеть пустые поля.',
     'Публикация работает предсказуемо. Для длинных названий пришлось отдельно настроить шаблон заголовка. Хотелось бы редактировать несколько региональных профилей одновременно.'),

    (6, 'Единый остаток для маркетплейсов', 'seed-mp-category-unified-stock', 'Баланс Склад', 'Other',
     'paid', 24900.00, 'RUB', 16350,
     'Общий доступный остаток для Ozon, Wildberries и Яндекс Маркета с резервами, буфером и правилами распределения.',
     'Модуль рассчитывает доступный остаток по всем подключённым каналам продаж и передаёт его в адаптеры площадок. Помогает магазинам, которые продают один ассортимент на собственном сайте и нескольких маркетплейсах.

Возможности: учёт активных резервов, страховой буфер, лимиты по каналу, приоритеты складов и отдельные правила для комплектов. Изменения записываются в журнал: оператор видит исходный остаток, резервы и итоговое количество для каждой площадки.

Расчёт запускается после изменения заказа или складской операции и дополнительно сверяется по расписанию. Если один из каналов временно недоступен, его задания остаются в очереди с ограничением частоты повторных попыток.

Для работы нужны настроенные склады и подключённые адаптеры площадок. Лицензия включает один магазин, до десяти складов, обновления на год и помощь с проверкой правил распределения перед запуском.',
     JSON_ARRAY('остатки', 'резервы', 'мультиканальность', 'склады', 'комплекты', 'синхронизация'),
     'Добавлен расчёт доступности комплектов по остаткам компонентов.',
     'Устранена гонка резервов при одновременном поступлении заказов из двух каналов.',
     'Как считается остаток набора, если один из его компонентов продаётся отдельно?',
     'Модуль учитывает состав набора и резервы каждого компонента. Доступность набора ограничивается самым дефицитным компонентом, а его отдельные продажи уменьшают количество наборов при следующем расчёте.',
     'Самая полезная покупка для нашего склада. Раньше вручную делили остаток между тремя площадками, теперь видим расчёт и причины ограничений. Особенно помогла поддержка комплектов из общих компонентов.',
     'Гибкие настройки, но для первого запуска нужно внимательно разобрать правила резервов. Потратили два дня на проверку сценариев. Хорошо бы добавить готовые профили для типовых схем работы.'),

    (7, 'MarketPrice: цены и маржа', 'seed-mp-category-marketprice', 'Маржа Лаб', 'Other',
     'free_trial', NULL, NULL, 11470,
     'Расчёт цен для маркетплейсов с учётом закупки, комиссий, логистики и минимальной маржи по товарным группам.',
     'MarketPrice рассчитывает цены для нескольких площадок по единым правилам и показывает ожидаемую маржу до выгрузки. Модуль помогает учесть затраты и сравнить результат разных ценовых сценариев на одном ассортименте.

Возможности: формулы на основе закупочной цены, комиссии по категориям, фиксированные расходы на обработку, нижняя граница маржи и округление. Для акций можно создать отдельное правило с ограниченным периодом действия.

В предварительном расчёте доступны закупка, расходы, итоговая цена и ожидаемый доход по каждой позиции. Товары с недостаточной маржой попадают в отдельный отчёт; экспорт в CSV помогает согласовать изменения до публикации.

Пробный период длится 21 день. Параметры комиссий и логистики задаёт пользователь, поэтому расчёт можно адаптировать к условиям конкретного кабинета и регулярно обновлять без изменения кода.',
     JSON_ARRAY('ценообразование', 'маржа', 'комиссии', 'акции', 'аналитика', 'CSV'),
     'Добавлено сравнение базового и акционного сценариев в одном отчёте.',
     'Исправлено округление цены при совместном применении минимальной маржи и скидки.',
     'Можно запретить публикацию цены, если после комиссии маржа становится ниже 15%?',
     'Да. Укажите минимальную маржу в правиле товарной группы и выберите действие при нарушении: пересчитать цену или исключить позицию из отправки. Причина исключения будет видна в отчёте.',
     'На пробном периоде нашли товары, которые продавали почти без прибыли из-за логистики. Теперь согласуем расчёт до отправки цен. Особенно удобно сравнивать обычную продажу и участие в акции.',
     'Расчёты понятные, но расходы нужно поддерживать в актуальном состоянии самостоятельно. Не сразу разобрались с приоритетами правил. После консультации настроили формулы для всех групп.'),

    (8, 'Отзывы и вопросы: единый кабинет', 'seed-mp-category-reviews-inbox', 'Диалог Маркет', 'Apache 2.0',
     'free', NULL, NULL, 6280,
     'Отзывы и вопросы покупателей с разных площадок в одной очереди: ответственные, шаблоны и история ответов.',
     'Модуль собирает обращения покупателей из подключённых кабинетов маркетплейсов в единую рабочую очередь. Команда поддержки видит новые отзывы и вопросы, назначает ответственных и сохраняет историю общения по товару.

Возможности: фильтры по площадке, оценке и статусу обработки, шаблоны ответов, внутренние заметки и отметки о проверке. Негативные отзывы можно вынести в отдельную очередь для старшего менеджера.

Ответы отправляются после подтверждения сотрудником. Шаблоны подставляют название товара и данные обращения, а история позволяет проверить, кто подготовил и кто опубликовал сообщение.

Модуль бесплатный с открытым исходным кодом. Для каждой площадки подключается кабинет с доступом к обращениям. Частота загрузки и права сотрудников на публикацию настраиваются отдельно.',
     JSON_ARRAY('отзывы', 'вопросы', 'поддержка', 'шаблоны', 'модерация', 'репутация'),
     'Добавлено назначение ответственного для группы выбранных обращений.',
     'Исправлена повторная загрузка уже обработанных вопросов после синхронизации.',
     'Можно разрешить стажёру готовить ответы, но запретить самостоятельную публикацию?',
     'Да. Назначьте ему право подготовки черновиков, а публикацию оставьте старшему менеджеру. Черновики попадут в очередь проверки с указанием автора и выбранного шаблона.',
     'Два менеджера обрабатывают обращения из трёх кабинетов. Перестали отвечать на один вопрос одновременно, появилось понятное распределение. Шаблоны используем как заготовки и редактируем перед отправкой.',
     'Для небольшой команды возможностей хватает. Не хватает сводного отчёта по времени ответа за месяц. Фильтры по статусу удобные, особенно отдельная очередь черновиков.'),

    (9, 'Поставки FBO: планирование и упаковка', 'seed-mp-category-fbo-supply-planner', 'Логистика Плюс', 'Other',
     'paid', 19900.00, 'RUB', 8720,
     'Планирование поставок на склады маркетплейсов по продажам и запасам, упаковочные листы и контроль приёмки.',
     'Планировщик поставок помогает определить, какие товары и в каком количестве отправить на склады площадок. Модуль объединяет статистику продаж, текущие запасы и ожидаемые поставки в один рабочий документ.

Возможности: расчёт потребности на заданное число дней, минимальная партия, кратность упаковки, группировка по складам и печать упаковочных листов. Менеджер может скорректировать предложенное количество и сохранить причину изменения.

После отправки поставка проходит внутренние этапы: подготовка, упаковка, отгрузка и сверка приёмки. Расхождения фиксируются по товару и коробу, чтобы сотрудник мог подготовить обращение с нужными данными.

Для расчёта нужна история продаж и актуальные остатки. Лицензия включает один магазин, пять пользователей планировщика и год обновлений. Первую поставку можно собрать вручную и сравнить с рекомендацией модуля.',
     JSON_ARRAY('FBO', 'поставки', 'планирование', 'упаковка', 'приёмка', 'прогноз'),
     'Добавлено округление потребности до полной транспортной упаковки.',
     'Исправлен учёт товаров в пути при повторном расчёте поставки.',
     'Учитывается ли товар, который уже отправлен, но ещё не принят складом площадки?',
     'Да. Поставки на этапах отгрузки и ожидания приёмки входят в количество в пути. При расчёте новой потребности оно вычитается из рекомендации, чтобы не заказать повторное пополнение.',
     'Планируем поставки раз в неделю. Удобно, что видны товары в пути и можно округлить количество до короба. Склад получает один упаковочный лист вместо нескольких таблиц из разных кабинетов.',
     'Для товаров с равномерными продажами рекомендации хорошие. Сезонные позиции пока корректируем вручную. Хотелось бы сохранять отдельные параметры прогноза для сезонных групп.'),

    (10, 'MarketPulse — прибыль и возвраты', 'seed-mp-category-marketpulse', 'Точка Прибыли', 'Other',
     'paid', 12900.00, 'RUB', 13950,
     'Сводные отчёты по продажам, удержаниям, возвратам и прибыли на маркетплейсах с детализацией до товара и заказа.',
     'MarketPulse собирает данные продаж и финансовых отчётов подключённых площадок, чтобы сравнить выручку, расходы и прибыль в одном интерфейсе. Подходит владельцам магазинов и аналитикам, которые регулярно сводят отчёты вручную.

Возможности: разрезы по площадке, кабинету, бренду и товару, отдельный учёт возвратов, комиссий и логистики, сравнение периодов и выгрузка детализации. За каждым показателем можно открыть список исходных операций.

Модуль показывает дату последней загрузки и статус сверки периода. Поздние корректировки отчётов учитываются при повторном импорте, а дубли операций отсеиваются по идентификаторам источника.

Для расчёта прибыли необходимо заполнить закупочные цены и выбрать способ их учёта. Лицензия включает один магазин, до пяти кабинетов и 12 месяцев обновлений. Отчёты помогают анализировать продажи и сверять данные кабинетов.',
     JSON_ARRAY('аналитика', 'прибыль', 'возвраты', 'финансы', 'отчёты', 'экспорт'),
     'Добавлена детализация возвратов с привязкой к исходному заказу.',
     'Исправлен повторный учёт удержаний при импорте скорректированного отчёта.',
     'Что произойдёт, если повторно загрузить отчёт площадки с исправленными удержаниями?',
     'Модуль сопоставит операции по идентификаторам и обновит изменившиеся значения. Повторные строки не увеличат выручку, а период получит отметку о пересчёте и новую дату сверки.',
     'Наконец свели три кабинета в один отчёт по прибыли. Хорошо видно, какие товары дают оборот, но теряют маржу на возвратах. Детализация помогла найти расхождение в одном из удержаний.',
     'Отчёты полезные, но без корректных закупочных цен прибыль получается условной. Перед запуском пришлось привести историю закупок в порядок. Хотелось бы больше вариантов группировки в экспорте.');

-- Существующие модули набора полностью пропускаются, в том числе архивные.
UPDATE `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`install_slug` = `s`.`install_slug` COLLATE utf8mb4_unicode_ci
SET `s`.`module_id` = `m`.`id`, `s`.`is_new` = 0;

INSERT INTO `marketplace_modules` (
    `client_id`, `display_name`, `install_slug`, `website_url`, `source_code_url`,
    `license_type`, `license_url`, `changelog_url`, `pricing_type`, `price`, `currency`,
    `author_display_name`, `short_description`, `category`, `detailed_description`,
    `icon_path`, `ratings_count`, `aggregate_rating`, `views_count`,
    `is_archived`, `is_blocked`, `blocked_at`, `blocked_by_client_id`, `is_favorite`,
    `created_at`, `updated_at`, `deleted_at`
)
SELECT `c`.`client_id`, `s`.`display_name`, `s`.`install_slug`,
    CONCAT('https://modules.example.test/', `s`.`install_slug`),
    CONCAT('https://github.com/aizberg-demo-examples/', `s`.`install_slug`),
    `s`.`license_type`,
    CONCAT('https://github.com/aizberg-demo-examples/', `s`.`install_slug`, '/blob/main/LICENSE'),
    CONCAT('https://github.com/aizberg-demo-examples/', `s`.`install_slug`, '/blob/main/CHANGELOG.md'),
    `s`.`pricing_type`, `s`.`price`, `s`.`currency`, `s`.`author`, `s`.`short_description`,
    'Маркетплейсы', `s`.`detailed_description`, @mp_category_icon, 0, NULL, `s`.`views_count`,
    0, 0, NULL, NULL, IF(`s`.`module_no` IN (1, 3, 6, 10), 1, 0),
    DATE_SUB(@mp_category_now, INTERVAL (300 + `s`.`module_no` * 11) DAY),
    DATE_SUB(@mp_category_now, INTERVAL `s`.`module_no` DAY), NULL
FROM `tmp_mp_category_modules` AS `s`
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` = `s`.`module_no`
WHERE `s`.`is_new` = 1;

UPDATE `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`install_slug` = `s`.`install_slug` COLLATE utf8mb4_unicode_ci
SET `s`.`module_id` = `m`.`id`;

INSERT INTO `marketplace_module_tags` (`marketplace_module_id`, `tag`, `created_at`, `updated_at`)
SELECT `s`.`module_id`, JSON_UNQUOTE(JSON_EXTRACT(`s`.`tags`, CONCAT('$[', `n`.`n` - 1, ']'))),
    `m`.`created_at`, `m`.`updated_at`
FROM `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`module_id`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 6
WHERE `s`.`is_new` = 1;

-- changes — JSON-массив строк, как в StoreModuleChangelogRequest.
INSERT INTO `marketplace_module_versions` (
    `marketplace_module_id`, `version`, `release_date`, `changes`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`,
    CASE `n`.`n` WHEN 1 THEN '1.0.0' WHEN 2 THEN '1.4.0' ELSE '2.1.0' END,
    CASE `n`.`n` WHEN 1 THEN `m`.`created_at`
        WHEN 2 THEN DATE_SUB(@mp_category_now, INTERVAL (110 + `s`.`module_no` * 2) DAY)
        ELSE `m`.`updated_at` END,
    CASE `n`.`n`
        WHEN 1 THEN JSON_ARRAY(CONCAT('Первый стабильный выпуск «', `s`.`display_name`, '».'),
            'Добавлены мастер подключения, базовые настройки и журнал фоновых операций.')
        WHEN 2 THEN JSON_ARRAY(`s`.`feature`,
            'Добавлены экспорт журнала в CSV и фильтрация ошибок по периоду.')
        ELSE JSON_ARRAY(`s`.`improvement`,
            'Улучшены сообщения об ошибках доступа и повторная обработка фоновых заданий.',
            'Обновлены инструкция по установке и примеры настройки.') END,
    CASE `n`.`n` WHEN 1 THEN `m`.`created_at`
        WHEN 2 THEN DATE_SUB(@mp_category_now, INTERVAL (110 + `s`.`module_no` * 2) DAY)
        ELSE `m`.`updated_at` END,
    CASE `n`.`n` WHEN 1 THEN `m`.`created_at`
        WHEN 2 THEN DATE_SUB(@mp_category_now, INTERVAL (110 + `s`.`module_no` * 2) DAY)
        ELSE `m`.`updated_at` END
FROM `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`module_id`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 3
WHERE `s`.`is_new` = 1;

-- Позиции скриншотов начинаются с 0, как в репозитории MarketplaceModules.
INSERT INTO `marketplace_module_screenshots` (
    `marketplace_module_id`, `path`, `position`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`, CASE `n`.`n`
    WHEN 1 THEN @mp_category_screen_1 WHEN 2 THEN @mp_category_screen_2 ELSE @mp_category_screen_3 END,
    `n`.`n` - 1, `m`.`created_at`, `m`.`updated_at`
FROM `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`module_id`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 3
WHERE `s`.`is_new` = 1;

-- Покупки датированы раньше оценок и отзывов. Создаются также для бесплатных
-- модулей: API покупки допускает любой pricing_type и не хранит сумму платежа.
INSERT INTO `marketplace_module_purchases` (
    `marketplace_module_id`, `client_id`, `purchased_at`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`, `c`.`client_id`,
    DATE_SUB(@mp_category_now, INTERVAL (85 + `n`.`n` + `s`.`module_no`) DAY),
    DATE_SUB(@mp_category_now, INTERVAL (85 + `n`.`n` + `s`.`module_no`) DAY),
    DATE_SUB(@mp_category_now, INTERVAL (85 + `n`.`n` + `s`.`module_no`) DAY)
FROM `tmp_mp_category_modules` AS `s`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 8
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` = 10 + `n`.`n`
WHERE `s`.`is_new` = 1;

INSERT INTO `marketplace_module_ratings` (
    `marketplace_module_id`, `client_id`, `value`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`, `c`.`client_id`,
    CASE WHEN `n`.`n` = 1 THEN 5 WHEN `n`.`n` = 2 THEN 4 WHEN `n`.`n` = 3 THEN 5
        WHEN `n`.`n` = 8 AND MOD(`s`.`module_no`, 4) = 0 THEN 2
        WHEN MOD(`n`.`n` + `s`.`module_no`, 5) = 0 THEN 3
        WHEN MOD(`n`.`n` * `s`.`module_no`, 3) = 0 THEN 4 ELSE 5 END,
    DATE_SUB(@mp_category_now, INTERVAL (45 + `n`.`n` + `s`.`module_no`) DAY),
    DATE_SUB(@mp_category_now, INTERVAL (45 + `n`.`n` + `s`.`module_no`) DAY)
FROM `tmp_mp_category_modules` AS `s`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 8
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` = 10 + `n`.`n`
WHERE `s`.`is_new` = 1;

-- Агрегаты рассчитываются по вставленным оценкам, а не задаются произвольно.
UPDATE `marketplace_modules` AS `m`
JOIN `tmp_mp_category_modules` AS `s` ON `s`.`module_id` = `m`.`id` AND `s`.`is_new` = 1
JOIN (
    SELECT `marketplace_module_id`, COUNT(*) AS `rating_count`, ROUND(AVG(`value`), 2) AS `rating_avg`
    FROM `marketplace_module_ratings` GROUP BY `marketplace_module_id`
) AS `r` ON `r`.`marketplace_module_id` = `m`.`id`
SET `m`.`ratings_count` = `r`.`rating_count`, `m`.`aggregate_rating` = `r`.`rating_avg`;

INSERT INTO `marketplace_module_reviews` (
    `marketplace_module_id`, `client_id`, `review_text`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`, `c`.`client_id`, CASE `n`.`n`
    WHEN 1 THEN `s`.`review_positive`
    WHEN 2 THEN `s`.`review_caveat`
    ELSE CONCAT('Проверили «', `s`.`display_name`,
        '» сначала на копии магазина, затем подключили рабочий каталог. Пользуемся второй месяц. ',
        'После обновления журнал стал понятнее, ошибки доступа видны сразу. ',
        'Поддержка помогла настроить расписание фоновых задач; на нашем объёме всё работает стабильно.') END,
    DATE_SUB(@mp_category_now, INTERVAL (25 + `n`.`n` + `s`.`module_no`) DAY),
    DATE_SUB(@mp_category_now, INTERVAL (25 + `n`.`n` + `s`.`module_no`) DAY)
FROM `tmp_mp_category_modules` AS `s`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 3
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` = 10 + `n`.`n`
WHERE `s`.`is_new` = 1;

-- За полезность голосуют другие покупатели, не авторы отзывов.
INSERT INTO `marketplace_module_review_votes` (
    `marketplace_module_review_id`, `client_id`, `is_helpful`, `created_at`, `updated_at`
)
SELECT `r`.`id`, `c`.`client_id`, IF(MOD(`c`.`seed_no` + `s`.`module_no`, 5) = 0, 0, 1),
    DATE_ADD(`r`.`created_at`, INTERVAL (`c`.`seed_no` - 13) DAY),
    DATE_ADD(`r`.`created_at`, INTERVAL (`c`.`seed_no` - 13) DAY)
FROM `tmp_mp_category_modules` AS `s`
JOIN `marketplace_module_reviews` AS `r` ON `r`.`marketplace_module_id` = `s`.`module_id`
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` BETWEEN 14 AND 18
WHERE `s`.`is_new` = 1;

INSERT INTO `marketplace_module_favorites` (
    `marketplace_module_id`, `client_id`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`, `c`.`client_id`,
    DATE_SUB(@mp_category_now, INTERVAL (95 + `c`.`seed_no` + `s`.`module_no`) DAY),
    DATE_SUB(@mp_category_now, INTERVAL (95 + `c`.`seed_no` + `s`.`module_no`) DAY)
FROM `tmp_mp_category_modules` AS `s`
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` BETWEEN 11 AND 16
WHERE `s`.`is_new` = 1;

INSERT INTO `marketplace_module_questions` (
    `marketplace_module_id`, `client_id`, `question_text`, `created_at`, `updated_at`
)
SELECT `s`.`module_id`, `c`.`client_id`, CASE `n`.`n`
    WHEN 1 THEN `s`.`question`
    WHEN 2 THEN CONCAT('Хотим сначала проверить «', `s`.`display_name`,
        '» на копии магазина. Можно ограничить обработку небольшой группой товаров?')
    ELSE 'Что будет с настройками и очередью задач после обновления модуля до новой версии?' END,
    DATE_SUB(@mp_category_now, INTERVAL (12 + `n`.`n` + `s`.`module_no`) DAY),
    DATE_SUB(@mp_category_now, INTERVAL (12 + `n`.`n` + `s`.`module_no`) DAY)
FROM `tmp_mp_category_modules` AS `s`
JOIN `tmp_mp_category_numbers` AS `n` ON `n`.`n` <= 3
JOIN `tmp_mp_category_clients` AS `c` ON `c`.`seed_no` = 13 + `n`.`n`
WHERE `s`.`is_new` = 1;

INSERT INTO `marketplace_module_answers` (
    `marketplace_module_question_id`, `client_id`, `answer_text`, `created_at`, `updated_at`
)
SELECT `q`.`id`, `m`.`client_id`, CASE
    WHEN `q`.`question_text` = `s`.`question` COLLATE utf8mb4_unicode_ci THEN `s`.`answer`
    WHEN `q`.`question_text` LIKE 'Хотим сначала проверить%' THEN
        'Да. Создайте отдельный профиль и включите только тестовую группу товаров. Проверьте предварительный результат и журнал операций. Для внешних подключений используйте отдельные тестовые настройки, чтобы копия магазина не отправляла изменения в рабочий кабинет.'
    ELSE 'Настройки сохраняются при обновлении. Перед установкой новой версии завершите текущие фоновые задания и сделайте резервную копию. После обновления перезапустите обработчики очереди и проверьте одну операцию; подробный порядок указан в инструкции к релизу.' END,
    DATE_ADD(`q`.`created_at`, INTERVAL (2 + `s`.`module_no`) HOUR),
    DATE_ADD(`q`.`created_at`, INTERVAL (2 + `s`.`module_no`) HOUR)
FROM `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`module_id`
JOIN `marketplace_module_questions` AS `q` ON `q`.`marketplace_module_id` = `s`.`module_id`
WHERE `s`.`is_new` = 1;

COMMIT;

-- Контрольный результат: 10 строк; при первом запуске inserted_now = 1,
-- при повторном = 0. Если в категории уже были модули, они остаются в ней.
SELECT `m`.`id`, `m`.`display_name`, `m`.`install_slug`, `m`.`category`,
    `s`.`is_new` AS `inserted_now`, `m`.`pricing_type`, `m`.`price`, `m`.`currency`,
    `m`.`aggregate_rating`, `m`.`ratings_count`, `m`.`views_count`,
    (SELECT COUNT(*) FROM `marketplace_module_tags` WHERE `marketplace_module_id` = `m`.`id`) AS `tags_count`,
    (SELECT COUNT(*) FROM `marketplace_module_versions` WHERE `marketplace_module_id` = `m`.`id`) AS `versions_count`,
    (SELECT COUNT(*) FROM `marketplace_module_screenshots` WHERE `marketplace_module_id` = `m`.`id`) AS `screenshots_count`,
    (SELECT COUNT(*) FROM `marketplace_module_questions` WHERE `marketplace_module_id` = `m`.`id`) AS `questions_count`,
    (SELECT COUNT(*) FROM `marketplace_module_answers` AS `a`
        JOIN `marketplace_module_questions` AS `q` ON `q`.`id` = `a`.`marketplace_module_question_id`
        WHERE `q`.`marketplace_module_id` = `m`.`id`) AS `answers_count`,
    (SELECT COUNT(*) FROM `marketplace_module_reviews` WHERE `marketplace_module_id` = `m`.`id`) AS `reviews_count`,
    (SELECT COUNT(*) FROM `marketplace_module_review_votes` AS `v`
        JOIN `marketplace_module_reviews` AS `r` ON `r`.`id` = `v`.`marketplace_module_review_id`
        WHERE `r`.`marketplace_module_id` = `m`.`id`) AS `review_votes_count`,
    (SELECT COUNT(*) FROM `marketplace_module_favorites` WHERE `marketplace_module_id` = `m`.`id`) AS `favorites_count`,
    (SELECT COUNT(*) FROM `marketplace_module_purchases` WHERE `marketplace_module_id` = `m`.`id`) AS `purchases_count`
FROM `tmp_mp_category_modules` AS `s`
JOIN `marketplace_modules` AS `m` ON `m`.`id` = `s`.`module_id`
ORDER BY `s`.`module_no`;

DROP TEMPORARY TABLE `tmp_mp_category_modules`;
DROP TEMPORARY TABLE `tmp_mp_category_numbers`;
DROP TEMPORARY TABLE `tmp_mp_category_clients`;
