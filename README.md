# honeycomb-translations  
https://github.com/honey-comb/translations

## Description

HoneyComb CMS translations package. It uses 
https://github.com/spatie/laravel-translation-loader package for loading translations from database.
Feel free to check their documentation.

# Attention

This is part resources package for HoneyComb CMS package.

If you want to use laravel version 5.5.* [use translations package version 0.1.*](https://github.com/honey-comb/translations/tree/0.1.1 "Translations package version 0.1.*")

## Requirement

 - php: `^7.1`
 - laravel: `^5.6`
 - composer
 
 ## Installation

Begin by installing this package through Composer.


```js
	{
	    "require": {
	        "honey-comb/translations": "0.2.*"
	    }
	}
```
or
```js
    composer require honey-comb/translations
```

## Laravel integration

Publish package config
```php
php artisan vendor:publish --provider="HoneyComb\Translations\Providers\HCTranslationServiceProvider"
```    
You can set up file names, which won't be imported to database in `config/translations-loader.php` file.

```php
'exclude_groups' => [
   //
],
```

Run Artisan commands

`php artisan hc:update`

// imports project translations files  
`php artisan import:translations`
