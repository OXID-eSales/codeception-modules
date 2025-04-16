# Change Log for OXID eShop Codeception Modules

## v4.2.0 - unreleased

### Deprecated
- Functionality for testing browser-based shop setup
- `DatabaseDefaultsFileGenerator` will be merged with the `Database` module
- Redundant `seeAndClick()` and `waitForAjax()` methods

## v4.1.0 - 2024-10-14

### Added
- Functionality for copying fixtures into `source/out` directory

## v4.0.1 - 2024-10-14

### Changed
- Raised timeout for running process from commandline

## v4.0.0 - 2024-04-03

### Added
-  Translator can be configured to use multiple translation domains
- `Email` module based on Mailpit

### Removed
- Example configuration in `Codeception\Template`
- `FlowTheme` module
