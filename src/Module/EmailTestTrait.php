<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

trait EmailTestTrait
{
    public function seeInEmailHtmlBody(string $expected): void
    {
        $this->mailpit->seeInOpenedEmailHtmlBody($expected);
    }

    public function seeInEmailPlainBody(string $expected): void
    {
        $this->mailpit->seeInOpenedEmailTextBody($expected);
    }

    public function seeInEmailSubject(string $expected): void
    {
        $this->mailpit->seeInOpenedEmailSubject($expected);
    }

    public function seeInEmailTo(string $expected): void
    {
        $this->mailpit->seeInOpenedEmailToField($expected);
    }

    public function seeInEmailFrom(string $expected): void
    {
        $this->mailpit->seeInOpenedEmailSender($expected);
    }
}
