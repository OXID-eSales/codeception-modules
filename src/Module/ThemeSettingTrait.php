<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;

trait ThemeSettingTrait
{
    private string $themeConfigBackupPath = '';

    private string $themeConfigYamlPath = '';
    private bool $themeConfigExistedBefore = false;

    public function installThemeConfiguration(string $themeId = 'apex', int $shopId = 1): void
    {
        $source = Path::join(
            (new BasicContext())->getSourcePath(),
            'Application',
            'views',
            $themeId,
            'config.yaml'
        );
        $target = $this->getThemeConfigurationPath($themeId, $shopId);

        $data = Yaml::parseFile($source);
        $metadata = Yaml::parseFile(Path::join(Path::getDirectory($source), 'metadata.yaml'));
        $data['activated'] = true;
        $data['title'] = $metadata['title'] ?? '';
        $data['source'] = Path::makeRelative(
            Path::getDirectory($source),
            (new BasicContext())->getShopRootPath()
        );

        $filesystem = new Filesystem();
        $filesystem->mkdir(Path::getDirectory($target));
        $filesystem->dumpFile($target, Yaml::dump($data, 10, 2));
    }

    public function backupThemeConfiguration(string $themeId = 'apex', int $shopId = 1): void
    {
        $this->themeConfigYamlPath = $this->getThemeConfigurationPath($themeId, $shopId);
        $this->themeConfigBackupPath = $this->themeConfigYamlPath . '.testing-backup';

        $filesystem = new Filesystem();
        $this->themeConfigExistedBefore = $filesystem->exists($this->themeConfigYamlPath);
        if ($this->themeConfigExistedBefore) {
            $filesystem->copy($this->themeConfigYamlPath, $this->themeConfigBackupPath, true);
        }
    }

    public function restoreThemeConfiguration(): void
    {
        if ($this->themeConfigBackupPath === '') {
            return;
        }

        $filesystem = new Filesystem();
        if ($this->themeConfigExistedBefore && $filesystem->exists($this->themeConfigBackupPath)) {
            $filesystem->copy($this->themeConfigBackupPath, $this->themeConfigYamlPath, true);
        } elseif (!$this->themeConfigExistedBefore && $filesystem->exists($this->themeConfigYamlPath)) {
            $filesystem->remove($this->themeConfigYamlPath);
        }

        $this->getModule(Oxideshop::class)->clearShopCachePreservingSession();
    }

    public function cleanupThemeConfigurationBackup(): void
    {
        if ($this->themeConfigBackupPath === '') {
            return;
        }

        $filesystem = new Filesystem();
        if ($filesystem->exists($this->themeConfigBackupPath)) {
            $filesystem->remove($this->themeConfigBackupPath);
        }
    }

    public function updateThemeSetting(string $name, mixed $value, string $themeId = 'apex', int $shopId = 1): void
    {
        $yamlPath = $this->getThemeConfigurationPath($themeId, $shopId);

        $filesystem = new Filesystem();
        $filesystem->mkdir(Path::getDirectory($yamlPath));

        $data = $filesystem->exists($yamlPath) ? Yaml::parseFile($yamlPath) : [];
        $data['themeSettings'][$name]['value'] = $value;

        $filesystem->dumpFile($yamlPath, Yaml::dump($data, 10, 2));

        $this->getModule(Oxideshop::class)->clearShopCachePreservingSession();
    }

    private function getThemeConfigurationPath(string $themeId, int $shopId): string
    {
        return Path::join(
            (new BasicContext())->getShopConfigurationDirectory($shopId),
            'themes',
            $themeId . '.yaml'
        );
    }
}
