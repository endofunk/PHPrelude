<?php

namespace Endofunk\Data;

use Endofunk\Core\Module;
use Endofunk\Lib\Lambda;
use Endofunk\Lib\Strings;
use Endofunk\Typeclass\IMonad;
use Exception;

/**
 * Result Monad
 *
 * A monad that represents success and failure conditions.
 * In a series of bind operations, if any function returns a failure monad, the following binds are skipped.
 * This allows for easy flow control for both success and failure cases.
 */
class Result extends Module implements IMonad {
	private $value;
	private $hasValue;

	private function __construct($value, $hasValue) {
		$this->value = $value;
		$this->hasValue = $hasValue;
	}

	protected static function __failure($value): Result {
		return new Result($value, false);
	}

	protected static function __success($value): Result {
		return new Result($value, true);
	}

	protected static function __try(callable $fn): Result {
		try {
			return Result::success($fn());
		} catch (Exception $error) {
			return Result::failure("Error Message: " . $error->getMessage() . "\nException code: " . $error->getCode() . "\nLine No: " . $error->getLine() . "\nFilename: " . $error->getFile());
		}
	}

	public function map(callable $fn): Result {
		return $this->hasValue ? self::success($fn($this->value)) : $this;
	}

	protected static function __fmap(callable $fn, Result $a): Result {
		return $a->map($fn);
	}

	protected static function __pure($a): Result {
		return self::success($a);
	}

	public function apply(Result $a): Result {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	public function fapply(Result $a): Result {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	protected static function __liftA1(callable $fn, Result $a): Result {
		return Result::fmap(Module::curry($fn), $a);
	}

	protected static function __liftA2(callable $fn, Result $a, Result $b): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b);
	}

	protected static function __liftA3(callable $fn, Result $a, Result $b, Result $c): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c);
	}

	protected static function __liftA4(callable $fn, Result $a, Result $b, Result $c, Result $d): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d);
	}

	protected static function __liftA5(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e);
	}

	protected static function __liftA6(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f);
	}

	protected static function __liftA7(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f, Result $g): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g);
	}

	protected static function __liftA8(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f, Result $g, Result $h): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h);
	}

	protected static function __liftA9(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f, Result $g, Result $h, Result $i): Result {
		return Result::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h)->fapply($i);
	}

	public function flatten(): Result {
		return ($this->value instanceof Result) ? $this->value : $this;
	}

	public function flatmap(callable $fn): Result {
		return $this->map($fn)->flatten();
	}

	protected static function __fflatmap(callable $fn): callable {
		return function (Result $a) use ($fn) {
			return $a->flatmap($fn);
		};
	}

	public function bind(callable $fn): Result {
		return $this->flatmap($fn);
	}

	// Kleisli Composition
	protected static function __compose(callable $f, callable $g): callable {
		return Lambda::compose($f, Result::fflatmap($g));
	}

	protected static function __liftM1(callable $fn, Result $a): Result {
		return $a->flatmap(function ($ma) {
			return Result::pure($fn($ma));
		});
	}

	protected static function __liftM2(callable $fn, Result $a, Result $b): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return Result::pure($fn($ma, $mb));
			});
		});
	}

	protected static function __liftM3(callable $fn, Result $a, Result $b, Result $c): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return Result::pure($fn($ma, $mb, $mc));
				});
			});
		});
	}

	protected static function __liftM4(callable $fn, Result $a, Result $b, Result $c, Result $d): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return Result::pure($fn($ma, $mb, $mc, $md));
					});
				});
			});
		});
	}

	protected static function __liftM5(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return Result::pure($fn($ma, $mb, $mc, $md, $me));
						});
					});
				});
			});
		});
	}

	protected static function __liftM6(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return Result::pure($fn($ma, $mb, $mc, $md, $me, $mf));
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM7(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f, Result $g): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return Result::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg));
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM8(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f, Result $g, Result $h): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return Result::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh));
									});
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM9(callable $fn, Result $a, Result $b, Result $c, Result $d, Result $e, Result $f, Result $g, Result $h, Result $i): Result {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return $i->flatmap(function ($mi) use ($ma, $mb, $mc, $md, $me, $mf, $mg, $mh) {
											return Result::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh, $mi));
										});
									});
								});
							});
						});
					});
				});
			});
		});
	}

	public function match(callable $success, callable $failure) {
		if ($this->hasValue()) {
			$success($this->getValue());
		} else {
			$failure($this->getValue());
		}
	}

	public function fold(callable $success, callable $failure) {
		if ($this->hasValue()) {
			return $success($this->getValue());
		} else {
			return $failure($this->getValue());
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function hasValue(): bool {
		return $this->hasValue;
	}

	public function __toString(): string {
		return Strings::log($this->hasValue ? "Result::success" : "Result::failure", $this->value);
	}
}

?>