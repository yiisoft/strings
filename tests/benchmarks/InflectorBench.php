<?php
declare(strict_types=1);

namespace Yiisoft\Strings\Tests\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Yiisoft\Strings\Inflector;

/**
 * @Iterations(10)
 * @Revs(1000)
 * @Warmup(2)
 */
final class InflectorBench
{
    private Inflector $inflector;

    public function __construct()
    {
        $this->inflector = new Inflector();
    }

    public function benchToPlural(): void
    {
        $this->inflector->toPlural('apple');
        $this->inflector->toPlural('book');
        $this->inflector->toPlural('child');
        $this->inflector->toPlural('person');
    }

    public function benchToSingular(): void
    {
        $this->inflector->toSingular('apples');
        $this->inflector->toSingular('books');
        $this->inflector->toSingular('children');
        $this->inflector->toSingular('people');
    }

    public function benchToSentence(): void
    {
        $this->inflector->toSentence('some_string_to_convert');
        $this->inflector->toSentence('someStringToconvert');
    }

    public function benchToWords(): void
    {
        $this->inflector->toWords('some_string_to_convert');
        $this->inflector->toWords('someStringToconvert');
    }

    public function benchPascalCaseToId(): void
    {
        $this->inflector->pascalCaseToId('SomeString');
        $this->inflector->pascalCaseToId('SomeOtherString');
    }

    public function benchToPascalCase(): void
    {
        $this->inflector->toPascalCase('some string');
        $this->inflector->toPascalCase('some_other_string');
    }

    public function benchToCamelCase(): void
    {
        $this->inflector->toCamelCase('some string');
        $this->inflector->toCamelCase('some_other_string');
    }

    public function benchToSnakeCase(): void
    {
        $this->inflector->toSnakeCase('SomeString');
        $this->inflector->toSnakeCase('some other string');
    }

    public function benchToHumanReadable(): void
    {
        $this->inflector->toHumanReadable('some_string');
        $this->inflector->toHumanReadable('SomeString');
    }

    public function benchClassToTable(): void
    {
        $this->inflector->classToTable('SomeClass');
        $this->inflector->classToTable('Another\\ClassName');
    }

    public function benchTableToClass(): void
    {
        $this->inflector->tableToClass('some_table');
        $this->inflector->tableToClass('another_table_name');
    }

    public function benchToSlug(): void
    {
        $this->inflector->toSlug('some string to slug');
        $this->inflector->toSlug('some other_string-to slug');
    }

    public function benchToTransliterated(): void
    {
        $this->inflector->toTransliterated('some string to transliterate');
        $this->inflector->toTransliterated('Українська мова');
        $this->inflector->toTransliterated('Русский язык');
        $this->inflector->toTransliterated('日本語って本当にかっこいいですね');
    }
}
