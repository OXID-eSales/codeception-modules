<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\Codeception\Module;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;

trait ThemeSettingTrait
{
    private const BACKUP_SUFFIX = '.testing-backup';

    private string $themeConfigurationPath = '';
    private string $themeEnvironmentConfigurationPath = '';

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
        $this->themeConfigurationPath = $this->getThemeConfigurationPath($themeId, $shopId);
        $this->backupConfiguration($this->themeConfigurationPath);
    }

    public function restoreThemeConfiguration(): void
    {
        if ($this->themeConfigurationPath !== '') {
            $this->restoreConfiguration($this->themeConfigurationPath);
            $this->clearShopCache();
        }
    }

    public function cleanupThemeConfigurationBackup(): void
    {
        if ($this->themeConfigurationPath !== '') {
            $this->cleanupConfigurationBackup($this->themeConfigurationPath);
        }
    }

    public function backupThemeEnvironmentConfiguration(string $themeId = 'apex', int $shopId = 1): void
    {
        $this->themeEnvironmentConfigurationPath = $this->getThemeEnvironmentConfigurationPath($themeId, $shopId);
        $this->backupConfiguration($this->themeEnvironmentConfigurationPath);
    }

    public function restoreThemeEnvironmentConfiguration(): void
    {
        if ($this->themeEnvironmentConfigurationPath !== '') {
            $this->restoreConfiguration($this->themeEnvironmentConfigurationPath);
            $this->clearShopCache();
        }
    }

    public function cleanupThemeEnvironmentConfigurationBackup(): void
    {
        if ($this->themeEnvironmentConfigurationPath !== '') {
            $this->cleanupConfigurationBackup($this->themeEnvironmentConfigurationPath);
        }
    }

    public function clearThemeEnvironmentConfiguration(string $themeId = 'apex', int $shopId = 1): void
    {
        (new Filesystem())->remove($this->getThemeEnvironmentConfigurationPath($themeId, $shopId));
        $this->clearShopCache();
    }

    public function updateThemeSetting(string $name, mixed $value, string $themeId = 'apex', int $shopId = 1): void
    {
        $this->updateSettingValue(
            $this->getThemeConfigurationPath($themeId, $shopId),
            $name,
            $value
        );
    }

    public function updateThemeEnvironmentSetting(
        string $name,
        mixed $value,
        string $themeId = 'apex',
        int $shopId = 1
    ): void {
        $this->updateSettingValue(
            $this->getThemeEnvironmentConfigurationPath($themeId, $shopId),
            $name,
            $value
        );
    }

    private function getThemeConfigurationPath(string $themeId, int $shopId): string
    {
        return Path::join(
            (new BasicContext())->getShopConfigurationDirectory($shopId),
            'themes',
            $themeId . '.yaml'
        );
    }

    private function getThemeEnvironmentConfigurationPath(string $themeId, int $shopId): string
    {
        return Path::join(
            EnvUrlFormatter::toEnvUrl((new BasicContext())->getProjectConfigurationDirectory()),
            'shops',
            (string) $shopId,
            'themes',
            $themeId . '.yaml'
        );
    }

    private function backupConfiguration(string $path): void
    {
        $filesystem = new Filesystem();
        $backupPath = $this->getConfigurationBackupPath($path);
        $filesystem->remove($backupPath);

        if ($filesystem->exists($path)) {
            $filesystem->copy($path, $backupPath, true);
        }
    }

    private function restoreConfiguration(string $path): void
    {
        $filesystem = new Filesystem();
        $backupPath = $this->getConfigurationBackupPath($path);

        if ($filesystem->exists($backupPath)) {
            $filesystem->copy($backupPath, $path, true);
        } else {
            $filesystem->remove($path);
        }
    }

    private function cleanupConfigurationBackup(string $path): void
    {
        (new Filesystem())->remove($this->getConfigurationBackupPath($path));
    }

    private function getConfigurationBackupPath(string $path): string
    {
        return $path . self::BACKUP_SUFFIX;
    }

    private function updateSettingValue(string $path, string $name, mixed $value): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir(Path::getDirectory($path));

        $data = $filesystem->exists($path) ? Yaml::parseFile($path) : [];
        $data['themeSettings'][$name]['value'] = $value;

        $filesystem->dumpFile($path, Yaml::dump($data, 10, 2));
        $this->clearShopCache();
    }

    private function clearShopCache(): void
    {
        $this->getModule(Oxideshop::class)->clearShopCachePreservingSession();
    }
}
