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
     * @var string
     */
    private $currentLocale = 'en';

    /**
     * @var array
     */
    private $fileNamePatterns = ['*lang.php', '*options.php'];

    /**
     * @var array
     */
    protected $config = [
        'paths' => null,
        'locale' => null,
        'file_name_patterns' => null,
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

        Translator::initialize(
            $this->getCurrentLocale(),
            $this->getLanguageDirectoryPaths(),
            $this->getFileNamePatterns()
        );
    }

    /**
     * @return array
     */
    private function getLanguageDirectoryPaths(): array
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

    /**
     * @return string
     */
    private function getCurrentLocale(): string
    {
        if (isset($this->config['locale'])) {
            return $this->config['locale'];
        }
        return $this->currentLocale;
    }

    /**
     * @return array
     */
    private function getFileNamePatterns(): array
    {
        if (isset($this->config['file_name_patterns'])) {
            $this->fileNamePatterns = explode(',', $this->config['file_name_patterns']);
        }
        return $this->fileNamePatterns;
    }
}
