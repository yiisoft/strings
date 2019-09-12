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

    public function testCamel2id(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('post-tag', $inflector->camel2id('PostTag'));
        $this->assertEquals('post_tag', $inflector->camel2id('PostTag', '_'));
        $this->assertEquals('єдиний_код', $inflector->camel2id('ЄдинийКод', '_'));

        $this->assertEquals('post-tag', $inflector->camel2id('postTag'));
        $this->assertEquals('post_tag', $inflector->camel2id('postTag', '_'));
        $this->assertEquals('єдиний_код', $inflector->camel2id('єдинийКод', '_'));

        $this->assertEquals('foo-ybar', $inflector->camel2id('FooYBar', '-', false));
        $this->assertEquals('foo_ybar', $inflector->camel2id('fooYBar', '_', false));
        $this->assertEquals('невже_іце_працює', $inflector->camel2id('НевжеІЦеПрацює', '_', false));

        $this->assertEquals('foo-y-bar', $inflector->camel2id('FooYBar', '-', true));
        $this->assertEquals('foo_y_bar', $inflector->camel2id('fooYBar', '_', true));
        $this->assertEquals('foo_y_bar', $inflector->camel2id('fooYBar', '_', true));
        $this->assertEquals('невже_і_це_працює', $inflector->camel2id('НевжеІЦеПрацює', '_', true));
    }

    public function testId2camel(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('PostTag', $inflector->id2camel('post-tag'));
        $this->assertEquals('PostTag', $inflector->id2camel('post_tag', '_'));
        $this->assertEquals('ЄдинийСвіт', $inflector->id2camel('єдиний_світ', '_'));

        $this->assertEquals('PostTag', $inflector->id2camel('post-tag'));
        $this->assertEquals('PostTag', $inflector->id2camel('post_tag', '_'));
        $this->assertEquals('НевжеІЦеПрацює', $inflector->id2camel('невже_і_це_працює', '_'));

        $this->assertEquals('ShouldNotBecomeLowercased', $inflector->id2camel('ShouldNotBecomeLowercased', '_'));

        $this->assertEquals('FooYBar', $inflector->id2camel('foo-y-bar'));
        $this->assertEquals('FooYBar', $inflector->id2camel('foo_y_bar', '_'));
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

    public function testSlugCommons(): void
    {
        $inflector = (new Inflector())->withoutIntl();

        $data = [
            '' => '',
            'hello world 123' => 'hello-world-123',
            'remove.!?[]{}…symbols' => 'removesymbols',
            'minus-sign' => 'minus-sign',
            'mdash—sign' => 'mdash-sign',
            'ndash–sign' => 'ndash-sign',
            'áàâéèêíìîóòôúùûã' => 'aaaeeeiiiooouuua',
            'älä lyö ääliö ööliä läikkyy' => 'ala-lyo-aalio-oolia-laikkyy',
        ];

        foreach ($data as $source => $expected) {
            if (extension_loaded('intl')) {
                $this->assertEquals($expected, $inflector->slug($source));
            }
            $this->assertEquals($expected, $inflector->slug($source));
        }
    }

    public function testSlugReplacements(): void
    {
        $inflector = new Inflector();

        $this->assertEquals('dont_replace_replacement', $inflector->slug('dont replace_replacement', '_'));
        $this->assertEquals('remove_trailing_replacements', $inflector->slug('_remove trailing replacements_', '_'));
        $this->assertEquals('thisrepisreprepreplacement', $inflector->slug('this is REP-lacement', 'REP'));
        $this->assertEquals('0_100_kmh', $inflector->slug('0-100 Km/h', '_'));
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

}
