<?php

namespace Endofunk\Data;

use Endofunk\Core\Module;
use Endofunk\Lib\Lambda;
use Endofunk\Lib\Strings;
use Endofunk\Typeclass\IMonad;

/**
 * Maybe Monad
 *
 * The Maybe type encapsulates an optional value.
 * A value of type Maybe a either contains a value of type a (represented as Just a), or it is empty (represented as Nothing).
 */
class Maybe extends Module implements IMonad {
	private $value;
	private $isJust;

	private function __construct($value, $isJust) {
		$this->value = $value;
		$this->isJust = $isJust;
	}

	protected static function __just($value): Maybe {
		return new Maybe($value, true);
	}

	protected static function __nothing(): Maybe {
		return new Maybe(null, false);
	}

	public function map(callable $fn): Maybe {
		return $this->isJust ? self::just($fn($this->value)) : $this;
	}

	protected static function __fmap(callable $fn, Result $a): Maybe {
		return $a->map($fn);
	}

	protected static function __pure($a): Maybe {
		return self::just($a);
	}

	public function apply(Maybe $a): Maybe {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	public function fapply(Maybe $a): Maybe {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	protected static function __liftA1(callable $fn, Maybe $a): Maybe {
		return Maybe::fmap(Module::curry($fn), $a);
	}

	protected static function __liftA2(callable $fn, Maybe $a, Maybe $b): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b);
	}

	protected static function __liftA3(callable $fn, Maybe $a, Maybe $b, Maybe $c): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c);
	}

	protected static function __liftA4(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d);
	}

	protected static function __liftA5(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e);
	}

	protected static function __liftA6(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f);
	}

	protected static function __liftA7(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f, Maybe $g): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g);
	}

	protected static function __liftA8(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f, Maybe $g, Maybe $h): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h);
	}

	protected static function __liftA9(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f, Maybe $g, Maybe $h, Maybe $i): Maybe {
		return Maybe::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h)->fapply($i);
	}

	public function flatten(): Maybe {
		return ($this->value instanceof Maybe) ? $this->value : $this;
	}

	public function flatmap(callable $fn): Maybe {
		return $this->map($fn)->flatten();
	}

	protected static function __fflatmap(callable $fn): callable {
		return function (Maybe $a) use ($fn) {
			return $a->flatmap($fn);
		};
	}

	public function bind(callable $fn): Maybe {
		return $this->flatmap($fn);
	}

	// Kleisli Composition
	protected static function __compose(callable $f, callable $g): callable {
		return Lambda::compose($f, Maybe::fflatmap($g));
	}

	protected static function __liftM1(callable $fn, Maybe $a): Maybe {
		return $a->flatmap(function ($ma) {
			return Maybe::pure($fn($ma));
		});
	}

	protected static function __liftM2(callable $fn, Maybe $a, Maybe $b): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return Maybe::pure($fn($ma, $mb));
			});
		});
	}

	protected static function __liftM3(callable $fn, Maybe $a, Maybe $b, Maybe $c): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return Maybe::pure($fn($ma, $mb, $mc));
				});
			});
		});
	}

	protected static function __liftM4(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return Maybe::pure($fn($ma, $mb, $mc, $md));
					});
				});
			});
		});
	}

	protected static function __liftM5(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return Maybe::pure($fn($ma, $mb, $mc, $md, $me));
						});
					});
				});
			});
		});
	}

	protected static function __liftM6(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return Maybe::pure($fn($ma, $mb, $mc, $md, $me, $mf));
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM7(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f, Maybe $g): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return Maybe::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg));
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM8(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f, Maybe $g, Maybe $h): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return Maybe::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh));
									});
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM9(callable $fn, Maybe $a, Maybe $b, Maybe $c, Maybe $d, Maybe $e, Maybe $f, Maybe $g, Maybe $h, Maybe $i): Maybe {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return $i->flatmap(function ($mi) use ($ma, $mb, $mc, $md, $me, $mf, $mg, $mh) {
											return Maybe::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh, $mi));
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

	public function match(callable $just, callable $nothing) {
		if ($this->isJust()) {
			$just($this->getValue());
		} else {
			$nothing();
		}
	}

	public function fold(callable $just, callable $nothing) {
		if ($this->isJust()) {
			return $just($this->getValue());
		} else {
			return $nothing();
		}
	}

	public function getValue() {
		return $this->value;
	}

	public function isJust(): bool {
		return $this->isJust;
	}

	public function __toString() {
		return Strings::log($this->isJust ? "Maybe::just" : "Maybe::nothing", $this->value);
	}
}
