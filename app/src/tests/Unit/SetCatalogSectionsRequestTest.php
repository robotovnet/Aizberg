<?php

namespace Tests\Unit;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory as ValidatorFactory;
use Modules\OrderOneClick\Http\Requests\SetCatalogSectionsRequest;
use PHPUnit\Framework\TestCase;

class SetCatalogSectionsRequestTest extends TestCase
{
    public function testEmptyCatalogSectionsArrayIsValid(): void
    {
        $validator = $this->validator(['catalog_sections' => []]);

        $this->assertTrue($validator->passes());
    }

    public function testCatalogSectionsFieldMustBePresent(): void
    {
        $validator = $this->validator([]);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('catalog_sections', $validator->errors()->toArray());
    }

    private function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $factory = new ValidatorFactory($translator);

        return $factory->make($data, (new SetCatalogSectionsRequest())->rules());
    }
}
