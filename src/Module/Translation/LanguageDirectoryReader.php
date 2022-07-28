<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\Translation;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\MessageCatalogue;

class LanguageDirectoryReader extends ArrayLoader
{
    /**
     * @var array
     */
    private $fileNamePatterns;

    /**
     * LanguageDirectoryReader constructor.
     *
     * @param array $fileNamePatterns An array of file name patterns to search (default '*lang.php', '*option.php').
     */
    public function __construct(array $fileNamePatterns)
    {
        $this->fileNamePatterns = $fileNamePatterns;
    }

    /**
     * {@inheritdoc}
     */
    public function load(mixed $resource, string $locale, string $domain = 'messages'): MessageCatalogue
    {
        if (!is_array($resource)) {
            $resource = [$resource];
        }

        $messages = [];

        foreach ($resource as $directory) {
            if (!file_exists($directory)) {
                throw new InvalidResourceException(sprintf('Translation directory "%s" not found.', $directory));
            }
            $messages = $this->loadDirectory($messages, $directory);
        }
        return parent::load($messages, $locale, $domain);
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function loadFile(SplFileInfo $file): array
    {
        $aLang = [];
        require $file;
        return $aLang;
    }

    /**
     * @param array  $messages
     * @param string $directory
     *
     * @return array
     */
    private function loadDirectory(array $messages, string $directory): array
    {
        $finder = $this->findFiles($directory);

        foreach ($finder as $file) {
            $lang = $this->loadFile($file);
            // not an array
            if (!\is_array($lang)) {
                throw new InvalidResourceException(sprintf('Unable to load file "%s".', $file));
            }

            $messages = array_merge($messages, $lang);
        }
        return $messages;
    }

    /**
     * @param string $directory
     *
     * @return Finder
     */
    private function findFiles(string $directory): Finder
    {
        $finder = new Finder();
        $finder = $finder->files()->in($directory);

        foreach ($this->getFileExtensionPattern() as $pattern) {
            $finder->name($pattern);
        }

        return $finder;
    }

    /**
     * @return array
     */
    private function getFileExtensionPattern(): array
    {
        return $this->fileNamePatterns;
    }
}
