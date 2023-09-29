# Change Log for OXID eShop Codeception Modules

## [v3.2.0] - Unreleased

### Added
- Modules:
  - `ShopSetup\SetupEnvironment`
- Methods:
  - `Database::executeQuery()`

### Deprecated
- Example configuration in `Codeception\Template`
- `FlowTheme` module

## [v3.1.0] - 2023-04-20

### Added
- Methods:
  - `OxideshopAdmin`:
    - `selectGenericExportStatusFrame()`
    - `selectGenericExportMainFrame()`

### Changed
- License updated

### Removed
- Dependency to `webmozart/path-util`

## [v3.0.0] - 2022-10-28

### Changed
- Updated Symfony components to v6

### Added
- SelectTheme module
- ShopSetup module
- CachingTrait
- CommandTrait
- DatabaseDefaultsFileGenerator class
