# Change Log for OXID eShop Codeception Modules

## v5.0.0 - unreleased

### Added
- Functionality for copying fixtures into `source/out` directory
- Possibility to update project configuration YAMLs
- Methods to track `AJAX` and `fetch` requests' completion
- `clickAndWait()` and `seeText()` methods as more stable alternatives for `click()` and `see()`

### Changed
- `ShopSetup` module accepts DB name and Path to the MySQL option file as parameters
- Functionality from `DatabaseDefaultsFileGenerator` moved into `Database` module
- Configuration value `page_load_timeout` to define explicit wait on page load

### Fixed
- Category file cache is not cleared on `Oxideshop` module start

### Removed
- Dependency on `Facts` component
- Redundant `seeAndClick()` and `waitForAjax()` methods
