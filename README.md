<p align="center">
    <a href="https://github.com/yii-extension" target="_blank">
        <img src="https://lh3.googleusercontent.com/ehSTPnXqrkk0M3U-UPCjC0fty9K6lgykK2WOUA2nUHp8gIkRjeTN8z8SABlkvcvR-9PIrboxIvPGujPgWebLQeHHgX7yLUoxFSduiZrTog6WoZLiAvqcTR1QTPVRmns2tYjACpp7EQ=w2400" height="100px">
    </a>
    <h1 align="center">Simple Model for Yii Packages</h1>
    <br>
</p>

[![Total Downloads](https://poser.pugx.org/yii-extension/simple-model/downloads.png)](https://packagist.org/packages/yii-extension/simple-model)
[![Build Status](https://github.com/yii-extension/simple-model/workflows/build/badge.svg)](https://github.com/yii-extension/simple-model/actions?query=workflow%3Abuild)
[![codecov](https://codecov.io/gh/yii-extension/simple-model/branch/master/graph/badge.svg?token=WQI25yo4d3)](https://codecov.io/gh/yii-extension/simple-model)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https://badge-api.stryker-mutator.io/github.com/yii-extension/simple-model/master)](https://dashboard.stryker-mutator.io/reports/github.com/yii-extension/simple-model/master)
[![static analysis](https://github.com/yii-extension/simple-model/workflows/static%20analysis/badge.svg)](https://github.com/yii-extension/simple-model/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yii-extension/simple-model/coverage.svg)](https://shepherd.dev/github/yii-extension/simple-model)

## Installation

```shell
composer require yii-extension/simple-model
```

### Usage

You can create two types of classes, they are `Model::class` and `FormModel::class` through inheritance.

`Model::class` is the base class for all models, defined methods are:

- `attributes()` - returns the list names of attributes.
- `errors()` - returns instance of `ModelErrors::class`:
  - `add(string $attribute, string $error)` - adds an error to the specified attribute.
  - `addErrors(array $values)` - adds the errors for model instance.
  - `clear(?string $attribute = null)` - removes errors for the specified attribute, or attribute is `null` remove all attributes.
  - `get(string $attribute)` - returns the error message for the specified attribute.
  - `getAll()` - returns all errors.
  - `getFirst(string $attribute)` - returns the first error message for the specified attribute.
  - `getFirsts()` - returns the first error message for all attributes.
  - `getSummary()` -  returns errors for all attributes as a one-dimensional array.
  - `getSummaryFirst()` - returns the first error message for all attributes as a one-dimensional array.
  - `has(?string $attribute = null)` - returns a value indicating whether there is any validation error, use `null` to check all attributes.
- `getAttributeValue(string $attribute)` - returns the attribute value.
- `getCastValue(string $attribute)` - returns the attribute `PHP` type cast value.
- `getFormName()` - returns the form name.
- `getRules()` - returns the validation rules.
- `getRulesWithAttributes` - returns the validation rules with `PHP` attributes.
- `has(string $attribute)` - returns whether the attribute exists.
- `isEmpty()` - returns whether the model instance is empty.
- `load(array $data, ?string $formName = null)` - loads the model with the given data.
- `setFormErrors(ModelErrorsContract $ModelErrors)` - sets custom class for `ModelErrors::class`.
- `setValidator(ValidatorInterface $validator)` - sets custom class for `ValidatorInterface::class`.
- `setValue(string $name, mixed $value)` - sets the attribute value.
- `setValues(array $values)` - sets the attribute values for model instance.
- `types()` - returns instance of `ModelTypes::class`.
  - `attributes` - returns the list names of attributes with `PHP` types.
  - `getType(string $attribute)` - returns the attribute type.
  - `phpTypeCast(string $name, mixed $value)` - returns the attribute `PHP` type cast value.
- `validate()` - validates the model instance.
- `validator()` - returns instance of `ValidatorInterface::class`.

`FormModel::class` is the base class for all form models, defined methods are:

- `getHint(string $attribute)` - returns the hint for the attribute.
- `getHints()` - returns the hints for all attributes.
- `getLabel(string $attribute)` - returns the label for the attribute.
- `getLabels()` - returns the labels for all attributes.
- `getPlaceholder(string $attribute)` - returns the placeholder for the attribute.
- `getPlaceholders()` - returns the placeholders for all attributes.


### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework. To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin -j2 --ignore-msi-with-no-mutations --only-covered
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/docs). To run static analysis:

```shell
./vendor/bin/psalm
```

### License

The `yii-extension/simple-model` is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Extension](https://github.com/yii-extension).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Powered by Yii Framework

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
