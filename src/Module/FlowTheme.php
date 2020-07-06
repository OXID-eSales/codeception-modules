<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use Codeception\Lib\Interfaces\DependsOnModule;
use Codeception\TestInterface;

/**
 * Class FlowTheme
 * @package OxidEsales\Codeception\Module
 */
class FlowTheme extends \Codeception\Module implements DependsOnModule
{
    /**
     * @var Database
     */
    private $database;

    /**
     * @return array
     */
    public function _depends(): array
    {
        return [
            Database::class => 'OxidEsales\Codeception\Module\Database is required'
        ];
    }

    /**
     * @param Database $database
     */
    public function _inject(Database $database): void
    {
        $this->database = $database;
    }

    /**
     * @param TestInterface $test
     */
    public function _before(TestInterface $test): void
    {
        $this->database->updateConfigInDatabase('stickyHeader', false, 'bool');
    }
}
