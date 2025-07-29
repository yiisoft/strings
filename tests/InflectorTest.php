<?php

declare(strict_types=1);

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\Inflector;

use function extension_loaded;

final class InflectorTest extends TestCase
{
    private function getTestDataForToPlural(): array
    {
        return [
            'access' => 'accesses',
            'address' => 'addresses',
            'move' => 'moves',
            'foot' => 'feet',
            'child' => 'children',
            'human' => 'humans',
            'man' => 'men',
            'staff' => 'staff',
            'tooth' => 'teeth',
            'person' => 'people',
            'mouse' => 'mice',
            'touch' => 'touches',
            'hash' => 'hashes',
            'shelf' => 'shelves',
            'potato' => 'potatoes',
            'bus' => 'buses',
            'test' => 'tests',
            'car' => 'cars',
            'netherlands' => 'netherlands',
            'currency' => 'currencies',
            'criterion' => 'criteria',
            'analysis' => 'analyses',
            'datum' => 'data',
            'schema' => 'schemas',
            'nation' => 'nations',
        ];
    }

    private function getTestDataForToSingular(): array
    {
        return array_flip($this->getTestDataForToPlural());
    }

    public function testToPlural(): void
    {
        $inflector = new Inflector();

        foreach ($this->getTestDataForToPlural() as $testIn => $testOut) {
            $this->assertEquals($testOut, $inflector->toPlural($testIn), 'Should be ' . $testIn . ' -> ' . $testOut);
            $this->assertEquals(ucfirst($testOut), ucfirst($inflector->toPlural($testIn)));
        }
    }

    public function testToPluralWithEmptyRules(): void
    {
        $inflector = (new Inflector())->withPluralizeRules([]);
        $this->assertEquals('access', $inflector->toPlural('access'));
    }

    public function testToPluralWithCustomRules(): void
    {
        $rules = ['/(t)est/i' => 'tests-result'];
        $inflector = (new Inflector())->withPluralizeRules($rules);
        $this->assertEquals('tests-result', $inflector->toPlural('test'));
        $this->assertEquals($rules, $inflector->getPluralizeRules());
    }

    public function testToPluralWithSpecialRules(): void
    {
        $rules = ['test' => 'tests-result'];
        $inflector = (new Inflector())->withSpecialRules($rules);
        $this->assertEquals('tests-result', $inflector->toPlural('test'));
        $this->assertEquals($rules, $inflector->getSpecialRules());
    }

    public function testToSingular(): void
    {
        $inflector = new Inflector();

        foreach ($this->getTestDataForToSingular() as $testIn => $testOut) {
            $this->assertEquals($testOut, $inflector->toSingular($testIn), 'Should be ' . $testIn . ' -> ' . $testOut);
            $this->assertEquals(ucfirst($testOut), ucfirst($inflector->toSingular($testIn)));
        }
    }

    public function testToSingularWithCustomRules(): void
    {
        $rules = ['/(t)ests$/i' => 'test-result'];
        $inflector = (new Inflector())->withSingularizeRules($rules);
        $this->assertEquals('test-result', $inflector->toSingular('tests'));
        $this->assertEquals($rules, $inflector->getSingularizeRules());
    }

    public function testToSingularWithSpecialRules(): void
    {
        $rules = ['tests-result' => 'test'];
        $inflector = (new Inflector())->withSpecialRules($rules);
        $this->assertEquals('tests-result', $inflector->toSingular('test'));
        $this->assertEquals($rules, $inflector->getSpecialRules());
    }

    public function testToSentence(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('Me my self and i', $inflector->toSentence('MeMySelfAndI'));
        $this->assertEquals('Me My Self And I', $inflector->toSentence('MeMySelfAndI', true));
        $this->assertEquals('Треба Більше Тестів!', $inflector->toSentence('ТребаБільшеТестів!', true));
    }

    public function testToPascalCase(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('MeMySelfAndI', $inflector->toPascalCase('me my_self-andI'));
        $this->assertEquals('QweQweEwq', $inflector->toPascalCase('qwe qwe^ewq'));
        $this->assertEquals(
            'ВідомоЩоТестиЗберігатьНашіНЕРВИ',
            $inflector->toPascalCase('Відомо, що тести зберігать наші НЕРВИ! 🙃')
        );
    }

