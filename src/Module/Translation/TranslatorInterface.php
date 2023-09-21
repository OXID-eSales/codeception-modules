<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module\Translation;

interface TranslatorInterface
{
    public static function translate(string $string): string;

    public static function switchTranslationDomain(string $domain): void;

    public static function addResource(string $locale, array $paths, string $domain): void;
}
