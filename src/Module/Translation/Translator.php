<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module\Translation;

use Symfony\Component\Translation\Translator as SymfonyTranslator;

/**
 * Class Translator
 * @package OxidEsales\Codeception\Module\Translation
 */
class Translator implements TranslatorInterface
{
    /**
     * @var SymfonyTranslator
     */
    private static $sfTranslator;

    /**
     * @param string $locale
     * @param array  $paths
     * @param array  $fileNamePatterns
     */
    public static function initialize(string $locale, array $paths, array $fileNamePatterns)
    {
        self::$sfTranslator = new SymfonyTranslator($locale);
        self::$sfTranslator->setFallbackLocales(['en', 'de']);
        self::$sfTranslator->addLoader('oxphp', new LanguageDirectoryReader($fileNamePatterns));

        $languageDirectory = self::getLanguageDirectories($paths, $locale);
        self::$sfTranslator->addResource('oxphp', $languageDirectory, $locale);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function translate(string $string)
    {
        return self::$sfTranslator->trans($string);
    }

    /**
     * Returns language map array
     *
     * @param array  $paths
     * @param string $language Language index
     *
     * @return array
     */
    private static function getLanguageDirectories(array $paths, string $language)
    {
        $languageDirectories = [];

        foreach ($paths as $path) {
            $languageDirectories[] = $path . $language;
        }

        return $languageDirectories;
    }
}
