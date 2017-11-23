# GrapeFluid/Configuration <img align="right" height="40px" src="https://developers.grapesc.cz/logo_inline.png">

[![PHP from Packagist](https://img.shields.io/packagist/php-v/grape-fluid/configuration.svg?style=flat-square)](https://packagist.org/packages/grape-fluid/configuration)
[![Build Status](https://img.shields.io/travis/grape-fluid/configuration.svg?style=flat-square)](https://travis-ci.org/grape-fluid/configuration)
[![Code coverage](https://img.shields.io/coveralls/grape-fluid/configuration.svg?style=flat-square)](https://coveralls.io/r/grape-fluid/configuration)
[![Licence](https://img.shields.io/packagist/l/grape-fluid/configuration.svg?style=flat-square)](https://packagist.org/packages/grape-fluid/configuration)
[![Downloads this Month](https://img.shields.io/packagist/dm/grape-fluid/configuration.svg?style=flat-square)](https://packagist.org/packages/grape-fluid/configuration)
[![Downloads total](https://img.shields.io/packagist/dt/grape-fluid/configuration.svg?style=flat-square)](https://packagist.org/packages/grape-fluid/configuration)
[![Latest stable](https://img.shields.io/packagist/v/grape-fluid/configuration.svg?style=flat-square)](https://packagist.org/packages/grape-fluid/configuration)


## Install

```
composer require grape-fluid/configuration
```

## Version


## Registration

```yaml
extensions:
    configuration: Grapesc\GrapeFluid\Configuration\Bridges\ConfigurationDI\ConfigurationExtension
```

## Advanced settings

```yaml
services:
    # Service that implements Grapesc\GrapeFluid\Configuration\IStorage
    - Grapesc\GrapeFluid\Configuration\Storage\NetteDatabase('your_configuration_table_name')
    # Optional - Service that implements Grapesc\GrapeFluid\Configuration\Crypt\ICrypt
    - Grapesc\GrapeFluid\Configuration\Crypt\OpenSSLCrypt('your_secret_token')
```

## Example 

```yaml
parameters:
    testapi:
        url: @c::val(test.api.url)
        port: @c::val(test.api.port)
        username: @c::val(test.api.username)
        password: @c::val(test.api.password)
        debug: @c::val(test.api.debug)
        endpoints:
            test: @c::con(%testapi.url%, /getTest)    

configuration:
    test.api.url: [default: "http://localhost/api", description: "API base path"]
    test.api.port: [default: 80, description: "API port", type: integer, nullable: false]
    test.api.username: [default: "admin", description: "API username"]
    test.api.password: [description: "API password", secured: true]
    test.api.debug: [default: false, description: "Enable debug", type: boolean, nullable: false]
```
