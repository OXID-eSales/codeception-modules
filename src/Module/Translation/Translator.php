<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module\Translation;

use Symfony\Component\Translation\Translator as SymfonyTranslator;

class Translator implements TranslatorInterface
{
    /**
     * @var SymfonyTranslator
     */
    private static $sfTranslator;

    public static function initialize($paths)
    {
        self::$sfTranslator = new SymfonyTranslator('en');

        self::$sfTranslator->addLoader('oxphp', new LanguageDirectoryReader());

        $languageDir = self::getLanguageDirectories($paths, 'de');

        self::$sfTranslator->addResource('oxphp', $languageDir, 'de');

        $languageDir = self::getLanguageDirectories($paths, 'en');

        self::$sfTranslator->addResource('oxphp', $languageDir, 'en');
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function translate($string)
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
    private static function getLanguageDirectories($paths, $language)
    {
        $languageDirectories = [];

        foreach ($paths as $path) {
            $languageDirectories[] = $path . $language;
        }

        return $languageDirectories;
    }

}