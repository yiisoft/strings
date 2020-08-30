<?php

declare(strict_types=1);

namespace Yiisoft\Strings;

/**
 * Inflector provides methods such as {@see pluralize()} or {@see slug()} that derive a new string based on
 * the string given.
 */
final class Inflector
{
    /**
     * Shortcut for `Any-Latin; NFKD` transliteration rule.
     *
     * The rule is strict, letters will be transliterated with
     * the closest sound-representation chars. The result may contain any UTF-8 chars. For example:
     * `获取到 どちら Українська: ґ,є, Српска: ђ, њ, џ! ¿Español?` will be transliterated to
     * `huò qǔ dào dochira Ukraí̈nsʹka: g̀,ê, Srpska: đ, n̂, d̂! ¿Español?`.
     *
     * For detailed information see [unicode normalization forms](http://unicode.org/reports/tr15/#Normalization_Forms_Table)
     * @see http://unicode.org/reports/tr15/#Normalization_Forms_Table
     * @see transliterate()
     */
    public const TRANSLITERATE_STRICT = 'Any-Latin; NFKD';

    /**
     * Shortcut for `Any-Latin; Latin-ASCII` transliteration rule.
     *
     * The rule is medium, letters will be
     * transliterated to characters of Latin-1 (ISO 8859-1) ASCII table. For example:
     * `获取到 どちら Українська: ґ,є, Српска: ђ, њ, џ! ¿Español?` will be transliterated to
     * `huo qu dao dochira Ukrainsʹka: g,e, Srpska: d, n, d! ¿Espanol?`.
     *
     * @see http://unicode.org/reports/tr15/#Normalization_Forms_Table
     * @see transliterate()
     */
    public const TRANSLITERATE_MEDIUM = 'Any-Latin; Latin-ASCII';

    /**
     * Shortcut for `Any-Latin; Latin-ASCII; [\u0080-\uffff] remove` transliteration rule.
     *
     * The rule is loose,
     * letters will be transliterated with the characters of Basic Latin Unicode Block.
     * For example:
     * `获取到 どちら Українська: ґ,є, Српска: ђ, њ, џ! ¿Español?` will be transliterated to
     * `huo qu dao dochira Ukrainska: g,e, Srpska: d, n, d! Espanol?`.
     *
     * @see http://unicode.org/reports/tr15/#Normalization_Forms_Table
     * @see transliterate()
     */
    public const TRANSLITERATE_LOOSE = 'Any-Latin; Latin-ASCII; [\u0080-\uffff] remove';

    /**
     * @var string[] The rules for converting a word into its plural form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     */
    private array $pluralizeRules = [
        '/([nrlm]ese|deer|fish|sheep|measles|ois|pox|media)$/i' => '\1',
        '/^(sea[- ]bass)$/i' => '\1',
        '/(m)ove$/i' => '\1oves',
        '/(f)oot$/i' => '\1eet',
        '/(h)uman$/i' => '\1umans',
        '/(s)tatus$/i' => '\1tatuses',
        '/(s)taff$/i' => '\1taff',
        '/(t)ooth$/i' => '\1eeth',
        '/(quiz)$/i' => '\1zes',
        '/^(ox)$/i' => '\1\2en',
        '/([m|l])ouse$/i' => '\1ice',
        '/(matr|vert|ind)(ix|ex)$/i' => '\1ices',
        '/(x|ch|ss|sh)$/i' => '\1es',
        '/([^aeiouy]|qu)y$/i' => '\1ies',
        '/(hive)$/i' => '\1s',
        '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '\1a',
        '/(p)erson$/i' => '\1eople',
        '/(m)an$/i' => '\1en',
        '/(c)hild$/i' => '\1hildren',
        '/(buffal|tomat|potat|ech|her|vet)o$/i' => '\1oes',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
        '/us$/i' => 'uses',
        '/(alias)$/i' => '\1es',
        '/(ax|cris|test)is$/i' => '\1es',
        '/(currenc)y$/' => '\1ies',
        '/on$/i' => 'a',
        '/s$/' => 's',
        '/^$/' => '',
        '/$/' => 's',
    ];

