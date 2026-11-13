<?php

declare(strict_types=1);

namespace Modules\ApiDocumentation\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;
use RuntimeException;

class JsonPlaceholderDocumentationSeeder extends Seeder
{
    private const OWNER_ID = 2;

    public function run(): void
    {
        if (!DB::table('clients')->where('id', self::OWNER_ID)->exists()) {
            throw new RuntimeException('Невозможно создать пример ApiDocumentation: клиент с id=2 не найден.');
        }

        DB::transaction(function (): void {
            $documentation = ApiDocumentation::withTrashed()->firstOrNew(['slug' => 'jsonplaceholder-api']);
            $documentation->fill([
                'client_id' => self::OWNER_ID,
                'name' => [
                    'ru' => 'JSONPlaceholder API',
                    'en' => 'JSONPlaceholder API',
                ],
                'short_description' => [
                    'ru' => 'Небольшой бесплатный REST API с тестовыми публикациями и комментариями.',
                    'en' => 'A small free REST API with fake posts and comments for testing.',
                ],
                'tags' => ['REST', 'JSON', 'testing', 'posts', 'comments'],
                'default_locale' => 'ru',
                'is_published' => true,
            ]);
            $documentation->deleted_at = null;
            $documentation->save();

            ApiDocumentationSection::query()
                ->where('api_documentation_id', $documentation->id)
                ->delete();

            $overview = $this->section($documentation, null, 1, 10, 'overview', [
                'ru' => 'Обзор',
                'en' => 'Overview',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'text',
                        'content' => [
                            'ru' => 'JSONPlaceholder — бесплатный тестовый REST API. Данные имитируют публикации, комментарии, пользователей, альбомы, фотографии и задачи.',
                            'en' => 'JSONPlaceholder is a free fake REST API with posts, comments, users, albums, photos, and todos.',
                        ],
                    ],
                    [
                        'type' => 'important',
                        'content' => [
                            'ru' => 'Операции записи имитируются: сервер возвращает результат, но фактически не сохраняет изменения.',
                            'en' => 'Write operations are faked: the server returns a result but does not persist changes.',
                        ],
                    ],
                    [
                        'type' => 'variable',
                        'name' => 'base_url',
                        'description' => [
                            'ru' => 'Базовый URL: https://jsonplaceholder.typicode.com',
                            'en' => 'Base URL: https://jsonplaceholder.typicode.com',
                        ],
                    ],
                ],
            ]);

            $quickStart = $this->section($documentation, $overview, 2, 10, 'quick-start', [
                'ru' => 'Быстрый старт',
                'en' => 'Quick start',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'text',
                        'content' => [
                            'ru' => 'Для чтения данных авторизация не нужна. Ответы возвращаются в JSON.',
                            'en' => 'No authentication is required. Responses are returned as JSON.',
                        ],
                    ],
                    [
                        'type' => 'code',
                        'language' => 'bash',
                        'content' => [
                            'ru' => "curl -H \"Accept: application/json\" https://jsonplaceholder.typicode.com/posts/1",
                            'en' => "curl -H \"Accept: application/json\" https://jsonplaceholder.typicode.com/posts/1",
                        ],
                    ],
                ],
                'endpoint' => ['method' => 'GET', 'url' => '/posts/1'],
                'request_headers' => [
                    $this->parameter('Accept', 'header', false, 'string', 'application/json',
                        'Желаемый формат ответа.', 'Preferred response format.'),
                ],
                'responses' => [
                    $this->response('200', 'Успех', 'Success', 'Публикация получена.', 'Post returned.', 'object', [
                        'type' => 'object',
                        'required' => ['userId', 'id', 'title', 'body'],
                        'properties' => [
                            'userId' => ['type' => 'integer'],
                            'id' => ['type' => 'integer'],
                            'title' => ['type' => 'string'],
                            'body' => ['type' => 'string'],
                        ],
                    ], ['userId' => 1, 'id' => 1, 'title' => 'sunt aut facere...', 'body' => 'quia et suscipit...']),
                ],
            ]);

            $posts = $this->section($documentation, null, 1, 20, 'posts', [
                'ru' => 'Публикации',
                'en' => 'Posts',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'text',
                        'content' => [
                            'ru' => 'Ресурс /posts содержит 100 тестовых публикаций и поддерживает чтение, фильтрацию и имитацию CRUD.',
                            'en' => 'The /posts resource contains 100 fake posts and supports reads, filtering, and simulated CRUD.',
                        ],
                    ],
                    [
                        'type' => 'table',
                        'columns' => [
                            ['key' => 'field', 'title' => ['ru' => 'Поле', 'en' => 'Field']],
                            ['key' => 'type', 'title' => ['ru' => 'Тип', 'en' => 'Type']],
                            ['key' => 'meaning', 'title' => ['ru' => 'Описание', 'en' => 'Description']],
                        ],
                        'rows' => [
                            ['cells' => [
                                'field' => ['ru' => 'id', 'en' => 'id'],
                                'type' => ['ru' => 'integer', 'en' => 'integer'],
                                'meaning' => ['ru' => 'ID публикации', 'en' => 'Post ID'],
                            ]],
                            ['cells' => [
                                'field' => ['ru' => 'userId', 'en' => 'userId'],
                                'type' => ['ru' => 'integer', 'en' => 'integer'],
                                'meaning' => ['ru' => 'ID автора', 'en' => 'Author ID'],
                            ]],
                            ['cells' => [
                                'field' => ['ru' => 'title / body', 'en' => 'title / body'],
                                'type' => ['ru' => 'string', 'en' => 'string'],
                                'meaning' => ['ru' => 'Заголовок и текст', 'en' => 'Title and body'],
                            ]],
                        ],
                    ],
                    [
                        'type' => 'section_link',
                        'section_id' => (int) $quickStart->id,
                        'label' => ['ru' => 'Сначала прочитайте быстрый старт', 'en' => 'Read the quick start first'],
                    ],
                ],
            ]);

            $this->section($documentation, $posts, 2, 10, 'list-posts', [
                'ru' => 'Список и фильтрация публикаций',
                'en' => 'List and filter posts',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'code',
                        'language' => 'javascript',
                        'content' => [
                            'ru' => "fetch('https://jsonplaceholder.typicode.com/posts?userId=1').then(r => r.json())",
                            'en' => "fetch('https://jsonplaceholder.typicode.com/posts?userId=1').then(r => r.json())",
                        ],
                    ],
                ],
                'endpoint' => ['method' => 'GET', 'url' => '/posts'],
                'request_parameters' => [
                    $this->parameter('userId', 'query', false, 'integer', 1,
                        'Фильтр по автору.', 'Filter by author.'),
                ],
                'responses' => [
                    $this->response('200', 'Список', 'List', 'Массив публикаций.', 'An array of posts.', 'array', [
                        'type' => 'array',
                        'items' => ['$ref' => '#/components/schemas/Post'],
                    ], [['userId' => 1, 'id' => 1, 'title' => 'Example title', 'body' => 'Example body']]),
                ],
            ]);

            $postDetails = $this->section($documentation, $posts, 2, 20, 'get-post', [
                'ru' => 'Получить публикацию',
                'en' => 'Get a post',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'variable',
                        'name' => 'postId',
                        'description' => ['ru' => 'Числовой ID публикации.', 'en' => 'Numeric post ID.'],
                    ],
                ],
                'endpoint' => ['method' => 'GET', 'url' => '/posts/{postId}'],
                'request_parameters' => [
                    $this->parameter('postId', 'path', true, 'integer', 1,
                        'Идентификатор публикации.', 'Post identifier.'),
                    $this->parameter('preview', 'cookie', false, 'boolean', false,
                        'Демонстрация параметра cookie; JSONPlaceholder его игнорирует.', 'Cookie parameter demo; ignored by JSONPlaceholder.'),
                ],
                'responses' => [
                    $this->response('200', 'Найдена', 'Found', 'Публикация.', 'A post.', 'object', [
                        'type' => 'object',
                        'required' => ['userId', 'id', 'title', 'body'],
                    ], ['userId' => 1, 'id' => 1, 'title' => 'Example title', 'body' => 'Example body']),
                    $this->response('404', 'Не найдена', 'Not found', 'Для неизвестного ID возвращается пустой объект.', 'An unknown ID returns an empty object.', 'object', [
                        'type' => 'object',
                    ], []),
                ],
            ]);

            $createPost = $this->section($documentation, $posts, 2, 30, 'create-post', [
                'ru' => 'Создать публикацию',
                'en' => 'Create a post',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'important',
                        'content' => [
                            'ru' => 'Создание только имитируется. Новая публикация получает тестовый ID 101.',
                            'en' => 'Creation is simulated. The new post receives the fake ID 101.',
                        ],
                    ],
                    [
                        'type' => 'section_link',
                        'section_id' => (int) $postDetails->id,
                        'label' => ['ru' => 'Формат публикации', 'en' => 'Post representation'],
                    ],
                ],
                'endpoint' => ['method' => 'POST', 'url' => '/posts'],
                'request_body' => [
                    'content_type' => 'application/json; charset=UTF-8',
                    'schema' => [
                        'type' => 'object',
                        'required' => ['title', 'body', 'userId'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'body' => ['type' => 'string'],
                            'userId' => ['type' => 'integer'],
                        ],
                    ],
                ],
                'request_headers' => [
                    $this->parameter('Content-Type', 'header', true, 'string', 'application/json; charset=UTF-8',
                        'Формат тела запроса.', 'Request body format.'),
                    $this->parameter('X-Request-Id', 'header', false, 'string', 'demo-123',
                        'Демонстрационный идентификатор запроса.', 'Demo request identifier.'),
                ],
                'request_parameters' => [
                    $this->parameter('payload', 'body', true, 'object', ['title' => 'foo'],
                        'Тело запроса; демонстрирует location=body.', 'Request payload; demonstrates location=body.'),
                ],
                'request_example' => ['title' => 'foo', 'body' => 'bar', 'userId' => 1],
                'responses' => [
                    $this->response('201', 'Создана', 'Created', 'Имитированный результат создания.', 'Simulated creation result.', 'object', [
                        'type' => 'object',
                        'required' => ['id', 'title', 'body', 'userId'],
                    ], ['id' => 101, 'title' => 'foo', 'body' => 'bar', 'userId' => 1]),
                    $this->response('201', 'Альтернативный пример', 'Alternative example', 'Второй вариант того же HTTP-кода демонстрирует повторяющиеся status_code.', 'A second variant demonstrates duplicate status codes.', 'object', null, ['id' => 101, 'title' => 'Another post', 'body' => 'Example', 'userId' => 2]),
                    $this->response('default', 'Неожиданный ответ', 'Unexpected response', 'Любой неописанный ответ.', 'Any undocumented response.', 'object', null, []),
                ],
            ]);

            $writeOperations = $this->section($documentation, $posts, 2, 40, 'write-operations', [
                'ru' => 'Изменение и удаление',
                'en' => 'Update and delete',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'text',
                        'content' => [
                            'ru' => 'PUT заменяет ресурс целиком, PATCH изменяет отдельные поля, DELETE имитирует удаление.',
                            'en' => 'PUT replaces a resource, PATCH changes selected fields, and DELETE simulates deletion.',
                        ],
                    ],
                    [
                        'type' => 'section_link',
                        'section_id' => (int) $createPost->id,
                        'label' => ['ru' => 'Создание публикации', 'en' => 'Creating a post'],
                    ],
                ],
            ]);

            $this->section($documentation, $writeOperations, 3, 10, 'patch-post', [
                'ru' => 'Частично изменить публикацию',
                'en' => 'Patch a post',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'code',
                        'language' => 'bash',
                        'content' => [
                            'ru' => "curl -X PATCH -H \"Content-Type: application/json\" -d '{\"title\":\"foo\"}' https://jsonplaceholder.typicode.com/posts/1",
                            'en' => "curl -X PATCH -H \"Content-Type: application/json\" -d '{\"title\":\"foo\"}' https://jsonplaceholder.typicode.com/posts/1",
                        ],
                    ],
                ],
                'endpoint' => ['method' => 'PATCH', 'url' => '/posts/{postId}'],
                'request_body' => [
                    'content_type' => 'application/json; charset=UTF-8',
                    'schema' => [
                        'type' => 'object',
                        'properties' => ['title' => ['type' => 'string']],
                    ],
                ],
                'request_parameters' => [
                    $this->parameter('postId', 'path', true, 'integer', 1,
                        'ID изменяемой публикации.', 'ID of the post to update.'),
                ],
                'request_example' => ['title' => 'foo'],
                'responses' => [
                    $this->response('200', 'Изменена', 'Updated', 'Объект после имитации изменения.', 'Object after simulated update.', 'object', null,
                        ['userId' => 1, 'id' => 1, 'title' => 'foo', 'body' => 'Original body']),
                ],
            ]);

            $this->section($documentation, $writeOperations, 3, 20, 'delete-post', [
                'ru' => 'Удалить публикацию',
                'en' => 'Delete a post',
            ], [
                'endpoint' => ['method' => 'DELETE', 'url' => '/posts/{postId}'],
                'request_parameters' => [
                    $this->parameter('postId', 'path', true, 'integer', 1,
                        'ID удаляемой публикации.', 'ID of the post to delete.'),
                ],
                'responses' => [
                    $this->response('200', 'Удалена', 'Deleted', 'Пустой объект подтверждает имитацию удаления.', 'An empty object confirms simulated deletion.', 'object', ['type' => 'object'], []),
                ],
            ]);

            $comments = $this->section($documentation, null, 1, 30, 'comments', [
                'ru' => 'Комментарии',
                'en' => 'Comments',
            ], [
                'description_blocks' => [
                    [
                        'type' => 'text',
                        'content' => [
                            'ru' => 'Комментарии можно получить фильтром или вложенным маршрутом публикации.',
                            'en' => 'Comments can be retrieved by filter or through a nested post route.',
                        ],
                    ],
                    [
                        'type' => 'section_link',
                        'section_id' => (int) $posts->id,
                        'label' => ['ru' => 'Перейти к публикациям', 'en' => 'Go to posts'],
                    ],
                ],
            ]);

            $this->section($documentation, $comments, 2, 10, 'post-comments', [
                'ru' => 'Комментарии публикации',
                'en' => 'Post comments',
            ], [
                'endpoint' => ['method' => 'GET', 'url' => '/posts/{postId}/comments'],
                'request_parameters' => [
                    $this->parameter('postId', 'path', true, 'integer', 1,
                        'ID публикации.', 'Post ID.'),
                ],
                'responses' => [
                    $this->response('200', 'Комментарии', 'Comments', 'Массив комментариев.', 'An array of comments.', 'array', [
                        'type' => 'array',
                        'items' => ['type' => 'object', 'properties' => [
                            'postId' => ['type' => 'integer'],
                            'id' => ['type' => 'integer'],
                            'name' => ['type' => 'string'],
                            'email' => ['type' => 'string', 'format' => 'email'],
                            'body' => ['type' => 'string'],
                        ]],
                    ], [[
                        'postId' => 1,
                        'id' => 1,
                        'name' => 'Example comment',
                        'email' => 'user@example.com',
                        'body' => 'Example body',
                    ]]),
                ],
            ]);
        });
    }

    /** @param array<string, mixed> $extra */
    private function section(
        ApiDocumentation $documentation,
        ?ApiDocumentationSection $parent,
        int $level,
        int $position,
        string $slug,
        array $title,
        array $extra = []
    ): ApiDocumentationSection {
        $endpoint = $extra['endpoint'] ?? null;
        $requestBody = $extra['request_body'] ?? null;

        return ApiDocumentationSection::query()->create([
            'api_documentation_id' => (int) $documentation->id,
            'parent_id' => $parent?->id,
            'level' => $level,
            'position' => $position,
            'slug' => $slug,
            'title' => $title,
            'description_blocks' => $extra['description_blocks'] ?? [],
            'http_method' => $endpoint['method'] ?? null,
            'endpoint_url' => $endpoint['url'] ?? null,
            'request_body_content_type' => $requestBody['content_type'] ?? null,
            'request_body_schema' => $requestBody['schema'] ?? null,
            'request_headers' => $extra['request_headers'] ?? [],
            'request_parameters' => $extra['request_parameters'] ?? [],
            'request_example' => $extra['request_example'] ?? null,
            'responses' => $extra['responses'] ?? [],
        ]);
    }

    /** @return array<string, mixed> */
    private function parameter(
        string $name,
        string $location,
        bool $required,
        string $type,
        $example,
        string $descriptionRu,
        string $descriptionEn
    ): array {
        return [
            'name' => $name,
            'location' => $location,
            'required' => $required,
            'description' => ['ru' => $descriptionRu, 'en' => $descriptionEn],
            'type' => $type,
            'example' => $example,
        ];
    }

    /** @return array<string, mixed> */
    private function response(
        string $statusCode,
        string $nameRu,
        string $nameEn,
        string $descriptionRu,
        string $descriptionEn,
        string $type,
        ?array $schema,
        array $example
    ): array {
        return [
            'status_code' => $statusCode,
            'name' => ['ru' => $nameRu, 'en' => $nameEn],
            'description' => ['ru' => $descriptionRu, 'en' => $descriptionEn],
            'type' => $type,
            'content_type' => 'application/json; charset=UTF-8',
            'schema' => $schema,
            'example' => $example,
        ];
    }
}
