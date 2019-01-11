<?php
namespace OxidEsales\Codeception\Module;

use Codeception\Util\Fixtures;

class FixturesHelper
{
    /**
     * Loads fixture data.
     *
     * @param string $fixtureFilePath
     */
    public function loadRuntimeFixtures($fixtureFilePath)
    {
        $fixtures = require($fixtureFilePath);
        foreach ($fixtures as $key => $data) {
            Fixtures::add($key, $data);
        }
    }
}
