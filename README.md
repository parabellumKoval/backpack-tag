# Backpack-tag

[![Build Status](https://travis-ci.org/parabellumKoval/backpack-tag.svg?branch=master)](https://travis-ci.org/parabellumKoval/backpack-tag)
[![Coverage Status](https://coveralls.io/repos/github/parabellumKoval/backpack-tag/badge.svg?branch=master)](https://coveralls.io/github/parabellumKoval/backpack-tag?branch=master)

[![Packagist](https://img.shields.io/packagist/v/parabellumKoval/backpack-tag.svg)](https://packagist.org/packages/parabellumKoval/backpack-tag)
[![Packagist](https://poser.pugx.org/parabellumKoval/backpack-tag/d/total.svg)](https://packagist.org/packages/parabellumKoval/backpack-tag)
[![Packagist](https://img.shields.io/packagist/l/parabellumKoval/backpack-tag.svg)](https://packagist.org/packages/parabellumKoval/backpack-tag)

This package provides a quick starter kit for implementing tag for Laravel Backpack. Provides a database, CRUD interface, API routes and more.

## Installation

Install via composer
```bash
composer require parabellumKoval/backpack-tag
```

Migrate
```bash
php artisan migrate
```

### Publish

#### Configuration File
```bash
php artisan vendor:publish --provider="Backpack\Tag\ServiceProvider" --tag="config"
```

#### Views File
```bash
php artisan vendor:publish --provider="Backpack\Tag\ServiceProvider" --tag="views"
```

#### Migrations File
```bash
php artisan vendor:publish --provider="Backpack\Tag\ServiceProvider" --tag="migrations"
```

#### Routes File
```bash
php artisan vendor:publish --provider="Backpack\Tag\ServiceProvider" --tag="routes"
```

## Usage

### Seeders
```bash
php artisan db:seed --class="Backpack\Tag\database\seeders\tageeder"
```

## Security

If you discover any security related issues, please email 
instead of using the issue tracker.

## Credits

- [](https://github.com/parabellumKoval/backpack-tag)
- [All contributors](https://github.com/parabellumKoval/backpack-tag/graphs/contributors)