    public function testToWords(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('camel case', $inflector->toWords('camelCase'));
        $this->assertEquals('lower case', $inflector->toWords('lower_case'));
        $this->assertEquals('tricky stuff it is testing', $inflector->toWords(' tricky_stuff.it-is testing... '));
        $this->assertEquals('і це дійсно так!', $inflector->toWords('ІЦеДійсноТак!'));
    }

    #[DataProvider('pascalCaseToIdProvider')]
    public function testPascalCaseToId(string $expectedResult, array $arguments): void
    {
        $inflector = new Inflector();

        $this->assertEquals($expectedResult, $inflector->pascalCaseToId(...$arguments));
    }

    public function testToHumanReadable(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('Me my self and i', $inflector->toHumanReadable('me_my_self_and_i'));
        $this->assertEquals('Me My Self And I', $inflector->toHumanReadable('me_my_self_and_i', true));
        $this->assertEquals('Але й веселі ці ваші тести', $inflector->toHumanReadable('але_й_веселі_ці_ваші_тести'));
    }

    public function testToCamelCase(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('customerTable', $inflector->toCamelCase('customer_table'));
        $this->assertEquals('ひらがなHepimiz', $inflector->toCamelCase('ひらがな_hepimiz'));
    }

    public static function dataToSnakeCase(): array
    {
        return [
            [['input' => 'userName'], 'user_name'],
            [['input' => 'travelSGuide'], 'travel_s_guide'],
            [['input' => 'ひらがなHepimiz'], 'ひらがな_hepimiz'],
            [['input' => 'Let\'s say "Hello, World!" yii 3 😂'], 'let_s_say_hello_world_yii_3'],
            [['input' => 'HTML'], 'h_t_m_l'],
            [['input' => 'createMyDTO'], 'create_my_d_t_o'],
            [['input' => 'HTML', 'strict' => false], 'html'],
            [['input' => 'createMyDTO', 'strict' => false], 'create_my_dto'],
        ];
    }

    #[DataProvider('dataToSnakeCase')]
    public function testToSnakeCase(array $arguments, string $expectedOutput): void
    {
        $inflector = new Inflector();

        $this->assertEquals($expectedOutput, $inflector->toSnakeCase(...$arguments));
    }

    public function testToTable(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('customer_tables', $inflector->classToTable('customerTable'));
    }

    public static function toSlugCommonsDataProvider(): array
    {
        return [
            ['', ''],
            ['hello world 123', 'hello-world-123'],
            ['remove.!?[]{}…symbols', 'remove-symbols'],
            ['minus-sign', 'minus-sign'],
            ['mdash—sign', 'mdash-sign'],
            ['ndash–sign', 'ndash-sign'],
            ['áàâéèêíìîóòôúùûã', 'aaaeeeiiiooouuua'],
            ['älä lyö ääliö ööliä läikkyy', 'ala-lyo-aalio-oolia-laikkyy'],
            'start' => ['---test', 'test'],
            'end' => ['test---', 'test'],
            'startAndEnd' => ['---test---', 'test'],
            'repeated' => ['hello----world', 'hello-world'],
            ['dont replace_replacement', 'dont_replace_replacement', '_'],
            ['_remove trailing replacements_', 'remove_trailing_replacements', '_'],
            ['this is REP-lacement', 'thisrepisreprepreplacement', 'REP'],
            ['0-100 Km/h', '0_100_km_h', '_'],
            ['test empty', 'testempty', ''],
        ];
    }

    #[DataProvider('toSlugCommonsDataProvider')]
    public function testToSlugCommons(string $input, string $expected, string $replacement = '-'): void
    {
        $inflector = new Inflector();
        if (extension_loaded('intl')) {
            $this->assertEquals($expected, $inflector->toSlug($input, $replacement));
        }
        $this->assertEquals($expected, $inflector
            ->withoutIntl()
            ->toSlug($input, $replacement));
    }

    public function testToSlugWithIntl(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is required.');
        }

        // Some test strings are from https://github.com/bergie/midgardmvc_helper_urlize. Thank you, Henri Bergius!
        $data = [
            // Korean
            '해동검도' => 'haedong-geomdo',
            // Hiragana
            'ひらがな' => 'hiragana',
            // Georgian
            'საქართველო' => 'sakartvelo',
            // Arabic
            'العربي' => 'alrby',
            'عرب' => 'rb',
            // Hebrew
            'עִבְרִית' => 'iberiyt',
            // Turkish
            'Sanırım hepimiz aynı şeyi düşünüyoruz.' => 'sanirim-hepimiz-ayni-seyi-dusunuyoruz',
            // Russian
            'недвижимость' => 'nedvizimost',
            'Контакты' => 'kontakty',
            // Chinese
            '美国' => 'mei-guo',
            // Estonian
            'Jääär' => 'jaaar',
        ];

