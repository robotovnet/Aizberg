# Модуль ImageUploader

REST API для загрузки и управления изображениями. Все маршруты доступны по префиксу `/image-uploader`.

Для доступа требуется авторизованный сотрудник (`auth:staff`) с правами:
- `imageuploader.images.view` — просмотр и получение данных;
- `imageuploader.images.management` — загрузка, изменение и удаление.

## Маршруты

| Метод | URL                     | Описание                                  | Тело запроса |
|-------|-------------------------|-------------------------------------------|--------------|
| POST  | `/image-uploader`   | Загрузить изображение                     | `image` (файл, обязательно), `title` (string, обязательно), `category` (string, опционально) |
| GET   | `/image-uploader`   | Получить список изображений (постранично) | `page` (int, опционально), `category` (string, опционально) |
| GET   | `/image-uploader/categories` | Получить список категорий изображений | — |
| GET   | `/image-uploader/{id}` | Получить данные конкретного изображения | — |
| PUT/PATCH | `/image-uploader/{id}` | Обновить название и категорию          | `title` (string, обязательно), `category` (string, опционально) |
| DELETE | `/image-uploader/{id}` | Удалить изображение                     | — |

Все ответы возвращаются в формате JSON. Значения `original_url` и `micro_url` указывают на файлы в хранилище `storage/app/public`.

