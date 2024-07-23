# Change Log for OXID eShop Codeception Modules

## v5.0.0 - unreleased

### Added
- Functionality for copying fixtures into `source/out` directory
- Possibility to update project configuration YAMLs

### Changed
- `ShopSetup` module accepts DB name and Path to the MySQL option file as parameters
- Functionality from `DatabaseDefaultsFileGenerator` moved into `Database` module

### Fixed
- Category file cache is not cleared on `Oxideshop` module start