    /**
     * @var string[] The rules for converting a word into its singular form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     */
    private array $singularizeRules = [
        '/([nrlm]ese|deer|fish|sheep|measles|ois|pox|media|ss)$/i' => '\1',
        '/^(sea[- ]bass)$/i' => '\1',
        '/(s)tatuses$/i' => '\1tatus',
        '/(f)eet$/i' => '\1oot',
        '/(t)eeth$/i' => '\1ooth',
        '/^(.*)(menu)s$/i' => '\1\2',
        '/(quiz)zes$/i' => '\\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias)(es)*$/i' => '\1',
        '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
        '/([ftw]ax)es/i' => '\1',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe|slave)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/ouses$/i' => 'ouse',
        '/([^a])uses$/i' => '\1us',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1\2ovie',
        '/(s)eries$/i' => '\1\2eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/(drive)s$/i' => '\1',
        '/([^fo])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/criteria$/i' => 'criterion',
        '/([ti])a$/i' => '\1um',
        '/(p)eople$/i' => '\1\2erson',
        '/(m)en$/i' => '\1an',
        '/(c)hildren$/i' => '\1\2hild',
        '/(n)ews$/i' => '\1\2ews',
        '/(n)etherlands$/i' => '\1\2etherlands',
        '/eaus$/i' => 'eau',
        '/(currenc)ies$/i' => '\1y',
        '/^(.*us)$/i' => '\\1',
        '/s$/i' => '',
    ];

    /**
     * @var string[] The special rules for converting a word between its plural form and singular form.
     * The keys are the special words in singular form, and the values are the corresponding plural form.
     */
    private array $specialRules = [
        'atlas' => 'atlases',
        'beef' => 'beefs',
        'brother' => 'brothers',
        'cafe' => 'cafes',
        'child' => 'children',
        'cookie' => 'cookies',
        'corpus' => 'corpuses',
        'cow' => 'cows',
        'curve' => 'curves',
        'foe' => 'foes',
        'ganglion' => 'ganglions',
        'genie' => 'genies',
        'genus' => 'genera',
        'graffito' => 'graffiti',
        'hoof' => 'hoofs',
        'loaf' => 'loaves',
        'man' => 'men',
        'money' => 'monies',
        'mongoose' => 'mongooses',
        'move' => 'moves',
        'mythos' => 'mythoi',
        'niche' => 'niches',
        'numen' => 'numina',
        'occiput' => 'occiputs',
        'octopus' => 'octopuses',
        'opus' => 'opuses',
        'ox' => 'oxen',
        'pasta' => 'pasta',
        'penis' => 'penises',
        'sex' => 'sexes',
        'soliloquy' => 'soliloquies',
        'testis' => 'testes',
        'trilby' => 'trilbys',
        'turf' => 'turfs',
        'wave' => 'waves',
        'Amoyese' => 'Amoyese',
        'bison' => 'bison',
        'Borghese' => 'Borghese',
        'bream' => 'bream',
        'breeches' => 'breeches',
        'britches' => 'britches',
        'buffalo' => 'buffalo',
        'cantus' => 'cantus',
        'carp' => 'carp',
        'chassis' => 'chassis',
        'clippers' => 'clippers',
        'cod' => 'cod',
        'coitus' => 'coitus',
        'Congoese' => 'Congoese',
        'contretemps' => 'contretemps',
        'corps' => 'corps',
        'debris' => 'debris',
        'diabetes' => 'diabetes',
        'djinn' => 'djinn',
        'eland' => 'eland',
        'elk' => 'elk',
        'equipment' => 'equipment',
        'Faroese' => 'Faroese',
        'flounder' => 'flounder',
        'Foochowese' => 'Foochowese',
        'gallows' => 'gallows',
        'Genevese' => 'Genevese',
        'Genoese' => 'Genoese',
        'Gilbertese' => 'Gilbertese',
        'graffiti' => 'graffiti',
        'headquarters' => 'headquarters',
        'herpes' => 'herpes',
        'hijinks' => 'hijinks',
        'Hottentotese' => 'Hottentotese',
        'information' => 'information',
        'innings' => 'innings',
        'jackanapes' => 'jackanapes',
        'Kiplingese' => 'Kiplingese',
        'Kongoese' => 'Kongoese',
        'Lucchese' => 'Lucchese',
        'mackerel' => 'mackerel',
        'Maltese' => 'Maltese',
        'mews' => 'mews',
        'moose' => 'moose',
        'mumps' => 'mumps',
        'Nankingese' => 'Nankingese',
        'news' => 'news',
        'nexus' => 'nexus',
        'Niasese' => 'Niasese',
        'Pekingese' => 'Pekingese',
        'Piedmontese' => 'Piedmontese',
        'pincers' => 'pincers',
        'Pistoiese' => 'Pistoiese',
        'pliers' => 'pliers',
        'Portuguese' => 'Portuguese',
        'proceedings' => 'proceedings',
        'rabies' => 'rabies',
        'rice' => 'rice',
        'rhinoceros' => 'rhinoceros',
        'salmon' => 'salmon',
        'Sarawakese' => 'Sarawakese',
        'scissors' => 'scissors',
        'series' => 'series',
        'Shavese' => 'Shavese',
        'shears' => 'shears',
        'siemens' => 'siemens',
        'species' => 'species',
        'swine' => 'swine',
        'testes' => 'testes',
        'trousers' => 'trousers',
        'trout' => 'trout',
        'tuna' => 'tuna',
        'Vermontese' => 'Vermontese',
        'Wenchowese' => 'Wenchowese',
        'whiting' => 'whiting',
        'wildebeest' => 'wildebeest',
        'Yengeese' => 'Yengeese',
    ];

    /**
     * @var string[] Fallback map for transliteration used by {@see transliterate()} when intl isn't available or
     * turned off with {@see withoutIntl()}.
     */
    private array $transliterationMap = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
        'ÿ' => 'y',
    ];

    /**
     * @var string|\Transliterator Either a {@see \Transliterator}, or a string from which a {@see \Transliterator}
     * can be built for transliteration. Used by {@see transliterate()} when intl is available.
     * Defaults to {@see TRANSLITERATE_LOOSE}.
     * @see https://secure.php.net/manual/en/transliterator.transliterate.php
     */
    private $transliterator = self::TRANSLITERATE_LOOSE;

    private bool $withoutIntl = false;

    /**
     * @param string[] $rules The rules for converting a word into its plural form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     * @return self
     */
    public function withPluralizeRules(array $rules): self
    {
        $new = clone $this;
        $new->pluralizeRules = $rules;
        return $new;
    }

    /**
     * @return string[] The rules for converting a word into its plural form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     */
    public function getPluralizeRules(): array
    {
        return $this->pluralizeRules;
    }

    /**
     * @param string[] $rules The rules for converting a word into its singular form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     * @return self
     */
    public function withSingularizeRules(array $rules): self
    {
        $new = clone $this;
        $new->singularizeRules = $rules;
        return $new;
    }

    /**
     * @return string[] The rules for converting a word into its singular form.
     * The keys are the regular expressions and the values are the corresponding replacements.
     */
    public function getSingularizeRules(): array
    {
        return $this->singularizeRules;
    }

    /**
     * @param string[] $rules The special rules for converting a word between its plural form and singular form.
     * The keys are the special words in singular form, and the values are the corresponding plural form.
     * @return self
     */
    public function withSpecialRules(array $rules): self
    {
        $new = clone $this;
        $new->specialRules = $rules;
        return $new;
    }

    /**
     * @return string[] The special rules for converting a word between its plural form and singular form.
     * The keys are the special words in singular form, and the values are the corresponding plural form.
     */
    public function getSpecialRules(): array
    {
        return $this->specialRules;
    }

    /**
     * @param string|\Transliterator $transliterator Either a {@see \Transliterator}, or a string from which
     * a {@see \Transliterator} can be built for transliteration. Used by {@see transliterate()} when intl is available.
     * Defaults to {@see TRANSLITERATE_LOOSE}.
     * @see https://secure.php.net/manual/en/transliterator.transliterate.php
     * @return self
     */
    public function withTransliterator($transliterator): self
    {
        $new = clone $this;
        $new->transliterator = $transliterator;
        return $new;
    }

    /**
     * @param string[] $transliterationMap Fallback map for transliteration used by {@see transliterate()} when intl
     * isn't available or turned off with {@see withoutIntl()}.
     * @return $this
     */
    public function withTransliterationMap(array $transliterationMap): self
    {
        $new = clone $this;
        $new->transliterationMap = $transliterationMap;
        return $new;
    }

    /**
     * Disables usage of intl for {@see transliterate()}.
     * @return self
     */
    public function withoutIntl(): self
    {
        $new = clone $this;
        $new->withoutIntl = true;
        return $new;
    }

    /**
     * Converts a word to its plural form.
     * Note that this is for English only!
     * For example, "apple" will become "apples", and "child" will become "children".
     * @param string $input The word to be pluralized.
     * @return string The pluralized word.
     */
    public function pluralize(string $input): string
    {
        if (isset($this->specialRules[$input])) {
            return $this->specialRules[$input];
        }
        foreach ($this->pluralizeRules as $rule => $replacement) {
            if (preg_match($rule, $input)) {
                return preg_replace($rule, $replacement, $input);
            }
        }

        return $input;
    }

    /**
     * Returns the singular of the $word.
     * @param string $input The english word to singularize.
     * @return string Singular noun.
     */
    public function singularize(string $input): string
    {
        $result = array_search($input, $this->specialRules, true);
        if ($result !== false) {
            return $result;
        }
        foreach ($this->singularizeRules as $rule => $replacement) {
            if (preg_match($rule, $input)) {
                return preg_replace($rule, $replacement, $input);
            }
        }

        return $input;
    }

    /**
     * Converts an underscored or PascalCase word into a English
     * sentence.
     * @param string $input The string to titleize.
     * @param bool $uppercaseAll Whether to set all words to uppercase.
     * @return string
     */
    public function toSentence(string $input, bool $uppercaseAll = false): string
    {
        $input = $this->humanize($this->pascalToUnderscore($input), $uppercaseAll);

        return $uppercaseAll ? StringHelper::uppercaseFirstCharacterInEachWord($input) : StringHelper::uppercaseFirstCharacter($input);
    }

    /**
     * Converts a PascalCase name into space-separated words.
     * For example, 'PostTag' will be converted to 'Post Tag'.
     * @param string $input The string to be converted.
     * @return string The resulting words.
     */
    public function pascalToSentence(string $input): string
    {
        return mb_strtolower(trim(str_replace([
            '-',
            '_',
            '.',
        ], ' ', preg_replace('/(?<!\p{Lu})(\p{Lu})|(\p{Lu})(?=\p{Ll})/u', ' \0', $input))));
    }

    /**
     * Converts a PascalCase name into an ID in lowercase.
     * Words in the ID may be concatenated using the specified character (defaults to '-').
     * For example, 'PostTag' will be converted to 'post-tag'.
     * @param string $input The string to be converted.
     * @param string $separator The character used to concatenate the words in the ID.
     * @param bool $strict Whether to insert a separator between two consecutive uppercase chars, defaults to false.
     * @return string The resulting ID.
     */
    public function pascalToId(string $input, string $separator = '-', bool $strict = false): string
    {
        $regex = $strict
            ? '/(?<=\p{L})(\p{Lu})/u'
            : '/(?<=\p{L})(?<!\p{Lu})(\p{Lu})/u';
        $result = preg_replace($regex, addslashes($separator) . '\1', $input);

        if ($separator !== '_') {
            $result = str_replace('_', $separator, $result);
        }

        return mb_strtolower(trim($result, $separator));
    }

    /**
     * Converts any "PascalCased" into an "underscored_word".
     * @param string $input The word(s) to underscore.
     * @return string
     */
    public function pascalToUnderscore(string $input): string
    {
        return mb_strtolower(preg_replace('/(?<=\\pL)(\\p{Lu})/u', '_\\1', $input));
    }

    /**
     * Returns given word as PascalCased.
     *
     * Converts a word like "send_email" to "SendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "WhoSOnline".
     * @param string $input The word to PascalCase.
     * @return string PascalCased string.
     * @see variablize()
     */
    public function toPascal(string $input): string
    {
        return str_replace(' ', '', StringHelper::uppercaseFirstCharacterInEachWord(preg_replace('/[^\pL\pN]+/u', ' ', $input)));
    }

    /**
     * Converts an ID into a PascalCase name.
     * Words in the ID separated by `$separator` (defaults to '-') will be concatenated into a PascalCase name.
     * For example, 'post-tag' is converted to 'PostTag'.
     * @param string $input The ID to be converted.
     * @param string $separator The character used to separate the words in the ID.
     * @return string The resulting PascalCase name.
     */
    public function idToPascal(string $input, string $separator = '-'): string
    {
        return str_replace(' ', '', StringHelper::uppercaseFirstCharacterInEachWord(str_replace($separator, ' ', $input)));
    }

    /**
     * Returns a human-readable string.
     * @param string $input The string to humanize.
     * @param bool $uppercaseWords Whether to set all words to uppercase or not.
     * @return string
     */
    public function humanize(string $input, bool $uppercaseWords = false): string
    {
        $input = str_replace('_', ' ', preg_replace('/_id$/', '', $input));

        return $uppercaseWords ? StringHelper::uppercaseFirstCharacterInEachWord($input) : StringHelper::uppercaseFirstCharacter($input);
    }

    /**
     * Same as camelize but first char is in lowercase.
     *
     * Converts a word like "send_email" to "sendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "whoSOnline".
     * @param string $input The word to convert.
     * @return string
     */
    public function variablize(string $input): string
    {
        $input = $this->toPascal($input);

        return mb_strtolower(mb_substr($input, 0, 1)) . mb_substr($input, 1, null);
    }

    /**
     * Converts a class name to its table name (pluralized) naming conventions.
     *
     * For example, converts "Person" to "people".
     * @param string $className the class name for getting related table_name.
     * @return string
     */
    public function tableize(string $className): string
    {
        return $this->pluralize($this->pascalToUnderscore($className));
    }

    /**
     * Returns a string with all spaces converted to given replacement,
     * non word characters removed and the rest of characters transliterated.
     *
     * If intl extension isn't available uses fallback that converts latin characters only
     * and removes the rest. You may customize characters map via $transliteration property
     * of the helper.
     *
     * @param string $input An arbitrary string to convert.
     * @param string $replacement The replacement to use for spaces.
     * @param bool $lowercase whether to return the string in lowercase or not. Defaults to `true`.
     * @return string The converted string.
     */
    public function slug(string $input, string $replacement = '-', bool $lowercase = true): string
    {
        // replace all non words character
        $input = preg_replace('/[^a-zA-Z0-9]++/u', $replacement, $this->transliterate($input));
        // remove first and last replacements
        $input = preg_replace('/^(?:' . preg_quote($replacement, '/') . ')++|(?:' . preg_quote($replacement, '/') . ')++$/u' . ($lowercase ? 'i' : ''), '', $input);

        return $lowercase ? strtolower($input) : $input;
    }

    /**
     * Returns transliterated version of a string.
     *
     * If intl extension isn't available uses fallback that converts latin characters only
     * and removes the rest. You may customize characters map via $transliteration property
     * of the helper.
     *
     * @noinspection PhpComposerExtensionStubsInspection
     *
     * @param string $input Input string.
     * @param string|\Transliterator|null $transliterator either a {@see \Transliterator} or a string
     * from which a {@see \Transliterator} can be built. If null, value set with {@see withTransliterator()}
     * or {@see TRANSLITERATE_LOOSE} is used.
     * @return string
     */
    public function transliterate(string $input, $transliterator = null): string
    {
        if ($this->useIntl()) {
            if ($transliterator === null) {
                $transliterator = $this->transliterator;
            }

            /* @noinspection PhpComposerExtensionStubsInspection */
            return transliterator_transliterate($transliterator, $input);
        }

        return strtr($input, $this->transliterationMap);
    }

    /**
     * Converts a table name to its class name.
     *
     * For example, converts "people" to "Person".
     * @param string $tableName
     * @return string
     */
    public function tableToClass(string $tableName): string
    {
        return $this->toPascal($this->singularize($tableName));
    }

    /**
     * Converts number to its ordinal English form. For example, converts 13 to 13th, 2 to 2nd ...
     * @param int $number The number to get its ordinal value.
     * @return string
     */
    public function numberToOrdinal(int $number): ?string
    {
        if (\in_array($number % 100, range(11, 13), true)) {
            return $number . 'th';
        }
        switch ($number % 10) {
            case 1:
                return $number . 'st';
            case 2:
                return $number . 'nd';
            case 3:
                return $number . 'rd';
            default:
                return $number . 'th';
        }
    }

    /**
     * @return bool If intl extension should be used.
     */
    private function useIntl(): bool
    {
        return $this->withoutIntl === false && \extension_loaded('intl');
    }
}
