# Proyecto levantado en un entorno Dockerizado con LAMP.

- Sistema backend de symfony 6 que replica los endpoints para una Libreria.


## Proyecto creado con comando:

`symfony new nombre_proyecto`

## Dependencias instaladas


### Desarrollo:
```
  - composer require annotations 
  - composer require logger
  - composer require symfony/orm-pack  
  - composer require --dev symfony/maker-bundle
  - composer require symfony/serializer-pack
  - composer require friendsofsymfony/rest-bundle
  - composer require symfony/twig-pack
  - composer require symfony/validator doctrine/annotations
  - composer require form
  - composer require league/flysystem-bundle
```

### Tests:

```
 - composer require --dev symfony/test-pack
 - composer require --dev doctrine/doctrine-fixtures-bundle
 - composer require --dev dama/doctrine-test-bundle

```