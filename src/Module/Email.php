<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\Module;
use Codeception\Module\Mailpit;

class Email extends Module implements DependsOnModule
{
    use EmailTestTrait;

    private Mailpit $mailpit;

    public function _depends(): array
    {
        return [Mailpit::class => 'Codeception\Module\Mailpit is required'];
    }

    public function _inject(Mailpit $mailpit): void
    {
        $this->mailpit = $mailpit;
    }

    public function openRecentEmail(): void
    {
        $this->mailpit->fetchEmails();
        $this->mailpit->openNextUnreadEmail();
    }
}
