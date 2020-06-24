<?php

namespace Yiisoft\Strings\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Strings\Inflector;

final class InflectorTest extends TestCase
{
    private function getTestDataForPluralize(): array
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
        ];
    }

    private function getTestDataForSignularize(): array
    {
        return array_flip($this->getTestDataForPluralize());
    }

    public function testPluralize(): void
    {
        $inflector = new Inflector();

        foreach ($this->getTestDataForPluralize() as $testIn => $testOut) {
            $this->assertEquals($testOut, $inflector->pluralize($testIn), 'Should be ' . $testIn . ' -> ' . $testOut);
            $this->assertEquals(ucfirst($testOut), ucfirst($inflector->pluralize($testIn)));
        }
    }

    public function testSingularize(): void
    {
        $inflector = new Inflector();

        foreach ($this->getTestDataForSignularize() as $testIn => $testOut) {
            $this->assertEquals($testOut, $inflector->singularize($testIn), 'Should be ' . $testIn . ' -> ' . $testOut);
            $this->assertEquals(ucfirst($testOut), ucfirst($inflector->singularize($testIn)));
        }
    }

    public function testTitleize(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('Me my self and i', $inflector->titleize('MeMySelfAndI'));
        $this->assertEquals('Me My Self And I', $inflector->titleize('MeMySelfAndI', true));
        $this->assertEquals('Треба Більше Тестів!', $inflector->titleize('ТребаБільшеТестів!', true));
    }

    public function testCamelize(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('MeMySelfAndI', $inflector->camelize('me my_self-andI'));
        $this->assertEquals('QweQweEwq', $inflector->camelize('qwe qwe^ewq'));
        $this->assertEquals('ВідомоЩоТестиЗберігатьНашіНЕРВИ', $inflector->camelize('Відомо, що тести зберігать наші НЕРВИ! 🙃'));
    }

    public function testUnderscore(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('me_my_self_and_i', $inflector->underscore('MeMySelfAndI'));
        $this->assertEquals('кожний_тест_особливий', $inflector->underscore('КожнийТестОсобливий'));
    }

    public function testCamel2words(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('Camel Case', $inflector->camel2words('camelCase'));
        $this->assertEquals('Lower Case', $inflector->camel2words('lower_case'));
        $this->assertEquals('Tricky Stuff It Is Testing', $inflector->camel2words(' tricky_stuff.it-is testing... '));
        $this->assertEquals('І Це Дійсно Так!', $inflector->camel2words('ІЦеДійсноТак!'));
    }

    /**
     * @dataProvider camel2idProvider()
     */
    public function testCamel2id(string $expectedResult, array $arguments): void
    {

        $inflector = new Inflector();

        $result = call_user_func_array([$inflector, 'camel2id'], $arguments);

        $this->assertEquals($expectedResult, $result);
    }


    /**
     * @dataProvider id2camelProvider()
     */
    public function testId2camel(string $expectedResult, array $arguments): void
    {
        $inflector = new Inflector();

        $result = call_user_func_array([$inflector, 'id2camel'], $arguments);

        $this->assertEquals($expectedResult, $result);
    }

    public function testHumanize(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('Me my self and i', $inflector->humanize('me_my_self_and_i'));
        $this->assertEquals('Me My Self And I', $inflector->humanize('me_my_self_and_i', true));
        $this->assertEquals('Але й веселі ці ваші тести', $inflector->humanize('але_й_веселі_ці_ваші_тести'));
    }

    public function testVariablize(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('customerTable', $inflector->variablize('customer_table'));
        $this->assertEquals('ひらがなHepimiz', $inflector->variablize('ひらがな_hepimiz'));
    }

    public function testTableize(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('customer_tables', $inflector->tableize('customerTable'));
    }

    public function slugCommonsDataProvider(): array
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

    /**
     * @dataProvider slugCommonsDataProvider
     */
    public function testSlugCommons(string $input, string $expected, string $replacement = '-'): void
    {
        $inflector = new Inflector();
        if (extension_loaded('intl')) {
            $this->assertEquals($expected, $inflector->slug($input, $replacement));
        }
        $this->assertEquals($expected, $inflector->withoutIntl()->slug($input, $replacement));
    }

    public function testSlugIntl(): void
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
            $this->assertEquals($expected, $inflector->slug($source));
        }
    }

    public function testTransliterateStrict(): void
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
            $this->assertEquals($expected, $inflector->transliterate($source, Inflector::TRANSLITERATE_STRICT));
        }
    }

    public function testTransliterateMedium(): void
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
            '¿Español?' => ['¿Espanol?'],
            // Chinese
            '美国' => ['mei guo'],
        ];

        $inflector = new Inflector();

        foreach ($data as $source => $allowed) {
            $this->assertIsOneOf($inflector->transliterate($source, Inflector::TRANSLITERATE_MEDIUM), $allowed);
        }
    }

    public function testTransliterateLoose(): void
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
            '¿Español?' => ['Espanol?'],
            // Chinese
            '美国' => ['mei guo'],
        ];

        $inflector = new Inflector();

        foreach ($data as $source => $allowed) {
            $this->assertIsOneOf($inflector->transliterate($source, Inflector::TRANSLITERATE_LOOSE), $allowed);
        }
    }

    public function testSlugPhp(): void
    {
        $data = [
            'we have недвижимость' => 'we-have',
        ];

        $inflector = (new Inflector())->withoutIntl();

        foreach ($data as $source => $expected) {
            $this->assertEquals($expected, $inflector->slug($source));
        }
    }

    public function testClassify(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('CustomerTable', $inflector->classify('customer_tables'));
    }

    public function testOrdinalize(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('21st', $inflector->ordinalize('21'));
        $this->assertEquals('22nd', $inflector->ordinalize('22'));
        $this->assertEquals('23rd', $inflector->ordinalize('23'));
        $this->assertEquals('24th', $inflector->ordinalize('24'));
        $this->assertEquals('25th', $inflector->ordinalize('25'));
        $this->assertEquals('111th', $inflector->ordinalize('111'));
        $this->assertEquals('113th', $inflector->ordinalize('113'));
    }

    public function testSentence(): void
    {
        $inflector = new Inflector();

        $array = [];
        $this->assertEquals('', $inflector->sentence($array));

        $array = ['Spain'];
        $this->assertEquals('Spain', $inflector->sentence($array));

        $array = ['Spain', 'France'];
        $this->assertEquals('Spain and France', $inflector->sentence($array));

        $array = ['Spain', 'France', 'Italy'];
        $this->assertEquals('Spain, France and Italy', $inflector->sentence($array));

        $array = ['Spain', 'France', 'Italy', 'Germany'];
        $this->assertEquals('Spain, France, Italy and Germany', $inflector->sentence($array));

        $array = ['Spain', 'France'];
        $this->assertEquals('Spain or France', $inflector->sentence($array, ' or '));

        $array = ['Spain', 'France', 'Italy'];
        $this->assertEquals('Spain, France or Italy', $inflector->sentence($array, ' or '));

        $array = ['Spain', 'France'];
        $this->assertEquals('Spain and France', $inflector->sentence($array, ' and ', ' or ', ' - '));

        $array = ['Spain', 'France', 'Italy'];
        $this->assertEquals('Spain - France or Italy', $inflector->sentence($array, ' and ', ' or ', ' - '));
    }

    /** Asserts that value is one of expected values.
     *
     * @param mixed $actual
     * @param array $expected
     * @param string $message
     */
    private function assertIsOneOf($actual, array $expected, $message = ''): void
    {
        self::assertThat($actual, new IsOneOfAssert($expected), $message);
    }

    public function camel2idProvider():array
    {
        return [
            ['photo\\album-controller', ['Photo\\AlbumController', '-', false]],
            ['photo\\album-controller', ['Photo\\AlbumController', '-', true]],
            ['photo\\album\\controller', ['Photo\\Album\\Controller', '-', false]],
            ['photo\\album\\controller', ['Photo\\Album\\Controller', '-', true]],

            ['photo\\album_controller', ['Photo\\AlbumController', '_', false]],
            ['photo\\album_controller', ['Photo\\AlbumController', '_', true]],
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

        ];
    }

    public function id2camelProvider(): array
    {
        return [
            ['PostTag', ['post-tag']],
            ['PostTag', ['post_tag', '_']],
            ['ЄдинийСвіт', ['єдиний_світ', '_']],

            ['PostTag', ['post-tag']],
            ['PostTag', ['post_tag', '_']],
            ['НевжеІЦеПрацює', ['невже_і_це_працює', '_']],

            ['ShouldNotBecomeLowercased', ['ShouldNotBecomeLowercased', '_']],

            ['FooYBar', ['foo-y-bar']],
            ['FooYBar', ['foo_y_bar', '_']],
        ];
    }
}
