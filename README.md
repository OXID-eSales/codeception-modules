# codeception-oxideshop-module
Codeception helper module for OXID eShop

##Installation
  
  You need to add the repository into your composer.json file

```
  composer require --dev oxid-esales/codeception-module
```

##Modules
  
  You can use module(s) as any other codeception module, by adding to the enabled modules in 
  your codeception suite configurations.
  
###Database module
  
```yml
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
  
  Update codeception build

```
  codecept build
```