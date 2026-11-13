# ApiDocumentation

Модуль хранит несколько мультиязычных API-документаций, дерево разделов до трёх уровней и отдаёт готовое дерево для клиентского приложения. Публичные методы возвращают только документы с `is_published=true`; методы изменения защищены `web` + `auth:client`, как в `MarketplaceModules`.

## Основные маршруты

| Метод | URI | Назначение |
|---|---|---|
| `GET` | `/api/api-documentations` | Список опубликованных документаций |
| `GET` | `/api/api-documentations/search?q=...` | Поиск документаций по названию, описанию и тегам |
| `GET` | `/api/api-documentations/{slug}?locale=ru` | Итоговый документ со всеми разделами в одном дереве |
| `GET` | `/api/api-documentations/{slug}/search?q=...&locale=ru` | Поиск внутри документации |
| `GET` | `/api/api-documentations/manage` | Документации текущего клиента со всеми переводами |
| `GET` | `/api/api-documentations/manage/{id}` | Полный документ для редактирования |
| `POST` | `/api/api-documentations` | Создание документации |
| `PATCH` | `/api/api-documentations/{id}` | Частичное изменение документации |
| `DELETE` | `/api/api-documentations/{id}` | Удаление документации |
| `POST` | `/api/api-documentations/{id}/sections` | Создание раздела |
| `PATCH` | `/api/api-documentations/{id}/sections/{sectionId}` | Частичное изменение раздела |
| `DELETE` | `/api/api-documentations/{id}/sections/{sectionId}` | Удаление раздела вместе с подразделами |

## Создание документации

```json
{
  "slug": "seller-api",
  "name": {
    "ru": "API продавца",
    "en": "Seller API"
  },
  "short_description": {
    "ru": "Интеграция магазина с площадкой",
    "en": "Marketplace integration"
  },
  "tags": ["seller", "orders"],
  "default_locale": "ru",
  "is_published": true
}
```

Переводимые значения передаются словарём `код языка -> текст`. Публичная выдача выбирает язык из `locale`, затем язык документации по умолчанию. В методах `/manage` возвращаются все словари переводов.

## Создание раздела

```json
{
  "parent_id": null,
  "position": 10,
  "slug": "create-order",
  "title": {
    "ru": "Создать заказ",
    "en": "Create an order"
  },
  "description_blocks": [
    {
      "type": "text",
      "content": {
        "ru": "Метод создаёт новый заказ.",
        "en": "Creates a new order."
      }
    },
    {
      "type": "important",
      "content": {
        "ru": "Не повторяйте запрос с тем же идентификатором.",
        "en": "Do not reuse the same identifier."
      }
    },
    {
      "type": "card",
      "variant": "accent",
      "title": {
        "ru": "Перед началом",
        "en": "Before you start"
      },
      "content": {
        "ru": "Понадобятся:\n\n- **ключ API**;\n- раздел [Авторизация](section:12).",
        "en": "You will need:\n\n- an **API key**;\n- the [Authentication](section:12) section."
      }
    },
    {
      "type": "code",
      "language": "bash",
      "content": {
        "ru": "curl -X POST https://api.example.test/v1/orders",
        "en": "curl -X POST https://api.example.test/v1/orders"
      }
    },
    {
      "type": "section_link",
      "section_id": 12,
      "label": {
        "ru": "Авторизация",
        "en": "Authentication"
      }
    },
    {
      "type": "variable",
      "name": "order_id",
      "description": {
        "ru": "Идентификатор заказа",
        "en": "Order identifier"
      }
    },
    {
      "type": "table",
      "columns": [
        {"key": "field", "title": {"ru": "Поле", "en": "Field"}},
        {"key": "meaning", "title": {"ru": "Описание", "en": "Description"}}
      ],
      "rows": [
        {
          "cells": {
            "field": {"ru": "id", "en": "id"},
            "meaning": {"ru": "Идентификатор", "en": "Identifier"}
          }
        }
      ]
    }
  ],
  "endpoint": {
    "method": "POST",
    "url": "/v1/orders"
  },
  "request_body": {
    "content_type": "application/json",
    "schema": {
      "type": "object",
      "required": ["external_id"],
      "properties": {
        "external_id": {"type": "string"}
      }
    }
  },
  "request_headers": [
    {
      "name": "Client-Id",
      "required": true,
      "description": {"ru": "Идентификатор клиента", "en": "Client identifier"},
      "type": "string",
      "example": "12345"
    }
  ],
  "request_parameters": [
    {
      "name": "dry_run",
      "location": "query",
      "required": false,
      "description": {"ru": "Только проверка", "en": "Validate only"},
      "type": "boolean",
      "example": false
    }
  ],
  "request_example": {
    "external_id": "order-1000"
  },
  "responses": [
    {
      "status_code": "200",
      "name": {"ru": "Успешный ответ", "en": "Success"},
      "description": {"ru": "Заказ создан", "en": "Order created"},
      "type": "object",
      "content_type": "application/json",
      "schema": {
        "type": "object",
        "properties": {"id": {"type": "integer"}}
      },
      "example": {"id": 42}
    },
    {
      "status_code": "200",
      "name": {"ru": "Предупреждение", "en": "Warning"},
      "description": {"ru": "Заказ уже существовал", "en": "Order already existed"},
      "type": "object",
      "example": {"id": 42, "created": false}
    }
  ]
}
```

Повторяющиеся `status_code` разрешены. Благодаря этому у одного HTTP-кода могут быть несколько вариантов ответа и JSON-примеров.

## Блоки описания

| `type` | Поля | Назначение |
|---|---|---|
| `text` | `content` | Обычный текст, Markdown |
| `important` | `content` | Важное замечание, Markdown |
| `card` | `variant`, `title`, `content` | Карточка с заголовком: `variant` — `accent` (голубая рамка) или `default` (серая); `content` — Markdown |
| `code` | `content`, `language` | Пример кода |
| `section_link` | `section_id`, `label` | Ссылка на раздел этой документации |
| `variable` | `name`, `description` | Описание переменной |
| `table` | `columns`, `rows` | Таблица |

В `content` блоков `text`, `important` и `card` поддерживается Markdown:

- абзацы разделяются пустой строкой (`\n\n`);
- список — строки, начинающиеся с `- `;
- жирный текст — `**текст**`;
- внешняя ссылка — `[текст](https://...)`;
- ссылка на раздел этой же документации — `[текст](section:14)`, где `14` — `id` раздела.

Markdown сохраняется и отдаётся как есть, без экранирования; отрисовка — задача клиента. Публичная выдача локализует `title` и `content` карточки так же, как `content` у `text`. Поиск внутри документации учитывает текст карточек.
