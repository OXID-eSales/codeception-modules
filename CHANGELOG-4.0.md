# Change Log for OXID eShop Codeception Modules

## v4.1.0 - unreleased

### Added
- Functionality for copying fixtures into `source/out` directory

### Changed
- Raised timeout for running process from commandline

### Deprecated
- `ShopSetup` module's configuration will be extended with new parameters in v5.0
- `DatabaseDefaultsFileGenerator::generate()` logic will be moved into `Database` module

## v4.0.0 - 2024-04-03

### Added
-  Translator can be configured to use multiple translation domains
- `Email` module based on Mailpit

### Removed
- Example configuration in `Codeception\Template`
- `FlowTheme` module
