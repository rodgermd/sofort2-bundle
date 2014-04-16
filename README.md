sofort2-bundle
==============

Symfony2 Bundle for Sofort PHP Library 2.x

Installation
------

### Add bundle
---

AppKernel.php:

```php
public function registerBundles()
{
    $bundles = array(
    ...
    new Sofort\SofortBundle(),
    ...
}
```

### Add controller routing
---

routing.yml:
``` yml
sofort_controller:
    resource: "@SofortBundle/Controller/"
    type: annotation
    prefix: /sofort
```
