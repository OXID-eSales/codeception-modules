<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module\Translation;

interface TranslatorInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public static function translate(string $string);

}