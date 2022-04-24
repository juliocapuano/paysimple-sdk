# PaySimple PHP SDK

[comment]: <> ([![Latest Version on Packagist]&#40;https://img.shields.io/packagist/v//paysimple_sdk.svg?style=flat-square&#41;]&#40;https://packagist.org/packages//paysimple_sdk&#41;)

[comment]: <> ([![GitHub Tests Action Status]&#40;https://img.shields.io/github/workflow/status//paysimple_sdk/run-tests?label=tests&#41;]&#40;https://github.com//paysimple_sdk/actions?query=workflow%3ATests+branch%3Amaster&#41;)

[comment]: <> ([![GitHub Code Style Action Status]&#40;https://img.shields.io/github/workflow/status//paysimple_sdk/Check%20&%20fix%20styling?label=code%20style&#41;]&#40;https://github.com//paysimple_sdk/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster&#41;)

[comment]: <> ([![Total Downloads]&#40;https://img.shields.io/packagist/dt//paysimple_sdk.svg?style=flat-square&#41;]&#40;https://packagist.org/packages//paysimple_sdk&#41;)

---

This package is a simple implementation of the PaySimple Api for PHP

[<img src="https://paysimple.com/assets/img/logo-color.png" width="200px" />](https://paysimple.com/)

---

## Installation

You can install the package via composer:

```bash
composer require juliocapuano/paysimple_sdk
```

## Usage

### Init the client base

```php
$user_id ='<< PaySimple User ID >>';
$api_key ='<< PaySimple API Key >>';
$is_sanbox = true; // sanbox API for testing (optional)
$paysimple = new PaySimple\V4\PaySimple($user_id, $api_key,[$is_sanbox]);
```

### Accessing to methods

```php
// instance
$paysimple = new PaySimple\V4\PaySimple($user_id, $api_key,[$is_sanbox]);

// primary methods for specific instances 
$customerServiceInstance = $paysimple->customers();
$accountServiceInstance = $paysimple->accounts();
$paymentServiceInstance = $paysimple->payments();
$merchantServiceInstance = $paysimple->merchant();
//$recurrentPaymentServiceInstance = $paysimple->recurrentPayments(); // not implemented 


// creating customer instance 
$customer_data =[...]; // array data 


$new_customer = $paysimple->customers()->new($customer_data);
// or
$new_customer = $customerServiceInstance->new($customer_data);
```

## Testing

```bash
composer test
```

[comment]: <> (## Changelog)

[comment]: <> (Please see [CHANGELOG]&#40;CHANGELOG.md&#41; for more information on what has changed recently.)

[comment]: <> (## Contributing)

[comment]: <> (Please see [CONTRIBUTING]&#40;.github/CONTRIBUTING.md&#41; for details.)

[comment]: <> (## Security Vulnerabilities)

[comment]: <> (Please review [our security policy]&#40;../../security/policy&#41; on how to report security vulnerabilities.)

## Credits

- [Julio Capuano](https://github.com/.)

[comment]: <> (- [All Contributors]&#40;../../contributors&#41;)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
