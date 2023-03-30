<?php declare(strict_types=1);

namespace Test;

use Diezz\YamlToObjectMapper\ConfigMapper;
use Diezz\YamlToObjectMapper\ValidationException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * @covers \Diezz\YamlToObjectMapper\ConfigMapper
 */
class ModelMapperTest extends TestCase
{
    private ConfigMapper $mapper;

    /**
     * @throws ReflectionException
     * @throws ValidationException
     */
    public function testScalarsMapping(): void
    {
        //Given
        $configFile = __DIR__ . DIRECTORY_SEPARATOR . 'TestScalarsMappings.yml';

        //When
        $result = $this->mapper->mapFromFile(TestScalarsMappingsModel::class, $configFile);

        //Then
        self::assertInstanceOf(TestScalarsMappingsModel::class, $result);
        self::assertEquals('stringValue', $result->stringValue);
        self::assertIsArray($result->list);
        self::assertContains('first', $result->list);
        self::assertContains('second', $result->list);
        self::assertContains('third', $result->list);
        self::assertEquals(3, $result->integerValue);
        self::assertTrue($result->booleanValue);
    }

    public function testNestedModel(): void
    {
        //Given
        $configFile = __DIR__ . DIRECTORY_SEPARATOR . 'TestNestedModel.yml';

        //When
        $result = $this->mapper->mapFromFile(TestNestedModel::class, $configFile);

        //Then
        self::assertInstanceOf(TestNestedModel::class, $result);
        self::assertInstanceOf(Model::class, $result->model);
        self::assertIsArray($result->model->values);
        self::assertContains('first', $result->model->values);
        self::assertContains('second', $result->model->values);
        self::assertContains('third', $result->model->values);
        self::assertEquals('nestedModel', $result->model->name);
    }

    protected function setUp(): void
    {
        $this->mapper = ConfigMapper::make();
        parent::setUp();
    }
}
