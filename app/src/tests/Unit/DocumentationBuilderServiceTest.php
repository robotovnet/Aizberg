<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Collection;
use Modules\ApiDocumentation\Entities\ApiDocumentation;
use Modules\ApiDocumentation\Entities\ApiDocumentationSection;
use Modules\ApiDocumentation\Services\DocumentationBuilderService;
use Modules\ApiDocumentation\Services\DocumentationSearchService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class DocumentationBuilderServiceTest extends TestCase
{
    public function testJsonAttributesAreStoredWithoutEscapedUnicode(): void
    {
        $documentation = new ApiDocumentation([
            'name' => ['ru' => 'Документация продавца'],
            'short_description' => ['ru' => 'Описание интеграции'],
            'tags' => ['продавец', 'заказы'],
        ]);

        self::assertSame(
            '{"ru":"Документация продавца"}',
            $documentation->getAttributes()['name']
        );
        self::assertSame(
            '{"ru":"Описание интеграции"}',
            $documentation->getAttributes()['short_description']
        );
        self::assertSame(
            '["продавец","заказы"]',
            $documentation->getAttributes()['tags']
        );
        self::assertSame(['ru' => 'Документация продавца'], $documentation->name);
    }

    public function testItBuildsLocalizedThreeLevelDocument(): void
    {
        $documentation = new ApiDocumentation([
            'slug' => 'seller-api',
            'name' => ['ru' => 'API продавца', 'en' => 'Seller API'],
            'short_description' => ['ru' => 'Описание', 'en' => 'Description'],
            'tags' => ['seller'],
            'default_locale' => 'ru',
            'is_published' => true,
        ]);
        $documentation->id = 1;

        $root = $this->section(10, null, 1, 'orders', ['ru' => 'Заказы', 'en' => 'Orders']);
        $child = $this->section(11, 10, 2, 'create', ['ru' => 'Создание', 'en' => 'Create']);
        $child->http_method = 'POST';
        $child->endpoint_url = '/v1/orders';
        $child->responses = [[
            'status_code' => '200',
            'name' => ['ru' => 'Успех', 'en' => 'Success'],
            'description' => ['ru' => 'Создано', 'en' => 'Created'],
            'type' => 'object',
            'example' => ['id' => 42],
        ]];
        $leaf = $this->section(12, 11, 3, 'fields', ['ru' => 'Поля', 'en' => 'Fields']);

        $documentation->setRelation('sections', new Collection([$root, $child, $leaf]));

        $result = (new DocumentationBuilderService())->build($documentation, 'ru');

        self::assertSame('API продавца', $result['name']);
        self::assertSame('ru', $result['locale']);
        self::assertSame('Заказы', $result['sections'][0]['title']);
        self::assertSame('Создание', $result['sections'][0]['children'][0]['title']);
        self::assertSame('Поля', $result['sections'][0]['children'][0]['children'][0]['title']);
        self::assertSame('POST', $result['sections'][0]['children'][0]['endpoint']['method']);
        self::assertSame('Успех', $result['sections'][0]['children'][0]['responses'][0]['name']);
        self::assertSame(['en', 'ru'], $result['available_locales']);
    }

    public function testManagementDocumentKeepsTranslationMaps(): void
    {
        $documentation = new ApiDocumentation([
            'slug' => 'seller-api',
            'name' => ['ru' => 'API продавца', 'en' => 'Seller API'],
            'short_description' => ['ru' => 'Описание', 'en' => 'Description'],
            'default_locale' => 'ru',
            'is_published' => false,
        ]);
        $documentation->id = 1;
        $documentation->setRelation('sections', new Collection([
            $this->section(10, null, 1, 'orders', ['ru' => 'Заказы', 'en' => 'Orders']),
        ]));

        $result = (new DocumentationBuilderService())->build($documentation, 'ru', true);

        self::assertSame(['ru' => 'API продавца', 'en' => 'Seller API'], $result['name']);
        self::assertSame(['ru' => 'Заказы', 'en' => 'Orders'], $result['sections'][0]['title']);
        self::assertNull($result['locale']);
    }

    public function testSearchExcerptCanCleanSerializedJson(): void
    {
        $service = new DocumentationSearchService(new DocumentationBuilderService());
        $method = new ReflectionMethod($service, 'excerpt');
        $method->setAccessible(true);

        $excerpt = $method->invoke($service, '{"ru":"Заказ\\nсоздан"}', 'Заказ');

        self::assertIsString($excerpt);
        self::assertStringContainsString('Заказ', $excerpt);
    }

    /**
     * @param array<string, string> $title
     */
    private function section(int $id, ?int $parentId, int $level, string $slug, array $title): ApiDocumentationSection
    {
        $section = new ApiDocumentationSection([
            'api_documentation_id' => 1,
            'parent_id' => $parentId,
            'level' => $level,
            'position' => $id,
            'slug' => $slug,
            'title' => $title,
        ]);
        $section->id = $id;

        return $section;
    }
}
