[![Platform](https://img.shields.io/badge/Platforms-Windows%20%7C%20macOS%20%7C%20Linux-4E4E4E.svg?colorA=28a745)](#Platform-Support)

[![Twitter](https://img.shields.io/badge/Twitter-@codefunctor-blue.svg?style=flat)](http://twitter.com/codefunctor)

![](https://raw.githubusercontent.com/endofunk/Endofunk-FX/master/Images/endofunk.png)

# PHPrelude
Functional Programming Library for PHP

This library provides a functional-programming core library: `Endofunk`, that adds many of the data types needed to employ a functional programming style in your codebase; more data types will be added in due course.

# Functional Data Types
The following is a list of the functional data types included in `Endofunk`. 

| Type  | Overview |
|-------|----------|
| Identity | The Identity type is a trivial type to access functor, monad, applicative functor, etc. algebras. |
| Maybe | The Maybe type encapsulates an optional value. A value of type Maybe a either contains a value of type a (represented as Just a), or it is empty (represented as Nothing)|
| Either| The Either type encapsulates a logical disjunction of two possibilities: either Left or Right. |
| Result | The Result type is similar to the Either type except that the left disjunction is fixed to capture of a C# Exception. |
| Validation | The Validation data type is isomorphic to Either, but has an instance of Applicative that accumulates on the error side. |
| Reader | The Reader type (also called the Environment monad). Represents a computation, which can read values from a shared environment, pass values from function to function, and execute sub-computations in a modified environment. |

All types support `Functor`, `Applicative Functor` and `Monad`; with `monadic lifters`, `applicative lifters`, and `Kleisli monadic composition`.

# Functional Prelude
The core prelude libraries are for the most part a copy of ![the Vector](https://github.com/joseph-walker/vector) PHP FP Library developed by ![joseph-walker](https://github.com/joseph-walker). The code for `Vector` has been integrated into this framework with a few changes; meaning there is no dependency on `Vector` only `PHPrelude`.

# A few Examples:

## Currying
...is essentially free with all functions that have the `Module` extension.
```php
$addOne = Arrays::map(function($a) { return $a + 1; });
$addOne([1, 2, 3]); // [2, 3, 4]
```

## Composition
```php
$addSix = Lambda::compose(Math::add(4), Math::add(2));
$addSix(4); // 10;
```

```php
$f = Lambda::compose(Strings::toUppercase(), Strings::trim());
$v = Identity::of("hello ")
	->map($f)
 ->Match(function ($v) {
		echo Strings::log("Identity", $v);
});
 
// -------------------------------------------------------------- Identity ----
// string(5) "HELLO"
```

## Kleisli Composition
```php
$incrementM = function ($v) {
	return Identity::of($v + 1);
};

$squareM = function ($v) {
	return Identity::of($v * $v);
};

$incsquare = Identity::compose($incrementM, $squareM);

Identity::of(2)
	->bind($incsquare)
	->Match(function ($v) {
		echo Strings::log("Identity", $v);
	});
 
// -------------------------------------------------------------- Identity ----
// int(9)
```

## Applicative Functor - Lazy Application
```php
$g = function (string $name, string $surname): string {
	return $name . ' ' . $surname;
};
$gc = Module::curry($g);

Identity::of($gc)
	->fapply(Identity::of("Jack"))
	->fapply(Identity::of("Sprat"))
 ->Match(function ($v) {
		echo Strings::log("Identity", $v);
});

\\ ------------------------------------------------------- Identity ----
\\ string(10) "Jack Sprat"
```

## Applicative Functor - Lifter
LiftA(x) supports arities from 1 to 9.  
```php
$g = function (string $name, string $surname): string {
	return $name . ' ' . $surname;
};

Identity::liftA2($g, Identity::of("Jack"), Identity::of("Sprat")) // liftA(x) auto currys the function
 ->Match(function ($v) {
		echo Strings::log("Identity", $v);
});

\\ ------------------------------------------------------- Identity ----
\\ string(10) "Jack Sprat"
```

## Result Monad wrapping callable in Try/Catch
```php

class SQL extends Module {

	protected static function __connect(string $dsn, string $database, string $host, string $username, string $password): Result {
		return Result::try (function () use ($dsn, $database, $host, $username, $password) {
			return new PDO("$dsn:dbname=$database;host=$host", $username, $password);
		});
	}

	protected static function __query($sql): callable {
		return function ($conn) use ($sql) {
			return Result::try (function () use ($conn, $sql) {
				$result = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
				if (!$result) {
					throw new Exception($mysqli->error0, $severity, $file, $line);
				}
				return [$conn, $result];
			});
		};
	}

	protected static function __close(): callable {
		return function ($conn) {
			return Result::try (function () use ($conn) {
				$conn[0] = null;
				return $conn[1];
			});
		};
	}
}
```

### Usage example of Result based SQL PDO monadic functions
```php
SQL::connect("mysql", "sakila", "127.0.0.1", "username", "password")
	->bind(SQL::query("call sakila.SP_actors()"))
	->bind(SQL::close())
	->match(function ($value) {
		echo json_encode($value) . "\n";
	}, function ($error) {
		echo $error . "\n";
	});
 
// [{"actor_id":"1","first_name":"PENELOPE","last_name":"GUINESS","last_update":"2006-02-15 04:34:33"},
// ...
// ...
// {"actor_id":"200","first_name":"THORA","last_name":"TEMPLE","last_update":"2006-02-15 04:34:33"}]
```

### Usage example of Result based Exception trap
```php
$f = function () {
	throw new Exception("Value must be 1 or below");
	return 1;
};

Result::try ($f)
  ->Match(function ($v) {
		 echo Strings::log("Result", $v);
});

// ------------------------------------------------------- Result ----
// string(113) "Error Message: Value must be 1 or below
// Exception code: 0
// Line No: 59
// Filename: /Users/JackSprat/Documents/web/index.php"
```
