# OXID eShop Codeception Modules

Codeception helper modules for OXID eShop

## Installation
  
This component is installable via composer:

```
composer require --dev oxid-esales/codeception-modules
```

## Usage
  
You can use modules from this package as any other codeception module, 
by adding them to the ``enabled`` modules section in your codeception 
suite configurations.
  
**Example:**
  
```
modules:
  enabled:
    - \OxidEsales\Codeception\Module\Oxideshop:
      screen_shot_url: '%SCREEN_SHOT_URL%'
      depends:
        - WebDriver
        - Db
    - \OxidEsales\Codeception\Module\Database:
      depends: Db
    - \OxidEsales\Codeception\Module\Translation\TranslationsModule:
      shop_path: '%SHOP_SOURCE_PATH%'
      paths: 'Application/views/theme-id'
```

After adding to the suite configuration, rebuild the codeception 
configuration by running the command:

```
codecept build
```

## Bugs and issues

If you experience any bugs or issues, please report them in 
the Issues section.
