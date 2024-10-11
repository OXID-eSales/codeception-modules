# Change Log for OXID eShop Codeception Modules

## v3.2.1 - Unreleased

### Changed
- Raised timeout for running process from commandline
- Configurable module activation and deactivation between tests and suites

## v3.2.0 - 2023-11-16

### Added
- Modules:
  - `ShopSetup\SetupEnvironment`
- Methods:
  - `Database::executeQuery()`

## v3.1.0 - 2023-04-20

### Added
- Methods:
  - `OxideshopAdmin`:
    - `selectGenericExportStatusFrame()`
    - `selectGenericExportMainFrame()`

### Changed
- License updated

### Removed
- Dependency to `webmozart/path-util`

## v3.0.0 - 2022-10-28

### Changed
- Updated Symfony components to v6

### Added
- SelectTheme module
- ShopSetup module
- CachingTrait
- CommandTrait
- DatabaseDefaultsFileGenerator class

## v2.0.0 - 2021-07-06

### Added
- Added `codeception/module-filesystem` in `composer.json` file

### Removed
- Removed database encoding:
  - config_key from ``src/Codeception/Template/ModuleAcceptance/acceptance.suite.yml``
  - protected requiredFields in ``src/Module/Database.php``

### Changed
- Public method signatures in `Module\OxideshopModules`

## v1.7.0 - unreleased

### Added
- SelectTheme module

## v1.6.0 - 2021-07-06

### Added
- Support array as translation directories list

### Changed
- InvalidResourceException is thrown if not existing translations directory is listed

## v1.5.0 - 2021-03-25

### Added
- Support of codeception v4

## v1.4.0 - 2020-11-10

### Added
- Add `shopId` parameter to `updateConfigInDatabase` method
- Add `updateConfigInDatabaseForShops` method as alias for multiple shop calls of `updateConfigInDatabase`
- Method:
    - `OxidEsales\Codeception\Module\OxideshopModules::uninstallModule`
    - `OxidEsales\Codeception\Module\Database::grabConfigValueFromDatabase`
    - `OxidEsales\Codeception\Module\Oxideshop::regenerateDatabaseViews`

## v1.3.0 - 2020-07-06

### Added
- Flow theme module
- Methods:
    - `Module\Oxideshop::seeAndClick`

### Deprecated
- Activate modules within oxideshop

## v1.2.0 - 2020-01-02

### Added
- Use declare(strict_types=1); in template files
- Screen shot url for failing tests

### Fix
- Fix bootstrap's template configuration with ^3.1 codeception version

### Removed
- Removed database encoding:
    - config_key from ``src/Codeception/Template/ModuleAcceptance/acceptance.suite.yml``
    - protected requiredFields in ``src/Module/Database.php``

## v1.1.0 -  2019-11-07

### Added
- Template for module tests initialization is added
- OxideshopAdmin module with admin frames selection actions
- OxideshopModules module with just the OXID module activation

### Fix
- Improved the waitForAjax method jQuery waiting condition to work with shortened and full jQuery calls

## v1.0.0 -  2019-07-26

### Added
- First version of the module introduced
