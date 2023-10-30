<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module\Translation;

use Codeception\Module;
use function is_array;

class TranslationsModule extends Module
{
    private array $translationPaths;
    private string $currentLocale = 'en';
    private array $fileNamePatterns = ['*lang.php', '*options.php'];
    protected array $config = [
        'paths' => null,
        'locale' => null,
        'file_name_patterns' => null,
    ];
    protected array $requiredFields = ['shop_path'];

    public function _initialize(): void
    {
        parent::_initialize();

        $this->resetTranslationPaths();
        if ($this->config['paths']) {
            $this->appendTranslationPaths((array)$this->config['paths']);
        }
        Translator::initialize(
            $this->getCurrentLocale(),
            $this->getLanguageDirectoryPaths(),
            $this->getFileNamePatterns()
        );
        if (isset($this->config['paths_admin'])) {
            $this->resetTranslationPaths();
            $this->appendTranslationPaths((array)$this->config['paths_admin']);
            Translator::addResource(
                $this->getCurrentLocale(),
                $this->getLanguageDirectoryPaths(),
                Translator::TRANSLATION_DOMAIN_ADMIN
            );
        }
    }

    private function getLanguageDirectoryPaths(): array
    {
        $fullPaths = [];
        foreach ($this->translationPaths as $translationPath) {
            $fullPaths[] = $this->config['shop_path'] . '/' . trim($translationPath, '/') . '/';
        }
        return $fullPaths;
    }

    private function resetTranslationPaths(): void
    {
        $this->translationPaths = ['Application/translations'];
    }

    private function appendTranslationPaths(array $paths): void
    {
        $this->translationPaths = array_merge(
            $this->translationPaths,
            $this->normalizeCustomPaths($paths)
        );
    }

    private function normalizeCustomPaths($paths): array
    {
        if (!is_array($paths)) {
            $paths = explode(',', $paths);
        }

        return $paths;
    }

    private function getCurrentLocale(): string
    {
        return $this->config['locale'] ?? $this->currentLocale;
    }

    private function getFileNamePatterns(): array
    {
        if (isset($this->config['file_name_patterns'])) {
            $this->fileNamePatterns = explode(',', $this->config['file_name_patterns']);
        }
        return $this->fileNamePatterns;
    }
}
