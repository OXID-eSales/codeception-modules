<?php

namespace {{namespace}}\Acceptance;

use OxidEsales\Codeception\Page\Home;
use OxidEsales\Codeception\Module\Translation\Translator;
use {{namespace}}\AcceptanceTester;

class ExampleCest
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
