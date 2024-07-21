# Enumerate
[![QA](https://github.com/fab2s/Enumerate/actions/workflows/qa.yml/badge.svg)](https://github.com/fab2s/Enumerate/actions/workflows/qa.yml) [![CI](https://github.com/fab2s/Enumerate/actions/workflows/ci.yml/badge.svg)](https://github.com/fab2s/Enumerate/actions/workflows/ci.yml) [![codecov](https://codecov.io/gh/fab2s/Enumerate/graph/badge.svg?token=M4PZ6Z6MqU)](https://codecov.io/gh/fab2s/Enumerate) ![Packagist Version](https://img.shields.io/packagist/v/fab2s/enumerate)
 [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com) [![License](http://poser.pugx.org/fab2s/Enumerate/license)](https://packagist.org/packages/fab2s/Enumerate)

`Enumerate` gives a nice boost to your [native enums](https://www.php.net/manual/en/language.types.enumerations.php).

## Why ?

`PHP Enums` are a wonderful addition to PHP, but they miss few things to make them fully practical in real world. If you dig, you will find that most of the current limitations seems to deal more with ideology than pragmatism.

As a software craftsmanship in spirit, I think that a feature like that should carry more complexity internally to bring more simplicity externally. 

For example, I just don't understand why `enums` are not, and worst, cannot be `stringable`. It's ok if you think that an `Unitenum` should not be `stringable`, or if you would be hurt to think that it would be treason to even imagine to `(string)` an `IntBackedEnum`, but these are all `SHOULDs` that just limit the way we can use them in real life situations when they become a `MUST`.

In practice, even `string` casting an `IntBackedEnum` could make sens in `HTTP` context where string is the only type, or when writing in a database. Yes these would be shortcuts, but why should we be forced to follow every detour ? 

Of course, I would prefer an interface that would allow us to `scalar` cast objects and end up with an `int` or `string` for enums, but this is a dream :-)

Unfortunately, we currently cannot have the freedom to embed such behaviors in our enums.

I am all in for strict types, but I find it cumbersome to have to write code just to be able to read an HTTP request or write in a database while it could all have been implemented once and for all in a way that would work perfectly fine.

Another thing that I don't understand is why can't we extend enums. There are so many practical cases where we would love to stay `DRY` in the way we can for example describe `roles` or even `types` in our applications. That in itself is an example where the engine could handle some added complexity, that is to inspect inheritance to maintain consistency and disallow case values to be updated, in order to provide with dryness and more _freedom_ to be creative.

Anyway, `enums` are great, and this package aims at making them just a little more usable.

It provides with a unified way to instantiate and use them.

This package is implemented as a static helper class `Enumerate`, that can be used separately to manipulate any enum in a standardised way (instantiation, json serialization ...), a trait, `EnumerateTrait`, that can be used in your enum to make them easier to deal with and an interface, `EnumerateInterface` that extends `JsonSerializable` and can be useful to `instanceof` your `enums`.

## Installation

`Enumerate` can be installed using composer:

```shell
composer require "fab2s/enumerate"
```

## Usage

To boost **any** PHP enum, use the `EnumerateTrait` trait in your enums:

```php
use fab2s\Enumerate\EnumerateTrait;

enum MyEnum // :string or : int or nothing
{
    use EnumerateTrait;
    // ...
```

`Enumerate` implements `jsonSerialize()` through `EnumerateInterface`, but, and this is another questionable matter with `PHP Traits`, you will have to declare that your `enum` implements the `JsonSerializable` or `EnumerateInterface` interface as traits currently cannot:

```php
use fab2s\Enumerate\EnumerateTrait;
use fab2s\Enumerate\EnumerateInterface;

enum MyEnum /* :string or : int or nothing */ implements EnumerateInterface // or JsonSerializable
{
    use EnumerateTrait;
    // ...
```

## So what ?

From there your enums benefits from most `Enumerate` helper methods, to the exception to the type resolution methods.

### `BackedEnum::tryFrom`

Current state is that both `IntBackedEnum` and `StringBackedEnum` are `BackedEnum`, but they don't agree on the types we are allowed to try.

This result in a situation where trying is actually only allowed on a single type (`string` or `int`) where the whole concept of trying seems a lot broader in itself. I mean, why shouldn't we be able to try `null` or even an enum `instance` ? 

There is nothing wrong with _trying_ as long as the result is consistent. In practice this will often result in having to implement more checks before we can even try anything.

`Enumerate` solves this by adding the `tryFromAny` method:

```php
// in EnumerateTrait /  EnumerateInterface
    /**
     * @throws ReflectionException
     */
    public static function tryFromAny(int|string|UnitEnum|null $value, bool $strict = true): ?static

// in Enumerate
    /**
     * @param UnitEnum|class-string<UnitEnum|BackedEnum> $enum
     *
     * @throws ReflectionException
     */
    public static function tryFromAny(UnitEnum|string $enum, int|string|UnitEnum|null $value, bool $strict = true): UnitEnum|BackedEnum|null
```

So now you can try for more types in a way that is just more practical without breaking the consistency of the answer.

Trying a `null` will be `null`, trying an `instance` will give the `instance` itself when it makes sens, trying an `int` on a `StringBackedEnum` will be `null` and so will a string on an `IntBackedEnum`. Doing this does not break anything, it just handles internally the complexity you would have to otherwise handle externally.

Nothing fancy, just usability.

`Enumerate` can go a little further if you decide to drop _strictness_ (as in, you are _free_ to do it or not):
```php
// will always be null
$result = MyEnum::tryFromAny(AnotherEnumWithOverlappingCases::someCase); 
// same as 
$result = Enumerate::tryFromAny(MyEnum::class, AnotherEnumWithOverlappingCases::someCase); 

// can return MyEnum::someCase if the case exist in MyEnum
// matched by value or case name for Unitenum's
$result = MyEnum::tryFromAny(AnotherEnumWithOverlappingCases::someCase, false);
// same as, works with enum FQN and instances
$result = Enumerate::tryFromAny(MyEnum::anyCase, AnotherEnumWithOverlappingCases::someCase, false);

```

### `BackedEnum::from`

Likewise, `Enumerate` provides with `fromAny` that do the same as the native `from` method but throws an `InvalidArgumentException` instead of returning `null` when no instance can be created from input.

Like with `tryFromAny`, you can reduce _strictness_:
```php
// throws an InvalidArgumentException if someCase is not present in MyEnum
// either by value for BackedEnum or case name for Unitenum
$result = MyEnum::fromAny('someCase');
// same as
$result = Enumerate::fromAny(MyEnum::class, 'someCase');


// can return MyEnum::someCase if the case exist in MyEnum
$result = MyEnum::fromAny(AnotherEnumWithOverlappingCases::someCase, false);
// same as
$result = Enumerate::fromAny(MyEnum::class, AnotherEnumWithOverlappingCases::someCase, false);

```

### _Merely_ `Stringable`

`Enumerate` adds a `toValue` method being the closest you can get to `stringable` so far. 
Types are respected, to `toValue` will return:
    - the int value for `IntBackedEnum` 
    - the string value for `StringBackedEnum`
    - the string case name for `Unitenum`

And all these value are valid input to create an instance using `tryFromAny` / `fromAny`.

```php
// either someCase name for UnitEnum or someCase value for BackedEnum
$result = MyEnum::someCase->toValue();
// same as
$result = Enumerate::toValue(MyEnum::someCase);

```

### `UnitEnum`

`Enumerate` treats `UnitEnum` as any `Enum`, using a match by _case name_ logic instead of the default and builtin _case value_ matching.

This is done internally using the `fromName` / `tryFromName` methods in a way that completely unifies enum types.

Doing so is of course a bit slower than value matching as we have to iterate through cases, but it could come handy in case where `UnitEnum` are present in existing code.

No need to say that doing this makes it possible to store and transfer `UnitEnum` with ease.

```php
use fab2s\Enumerate\Enumerate;
use fab2s\Enumerate\EnumerateTrait;
use fab2s\Enumerate\EnumerateInterface;

enum SomeUnitEnum implements EnumerateInterface
{
    use EnumerateTrait;

    case ONE;
    case TWO;
    case three;
}

SomeUnitEnum::tryFromName('ONE'); // SomeUnitEnum::ONE
// same as
Enumerate::tryFromName(SomeUnitEnum::class, 'ONE'); // SomeUnitEnum::ONE

// the toValue method is the nearest we can get to stringable
SomeUnitEnum::ONE->tovalue(); // "ONE"
// same as
SomeUnitEnum::ONE->jsonSerialize(); // "ONE"
// same as, except it will take nulls in
Enumerate::toValue(SomeUnitEnum::ONE); // "ONE"
// same as 
json_encode(SomeUnitEnum::ONE); // "ONE"


SomeUnitEnum::fromAny('TWO'); // SomeUnitEnum::TWO
SomeUnitEnum::tryFromAny('TWO'); // SomeUnitEnum::TWO
SomeUnitEnum::tryFromAny(UnitEnum::TWO); // SomeUnitEnum::TWO
```

### `BackedEnum`

`IntBackedEnum` and `StringBackedEnum` works the same.

````php
use fab2s\Enumerate\EnumerateTrait;

enum SomeIntBackedEnum: int implements JsonSerializable
{
    use EnumerateTrait;

    case ONE   = 1;
    case TWO   = 2;
    case three = 3;
}

SomeIntBackedEnum::tryFromAny(1); // SomeIntBackedEnum::ONE
SomeIntBackedEnum::tryFromAny('1'); // null
SomeIntBackedEnum::tryFromAny('ONE'); // null
SomeIntBackedEnum::tryFromName('ONE'); // SomeIntBackedEnum::ONE
````

### Comparing enums

`Enumerate` comes with two methods to assert if some input matches a case: `equals` and `compares`.

The `equals` methods is strict and will only return true if at least one of the argument can be turned into an enum case while `compares` will do the same even if the only match comes from a compatible `enum` instance (read another `enum` that overlaps one of the current `enum` case).

Again, `UnitEnum` are matched by `case name` which seams perfectly reasonable.

```php
// true when someCaseValue is the value of someCase for BackedEnum
// false for UnitEnum, would be true with someCase
MyEnum::someCase->equals('someCaseValue'); 
// same as 
Enumerate::equals(MyEnum::someCase, 'someCaseValue')
// always true
MyEnum::someCase->equals(MyEnum::someCase, null, 'whatever' /*, ...*/); 
// same as 
Enumerate::equals(MyEnum::someCase, MyEnum::someCase, null, 'whatever' /*, ...*/)

// true if we have an equality by value for BackedEnum
// or by name for UnitEnum
MyEnum:::someCase->compares(AnotherEnumWithOverlappingCases::someCase); 
// same as 
Enumerate::compares(MyEnum:::someCase, AnotherEnumWithOverlappingCases::someCase); 
```

## Requirements

`Enumerate` is tested against php 8.1, 8.2 and 8.3

## Contributing

Contributions are welcome, do not hesitate to open issues and submit pull requests.

## License

`Enumerate` is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
