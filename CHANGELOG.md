# Change Log for OXID eShop Codeception Modules

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.0.0] -  Unreleased

### Removed
- Removed database encoding:
    - config_key from ``src/Codeception/Template/ModuleAcceptance/acceptance.suite.yml``
    - protected requiredFields in ``src/Module/Database.php``

## [1.2.0] - Unreleased

### Added
- Use declare(strict_types=1); in template files
- Screen shot url for failing tests

### Fix
- Fix bootstrap's template configuration with ^3.1 codeception version

## [1.1.0] -  2019-11-07

### Added
- Template for module tests initialization is added
- OxideshopAdmin module with admin frames selection actions
- OxideshopModules module with just the OXID module activation

### Fix
- Improved the waitForAjax method jQuery waiting condition to work with shortened and full jQuery calls

## [1.0.0] -  2019-07-26

### Added
- First version of the module introduced

[1.1.0]: https://github.com/OXID-eSales/codeception-page-object/compare/v1.0.0..HEAD
[1.0.0]: https://github.com/OXID-eSales/codeception-page-object/compare/78f569ceafc73440b800553c2f78885292aeccf8..v1.0.0
