<?php

namespace Endofunk\Data;

use Endofunk\Core\Module;
use Endofunk\Lib\Lambda;
use Endofunk\Lib\Strings;
use Endofunk\Typeclass\IMonad;

/**
 * Identity Monad
 *
 * The Identity monad is a monad that does not embody any computational strategy.
 * It simply applies the bound function to its input without any modification.
 * Computationally, there is no reason to use the Identity monad instead of the much simpler act of
 * simply applying functions to their arguments.
 *
 * The purpose of the Identity monad is its fundamental role in the theory of monad transformers.
 * Any monad transformer applied to the Identity monad yields a non-transformer version of that monad.
 */
class Identity extends Module implements IMonad {
	private $value;

	private function __construct($value) {
		$this->value = $value;
	}

	protected static function __of($a): Identity {
		return new Identity($a);
	}

	public function map(callable $fn): Identity {
		return self::of($fn($this->value));
	}

	protected static function __fmap(callable $fn, Identity $i): Identity {
		return $i->map($fn);
	}

	protected static function __pure($a): Identity {
		return self::of($a);
	}

	public function apply(Identity $f): Identity {
		return $this->flatmap(function ($ma) use ($f) {
			return $f->map(function (callable $fn) use ($ma) {
				return $fn($ma);
			});
		});
	}

	public function fapply(Identity $a): Identity {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	protected static function __liftA1(callable $fn, Identity $a): Identity {
		return Identity::fmap(Module::curry($fn), $a);
	}

	protected static function __liftA2(callable $fn, Identity $a, Identity $b): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b);
	}

	protected static function __liftA3(callable $fn, Identity $a, Identity $b, Identity $c): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c);
	}

	protected static function __liftA4(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d);
	}

	protected static function __liftA5(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e);
	}

	protected static function __liftA6(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f);
	}

	protected static function __liftA7(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f, Identity $g): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g);
	}

	protected static function __liftA8(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f, Identity $g, Identity $h): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h);
	}

	protected static function __liftA9(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f, Identity $g, Identity $h, Identity $i): Identity {
		return Identity::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h)->fapply($i);
	}

	public function flatten(): Identity {
		return ($this->value instanceof Identity) ? $this->value : $this;
	}

	public function flatmap(callable $fn): Identity {
		return $this->map($fn)->flatten();
	}

	protected static function __fflatmap(callable $fn): callable {
		return function (Identity $a) use ($fn) {
			return $a->flatmap($fn);
		};
	}

	public function bind(callable $fn): Identity {
		return $this->flatmap($fn);
	}

	// Kleisli Composition
	protected static function __compose(callable $f, callable $g): callable {
		return Lambda::compose($f, Identity::fflatmap($g));
	}

	protected static function __liftM1(callable $fn, Identity $a): Identity {
		return $a->flatmap(function ($ma) {
			return Identity::pure($fn($ma));
		});
	}

	protected static function __liftM2(callable $fn, Identity $a, Identity $b): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return Identity::pure($fn($ma, $mb));
			});
		});
	}

	protected static function __liftM3(callable $fn, Identity $a, Identity $b, Identity $c): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return Identity::pure($fn($ma, $mb, $mc));
				});
			});
		});
	}

	protected static function __liftM4(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return Identity::pure($fn($ma, $mb, $mc, $md));
					});
				});
			});
		});
	}

	protected static function __liftM5(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return Identity::pure($fn($ma, $mb, $mc, $md, $me));
						});
					});
				});
			});
		});
	}

	protected static function __liftM6(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return Identity::pure($fn($ma, $mb, $mc, $md, $me, $mf));
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM7(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f, Identity $g): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return Identity::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg));
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM8(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f, Identity $g, Identity $h): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return Identity::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh));
									});
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM9(callable $fn, Identity $a, Identity $b, Identity $c, Identity $d, Identity $e, Identity $f, Identity $g, Identity $h, Identity $i): Identity {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return $i->flatmap(function ($mi) use ($ma, $mb, $mc, $md, $me, $mf, $mg, $mh) {
											return Identity::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh, $mi));
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

	public function match(callable $fn) {
		$fn($this->getValue());
	}

	public function fold(callable $fn) {
		return $fn($this->getValue());
	}

	public function getValue() {
		return $this->value;
	}

	public function __toString(): string {
		return Strings::log("Identity::value", $this->value);
	}
}
