<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\Codeception\Module\Translation;

/**
 * Class TranslationsModule
 * @package OxidEsales\Codeception\Module\Translation
 */
class TranslationsModule extends \Codeception\Module
{
    /**
     * @var array
     */
    private $paths = ['Application/translations'];

    /**
     * @var array
     */
    protected $config = [
        'paths' => null,
    ];

    /**
     * @var array
     */
    protected $requiredFields = ['shop_path'];

    /**
     * Initializes translator
     */
    public function _initialize()
    {
        parent::_initialize();

        Translator::initialize($this->getLanguageDirectoryPaths());
    }

    /**
     * @return array
     */
    private function getLanguageDirectoryPaths()
    {
        $fullPaths = [];
        if ($this->config['paths']) {
            $customPaths = explode(',', $this->config['paths']);
            $this->paths = array_merge($this->paths, $customPaths);
        }
        foreach ($this->paths as $path) {
            $fullPaths[] = $this->config['shop_path'].'/'.trim($path, '/').'/';
        }
        return $fullPaths;
    }
}
