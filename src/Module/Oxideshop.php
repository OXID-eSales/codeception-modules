<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Exception\ElementNotFound;
use Codeception\Exception\MalformedLocatorException;
use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Db;
use Codeception\Module\WebDriver;
use Codeception\TestInterface;
use Facebook\WebDriver\WebDriverElement;

class Oxideshop extends Module implements DependsOnModule
{
    use CachingTrait;
    use CommandTrait;

    private WebDriver $webDriver;

    private Db $database;

    protected array $config = [
        'screen_shot_url' => '',
        'page_load_timeout' => 0.25,
    ];

    public function _depends(): array
    {
        return [
            WebDriver::class => 'Codeception\Module\WebDriver is required',
            Db::class => 'Codeception\Module\Db is required'
        ];
    }

    public function _inject(WebDriver $driver, Db $database): void
    {
        $this->webDriver = $driver;
        $this->database = $database;
    }

    public function _before(TestInterface $test): void
    {
        Context::resetActiveUser();
        $this->clearShopCache();
        $this->cleanUpCompilationDirectory();
    }

    public function _failed(TestInterface $test, $fail): void
    {
        $report = $test->getMetadata()->getReports();
        if (isset($report['png']) && $this->config['screen_shot_url']) {
            $fileName = basename($report['png']);
            $fullUrl = rtrim($this->config['screen_shot_url'], '/') . '/' . $fileName;
            $test->getMetadata()->addReport('png', $fullUrl);
        }
    }

    public function clearShopCache(): void
    {
        $this->webDriver->_restart();
        $this->processConsoleCommand('oe:cache:clear');
    }

    public function cleanUp(): void
    {
        $this->database->_beforeSuite();
    }

    public function clearString(string $line): string
    {
        return trim(preg_replace("/[ \t\r\n]+/", ' ', $line));
    }

    /**
     * @deprecated method will be removed in next major
     */
    public function addFetchListener(): string
    {
        $eventId = 'fetch_event_' . md5(uniqid(more_entropy: true));
        $this->webDriver->executeJs(
            "
window.$eventId = false;

// listen to fetch() calls
window.fetch = new Proxy(window.fetch, {
    apply(fetch, that, args) {
        const proxy = Reflect.apply(fetch, that, args);
        proxy.then(() => {
            window.$eventId = true;
        });

        return proxy;
    }
});
                "
        );

        return $eventId;
    }

    public function waitForFetchDone(string $eventId): void
    {
        $this->webDriver->waitForJS(script: "return window.$eventId === true", timeout: 10);
    }

    public function addAjaxListener(): string
    {
        $eventId = 'ajax_event_' . md5(uniqid(more_entropy: true));
        $this->webDriver->executeJs(
            "
window.$eventId = false;

// listen to XMLHttpRequest
window.XMLHttpRequest = new Proxy(window.XMLHttpRequest, {
    construct(xhr, args, that) {
        const proxy = Reflect.construct(xhr, args, that);
        proxy.addEventListener('readystatechange', function () {
            if (proxy.readyState === proxy.DONE) {
                window.$eventId = true;
            }
        }, false);

        return proxy;
    }
});
                "
        );

        return $eventId;
    }

    public function waitForAjaxDone(string $eventId): void
    {
        $this->webDriver->waitForJS(script: "return window.$eventId === true", timeout: 10);
    }

    public function waitForPageLoad(int $timeout = 60): void
    {
        $this->waitForDocumentReadyState($timeout);
        $this->webDriver->wait($this->config['page_load_timeout']);
    }

    public function waitForDocumentReadyState(int $timeout = 60): void
    {
        $this->webDriver->waitForJs('return document.readyState === "complete"', $timeout);
    }

    public function waitForTextUpdate(string $element, string $textBefore): void
    {
        $this->webDriver->waitForElementChange($element, function (WebDriverElement $element) use ($textBefore) {
            $text = $element->getText();

            return $text && $text !== $textBefore;
        });
    }

    public function seePageHasElement($element): bool
    {
        return count($this->getModule('WebDriver')->_findElements($element)) > 0;
    }

    /**
     * @see clickWIthLeftButton() - method imitates the similar behaviour
     */
    public function clickAndWait($link, $context = null): void
    {
        try {
            $this->webDriver->moveMouseOver($context ?? $link);
        } catch (ElementNotFound | MalformedLocatorException) {
            /**
             * $link = 'SOME TEXT PRESENT IN PAGE' and $context = null
             * Can't center view to the $link (it works only when XPath or CSS selector is available)
             * Continue with normal click() without scrolling and centering.
             */
        }
        $this->webDriver->click($link, $context);
        $this->waitForPageLoad();
    }

    /**
     * Method uses waitForText() which works only for exact matches
     * To ignore HTML tags, use $I->see() ($I->see("word1 word2") will match "word1<br/>word2")
     */
    public function seeText(string $text, ?string $selector = null, int $timeout = 10): void
    {
        $this->webDriver->waitForText(strip_tags($text), timeout: $timeout);
        $this->webDriver->see($text, $selector);
    }

    public function seeImage(string $selector): void
    {
        $this->webDriver->seeElement($selector);
        $imageHeight = $this->webDriver
            ->executeJS(
                "return document.querySelector('$selector').naturalHeight;"
            );
        $this->assertGreaterThan(
            1,
            $imageHeight,
            'The image is not visible.'
        );
    }

    /**
     * Can be used instead of click() when expecting an alert popup
     * (alert is implemented as a JS exception, therefore, waitForJs() will fail here)
     */
    public function openAlert($link, $context = null): void
    {
        $this->webDriver->moveMouseOver($link);
        $this->webDriver->click($link, $context);
        $this->webDriver->wait($this->config['page_load_timeout']);
        $this->webDriver->seeInPopup('');
    }

    public function regenerateDatabaseViews(): void
    {
        $this->processVendorBinary('oe-eshop-db_views_generate');
    }
}
