<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\Translation;

use Symfony\Component\Translation\Translator as SymfonyTranslator;

class Translator implements TranslatorInterface
{
    public const TRANSLATION_DOMAIN_SHOP = 'shop';
    public const TRANSLATION_DOMAIN_ADMIN = 'admin';
    private static SymfonyTranslator $sfTranslator;
    private static string $domain = self::TRANSLATION_DOMAIN_SHOP;

    public static function initialize(string $locale, array $paths, array $fileNamePatterns): void
    {
        self::$sfTranslator = new SymfonyTranslator($locale);
        self::$sfTranslator->setFallbackLocales(['en', 'de']);
        self::$sfTranslator->addLoader('oxphp', new LanguageDirectoryReader($fileNamePatterns));

        self::addResource($locale, $paths, self::$domain);
    }

    public static function translate(string $string): string
    {
        return self::$sfTranslator->trans(id: $string, domain: self::$domain);
    }

    public static function addResource(string $locale, array $paths, string $domain): void
    {
        self::$sfTranslator->addResource(
            'oxphp',
            self::getLanguageDirectories($paths, $locale),
            $locale,
            $domain
        );
    }

    public static function switchTranslationDomain(string $domain): void
    {
        self::$domain = $domain;
    }

    private static function getLanguageDirectories(array $paths, string $language): array
    {
        array_walk($paths, static function (&$v) use ($language) {
            $v .= $language;
        });

        return $paths;
    }
}
