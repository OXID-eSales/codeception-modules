# Codeception Oxideshop Module

Codeception helper module for OXID eShop

## Installation
  
This component is installable via composer:

```
composer require --dev oxid-esales/codeception-module
```

## Usage
  
You can use this module as any other codeception module, by adding to 
the ``enabled`` modules section in your codeception suite configurations.
  
**Example:**
  
```
modules:
  enabled:
    - \OxidEsales\Codeception\Module\Oxideshop
    - \OxidEsales\Codeception\Module\Database:
      depends: Db
      config_key: XXXXXXXXX
    - \OxidEsales\Codeception\Module\Translation\TranslationsModule:
      shop_path: '%SHOP_SOURCE_PATH%'
      paths: 'Application/views/flow'
```

After adding to the suite configuration, rebuild the codeception configuration with:

```
codecept build
```

## Bugs and issues

If you experience any bugs or issues, please report them in 
the [Testing library](https://github.com/OXID-eSales/testing_library) 
Issues section.