        $inflector = new Inflector();

        foreach ($data as $source => $expected) {
            $this->assertEquals($expected, $inflector->toSlug($source));
        }
    }

    public function testToTransliteratedStrict(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is required.');
        }

        // Some test strings are from https://github.com/bergie/midgardmvc_helper_urlize. Thank you, Henri Bergius!
        $data = [
            // Korean
            '해동검도' => 'haedong-geomdo',

            // Hiragana
            'ひらがな' => 'hiragana',

            // Georgian
            'საქართველო' => 'sakartvelo',

            // Arabic
            'العربي' => 'ạlʿrby',
            'عرب' => 'ʿrb',

            // Hebrew
            'עִבְרִית' => 'ʻibĕriyţ',

            // Turkish
            'Sanırım hepimiz aynı şeyi düşünüyoruz.' => 'Sanırım hepimiz aynı şeyi düşünüyoruz.',

            // Russian
            'недвижимость' => 'nedvižimostʹ',
            'Контакты' => 'Kontakty',

            // Ukrainian
            'Українська: ґанок, європа' => 'Ukraí̈nsʹka: g̀anok, êvropa',

            // Serbian
            'Српска: ђ, њ, џ!' => 'Srpska: đ, n̂, d̂!',

            // Spanish
            '¿Español?' => '¿Español?',

            // Chinese
            '美国' => 'měi guó',
        ];

        $inflector = new Inflector();

        foreach ($data as $source => $expected) {
            $this->assertEquals($expected, $inflector->toTransliterated($source, Inflector::TRANSLITERATE_STRICT));
        }
    }

    public function testToTransliteratedMedium(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is required.');
        }

        // Some test strings are from https://github.com/bergie/midgardmvc_helper_urlize. Thank you, Henri Bergius!
        $data = [
            // Korean
            '해동검도' => ['haedong-geomdo'],

            // Hiragana
            'ひらがな' => ['hiragana'],

            // Georgian
            'საქართველო' => ['sakartvelo'],

            // Arabic
            'العربي' => ['alʿrby'],
            'عرب' => ['ʿrb'],

            // Hebrew
            'עִבְרִית' => ['\'iberiyt', 'ʻiberiyt'],

            // Turkish
            'Sanırım hepimiz aynı şeyi düşünüyoruz.' => ['Sanirim hepimiz ayni seyi dusunuyoruz.'],

            // Russian
            'недвижимость' => ['nedvizimost\'', 'nedvizimostʹ'],
            'Контакты' => ['Kontakty'],

            // Ukrainian
            'Українська: ґанок, європа' => ['Ukrainsʹka: ganok, evropa', 'Ukrains\'ka: ganok, evropa'],

            // Serbian
            'Српска: ђ, њ, џ!' => ['Srpska: d, n, d!'],

            // Spanish
            '¿Español?' => ['¿Espanol?', '?Espanol?'],

            // Chinese
            '美国' => ['mei guo'],
        ];

        $inflector = new Inflector();

        foreach ($data as $source => $allowed) {
            $this->assertIsOneOf($inflector->toTransliterated($source, Inflector::TRANSLITERATE_MEDIUM), $allowed);
        }
    }

    public function testToTransliteratedLoose(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is required.');
        }

        // Some test strings are from https://github.com/bergie/midgardmvc_helper_urlize. Thank you, Henri Bergius!
        $data = [
            // Korean
            '해동검도' => ['haedong-geomdo'],

            // Hiragana
            'ひらがな' => ['hiragana'],

            // Georgian
            'საქართველო' => ['sakartvelo'],

            // Arabic
            'العربي' => ['alrby'],
            'عرب' => ['rb'],

            // Hebrew
            'עִבְרִית' => ['\'iberiyt', 'iberiyt'],

            // Turkish
            'Sanırım hepimiz aynı şeyi düşünüyoruz.' => ['Sanirim hepimiz ayni seyi dusunuyoruz.'],

            // Russian
            'недвижимость' => ['nedvizimost\'', 'nedvizimost'],
            'Контакты' => ['Kontakty'],

            // Ukrainian
            'Українська: ґанок, європа' => ['Ukrainska: ganok, evropa', 'Ukrains\'ka: ganok, evropa'],

            // Serbian
            'Српска: ђ, њ, џ!' => ['Srpska: d, n, d!'],

            // Spanish
            '¿Español?' => ['Espanol?', '?Espanol?'],

            // Chinese
            '美国' => ['mei guo'],
        ];

        $inflector = new Inflector();

        foreach ($data as $source => $allowed) {
            $this->assertIsOneOf($inflector->toTransliterated($source, Inflector::TRANSLITERATE_LOOSE), $allowed);
        }
    }

    public function testToTransliteratedWithTransliterator(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is required.');
        }

        $inflector = (new Inflector())->withTransliterator(Inflector::TRANSLITERATE_STRICT);
        $this->assertEquals('žestʹ', $inflector->toTransliterated('жесть'));
    }

    public function testToTransliteratedWithTransliterationMap(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('intl extension is required.');
        }

        $inflector = (new Inflector())
            ->withoutIntl()
            ->withTransliterationMap(
                [
                    'O' => 'E',
                    'N' => 'N',
                    'E' => 'O',
                ]
            );
        $this->assertEquals('ENO', $inflector->toTransliterated('ONE'));
    }

    public function testToSlugPhp(): void
    {
        $data = [
            'we have недвижимость' => 'we-have',
        ];

        $inflector = (new Inflector())->withoutIntl();

        foreach ($data as $source => $expected) {
            $this->assertEquals($expected, $inflector->toSlug($source));
        }
    }

    public function testTableToClass(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('CustomerTable', $inflector->tableToClass('customer_tables'));
    }

    public function testImmutability(): void
    {
        $original = new Inflector();
        $this->assertNotSame($original, $original->withoutIntl());
        $this->assertNotSame($original, $original->withPluralizeRules([]));
        $this->assertNotSame($original, $original->withSingularizeRules([]));
        $this->assertNotSame($original, $original->withSpecialRules([]));
        $this->assertNotSame($original, $original->withTransliterationMap([]));
        $this->assertNotSame($original, $original->withTransliterator(Inflector::TRANSLITERATE_MEDIUM));
    }

    /**
     * Asserts that value is one of expected values.
     *
     * @param string $message
     */
    private function assertIsOneOf(mixed $actual, array $expected, $message = ''): void
    {
        self::assertThat($actual, new IsOneOfAssert($expected), $message);
    }

    public static function pascalCaseToIdProvider(): array
    {
        return [
            ['photo\\album-controller', ['Photo\\AlbumController', '-', false]],
            ['photo\\album-controller', ['Photo\\AlbumController', '-', true]],
            ['photo\\album\\controller', ['Photo\\Album\\Controller', '-', false]],
            ['photo\\album\\controller', ['Photo\\Album\\Controller', '-', true]],

            ['photo\\album_controller', ['Photo\\AlbumController', '_', false]],
            ['photo\\album_controller', ['Photo\\AlbumController', '_', true]],
            ['photo\\album\\controller', ['Photo\\AlbumController', '\\', false]],
            ['photo\\album\\controller', ['Photo\\AlbumController', '\\', true]],
            ['photo\\album/controller', ['Photo\\AlbumController', '/', false]],
            ['photo\\album/controller', ['Photo\\AlbumController', '/', true]],
            ['photo\\album\\controller', ['Photo\\Album\\Controller', '_', false]],
            ['photo\\album\\controller', ['Photo\\Album\\Controller', '_', true]],

            ['photo/album/controller', ['Photo/Album/Controller', '-', false]],
            ['photo/album/controller', ['Photo/Album/Controller', '-', true]],

            ['post-tag', ['PostTag']],
            ['post_tag', ['PostTag', '_']],
            ['єдиний_код', ['ЄдинийКод', '_']],

            ['post-tag', ['postTag']],
            ['post_tag', ['postTag', '_']],
            ['єдиний_код', ['єдинийКод', '_']],

            ['foo-ybar', ['FooYBar', '-', false]],
            ['foo_ybar', ['fooYBar', '_', false]],
            ['невже_іце_працює', ['НевжеІЦеПрацює', '_', false]],

            ['foo-y-bar', ['FooYBar', '-', true]],
            ['foo_y_bar', ['fooYBar', '_', true]],
            ['foo_y_bar', ['fooYBar', '_', true]],
            ['невже_і_це_працює', ['НевжеІЦеПрацює', '_', true]],

            ['t-e-s-t', ['TEST', '-', true]],
            ['test', ['TEST', '-']],
        ];
    }
}
