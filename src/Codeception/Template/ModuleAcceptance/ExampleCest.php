<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace {{namespace}}\Acceptance;

use OxidEsales\Codeception\Page\Home;
use OxidEsales\Codeception\Module\Translation\Translator;
use {{namespace}}\AcceptanceTester;

final class ExampleCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function frontPageWorks(AcceptanceTester $I)
    {
        $homePage = new Home($I);
        $I->amOnPage($homePage->URL);
        $I->see(Translator::translate("HOME"));
    }
}
