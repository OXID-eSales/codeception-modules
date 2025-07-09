# Change Log for OXID eShop Codeception Modules

## v5.0.0-alpha.2 - Unreleased

### Added
- Symfony 7.x support
- Method to check for image visibility

### Changed
- `ShopSetup` now requires the `theme_id` configuration field

### Removed
- `SelectTheme` module has been removed; theme activation is now managed by the `ShopSetup` module
- `FixtureFileNotFoundException` has been replaced with `InvalidArgumentException` for missing fixture files

## v5.0.0-alpha.1 - 2025-02-03

### Added
- Functionality for copying fixtures into `source/out` directory
- Possibility to update project configuration YAMLs
- Methods to track `AJAX` and `fetch` requests' completion
- `clickAndWait()` and `seeText()` methods as more stable alternatives for `click()` and `see()`
- Method to wait for an element's text update

### Changed
- `ShopSetup` module accepts DB name and Path to the MySQL option file as parameters
- Functionality from `DatabaseDefaultsFileGenerator` moved into `Database` module
- Configuration value `page_load_timeout` to define explicit wait on page load

### Fixed
- Category file cache is not cleared on `Oxideshop` module start

### Removed
- Dependency on `Facts` component
- Redundant `seeAndClick()` and `waitForAjax()` methods
