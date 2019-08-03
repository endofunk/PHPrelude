<?php

namespace Endofunk\Data;

use Endofunk\Core\Module;
use Endofunk\Lib\Lambda;
use Endofunk\Lib\Strings;
use Endofunk\Typeclass\IMonad;

/**
 * Either Monad
 *
 * The Either type represents values with two possibilities: a value of type Either a b is either Left a or Right b.
 * The Either type is sometimes used to represent a value which is either correct or an error; by convention,
 * the Left constructor is used to hold an error value and the Right constructor is used to hold a correct value
 * (mnemonic: "right" also means "correct").
 */
class Either extends Module implements IMonad {
	private $value;
	private $isRight;

	private function __construct($value, $isRight) {
		$this->value = $value;
		$this->isRight = $isRight;
	}

	protected static function __left($value): Either {
		return new Either($value, false);
	}

	protected static function __right($value): Either {
		return new Either($value, true);
	}

	public function map(callable $fn): Either {
		return $this->isRight ? self::right($fn($this->value)) : $this;
	}

	protected static function __fmap(callable $fn, Result $a): Either {
		return $a->map($fn);
	}

	protected static function __pure($a): Either {
		return self::right($a);
	}

	public function apply(Either $a): Either {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	public function fapply(Either $a): Either {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	protected static function __liftA1(callable $fn, Either $a): Either {
		return Either::fmap(Module::curry($fn), $a);
	}

	protected static function __liftA2(callable $fn, Either $a, Either $b): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b);
	}

	protected static function __liftA3(callable $fn, Either $a, Either $b, Either $c): Result {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c);
	}

	protected static function __liftA4(callable $fn, Either $a, Either $b, Either $c, Either $d): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d);
	}

	protected static function __liftA5(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e);
	}

	protected static function __liftA6(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f);
	}

	protected static function __liftA7(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f, Either $g): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g);
	}

	protected static function __liftA8(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f, Either $g, Either $h): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h);
	}

	protected static function __liftA9(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f, Either $g, Either $h, Either $i): Either {
		return Either::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h)->fapply($i);
	}

	public function flatten(): Either {
		return ($this->value instanceof Either) ? $this->value : $this;
	}

	public function flatmap(callable $fn): Either {
		return $this->map($fn)->flatten();
	}

	protected static function __fflatmap(callable $fn): callable {
		return function (Either $a) use ($fn) {
			return $a->flatmap($fn);
		};
	}

	public function bind(callable $fn): Either {
		return $this->flatmap($fn);
	}

	// Kleisli Composition
	protected static function __compose(callable $f, callable $g): callable {
		return Lambda::compose($f, Either::fflatmap($g));
	}

	protected static function __liftM1(callable $fn, Either $a): Either {
		return $a->flatmap(function ($ma) {
			return Either::pure($fn($ma));
		});
	}

	protected static function __liftM2(callable $fn, Either $a, Either $b): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return Either::pure($fn($ma, $mb));
			});
		});
	}

	protected static function __liftM3(callable $fn, Either $a, Either $b, Either $c): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return Either::pure($fn($ma, $mb, $mc));
				});
			});
		});
	}

	protected static function __liftM4(callable $fn, Either $a, Either $b, Either $c, Either $d): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return Either::pure($fn($ma, $mb, $mc, $md));
					});
				});
			});
		});
	}

	protected static function __liftM5(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return Either::pure($fn($ma, $mb, $mc, $md, $me));
						});
					});
				});
			});
		});
	}

	protected static function __liftM6(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return Either::pure($fn($ma, $mb, $mc, $md, $me, $mf));
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM7(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f, Either $g): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return Either::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg));
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM8(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f, Either $g, Either $h): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return Either::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh));
									});
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM9(callable $fn, Either $a, Either $b, Either $c, Either $d, Either $e, Either $f, Either $g, Either $h, Either $i): Either {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return $i->flatmap(function ($mi) use ($ma, $mb, $mc, $md, $me, $mf, $mg, $mh) {
											return Either::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh, $mi));
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
		return Strings::log($this->isRight ? "Either::right" : "Either::left", $this->value);
	}
}

?>