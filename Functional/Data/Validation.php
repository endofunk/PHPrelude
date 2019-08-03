<?php

namespace Endofunk\Data;

use Endofunk\Core\Module;
use Endofunk\Lib\Lambda;
use Endofunk\Lib\Strings;
use Endofunk\Typeclass\IMonad;

/**
 * Validation Monad
 *
 * A Validation is either a value of the type left or right, similar to Either.
 * However, the Applicative instance for Validation accumulates errors using a Semigroup on err.
 *
 * In contrast, the Applicative for Either returns only the first error.
 */
class Validation extends Module implements IMonad {
	private $value;
	private $isRight;

	private function __construct($value, $isRight) {
		$this->value = $value;
		$this->isRight = $isRight;
	}

	protected static function __left($value): Validation {
		return new Validation($value, false);
	}

	protected static function __right($value): Validation {
		return new Validation($value, true);
	}

	public function map(callable $fn): Validation {
		return $this->isRight ? self::right($fn($this->value)) : $this;
	}

	protected static function __fmap(callable $fn, Result $a): Validation {
		return $a->map($fn);
	}

	protected static function __pure($a): Validation {
		return self::right($a);
	}

	public function apply(Validation $a): Validation {
		switch ([$a->isRight(), $this->isRight]) {
		case [true, true]:
			return $this->flatmap(function (callable $fn) use ($a) {
				return $a->map(function ($ma) use ($fn) {
					return $fn($ma);
				});
			});
		case [false, false]:
			if (is_array($a->getValue())) {
				array_push($a->getValue(), $this->value);
				return Validation::left($a->getValue());
			} else {
				$r = [$a->getValue()];
				array_push($r, $this->value);
				return Validation::left($r);
			}
		case [false, true]:
			return Validation::left($a->getValue());
		default:
			return $this;
		}
	}

	public function fapply(Validation $a): Validation {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	protected static function __liftA1(callable $fn, Validation $a): Validation {
		return Validation::fmap(Module::curry($fn), $a);
	}

	protected static function __liftA2(callable $fn, Validation $a, Validation $b): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b);
	}

	protected static function __liftA3(callable $fn, Validation $a, Validation $b, Validation $c): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c);
	}

	protected static function __liftA4(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d);
	}

	protected static function __liftA5(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e);
	}

	protected static function __liftA6(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f);
	}

	protected static function __liftA7(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f, Validation $g): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g);
	}

	protected static function __liftA8(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f, Validation $g, Validation $h): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h);
	}

	protected static function __liftA9(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f, Validation $g, Validation $h, Validation $i): Validation {
		return Validation::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h)->fapply($i);
	}

	public function flatten(): Validation {
		return ($this->value instanceof Validation) ? $this->value : $this;
	}

	public function flatmap(callable $fn): Validation {
		return $this->map($fn)->flatten();
	}

	protected static function __fflatmap(callable $fn): callable {
		return function (Validation $a) use ($fn) {
			return $a->flatmap($fn);
		};
	}

	// Kleisli Composition
	protected static function __compose(callable $f, callable $g): callable {
		return Lambda::compose($f, Validation::fflatmap($g));
	}

	public function bind(callable $fn): Validation {
		return $this->flatmap($fn);
	}

	protected static function __liftM1(callable $fn, Validation $a): Validation {
		return $a->flatmap(function ($ma) {
			return Validation::pure($fn($ma));
		});
	}

	protected static function __liftM2(callable $fn, Validation $a, Validation $b): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return Validation::pure($fn($ma, $mb));
			});
		});
	}

	protected static function __liftM3(callable $fn, Validation $a, Validation $b, Validation $c): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return Validation::pure($fn($ma, $mb, $mc));
				});
			});
		});
	}

	protected static function __liftM4(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return Validation::pure($fn($ma, $mb, $mc, $md));
					});
				});
			});
		});
	}

	protected static function __liftM5(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return Validation::pure($fn($ma, $mb, $mc, $md, $me));
						});
					});
				});
			});
		});
	}

	protected static function __liftM6(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return Validation::pure($fn($ma, $mb, $mc, $md, $me, $mf));
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM7(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f, Validation $g): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return Validation::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg));
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM8(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f, Validation $g, Validation $h): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return Validation::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh));
									});
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM9(callable $fn, Validation $a, Validation $b, Validation $c, Validation $d, Validation $e, Validation $f, Validation $g, Validation $h, Validation $i): Validation {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return $i->flatmap(function ($mi) use ($ma, $mb, $mc, $md, $me, $mf, $mg, $mh) {
											return Validation::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh, $mi));
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

	public function match(callable $right, callable $left) {
		if ($this->isRight()) {
			$right($this->getValue());
		} else {
			$left($this->getValue());
		}
	}

	public function fold(callable $right, callable $left) {
		if ($this->isRight()) {
			return $right($this->getValue());
		} else {
			return $left($this->getValue());
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function isRight(): bool {
		return $this->isRight;
	}

	public function __toString(): string {
		return Strings::log($this->isRight ? "Validation::right" : "Validation::left", $this->value);
	}
}
