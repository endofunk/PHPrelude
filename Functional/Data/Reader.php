<?php

namespace Endofunk\Data;

use Endofunk\Core\Module;
use Endofunk\Lib\Lambda;
use Endofunk\Lib\Strings;
use Endofunk\Typeclass\IMonad;

/**
 * Reader Monad
 *
 * The Reader monad (also called the Environment monad).
 * Represents a computation, which can read values from a shared environment, pass values from
 * function to function, and execute sub-computations in a modified environment.
 *
 * Using Reader monad for such computations is often clearer and easier than using the State monad.
 */
class Reader extends Module implements IMonad {
	private $runReader;

	private function __construct(callable $runReader) {
		$this->runReader = $runReader;
	}

	protected static function __of(callable $runReader): Reader {
		return new Reader($runReader);
	}

	public function run($env) {
		return ($this->runReader)($env);
	}

	public function map(callable $fn): Reader {
		return Reader::of(Lambda::compose($fn, $this->runReader));
	}

	protected static function __pure($a): Reader {
		return self::of(Lambda::k($a));
	}

	public function apply(Reader $a): Reader {
		return $this->flatmap(function ($fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	public function fapply(Reader $a): Reader {
		return $this->flatmap(function (callable $fn) use ($a) {
			return $a->map(function ($ma) use ($fn) {
				return $fn($ma);
			});
		});
	}

	protected static function __liftA1(callable $fn, Reader $a): Reader {
		return Reader::fmap(Module::curry($fn), $a);
	}

	protected static function __liftA2(callable $fn, Reader $a, Reader $b): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b);
	}

	protected static function __liftA3(callable $fn, Reader $a, Reader $b, Reader $c): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c);
	}

	protected static function __liftA4(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d);
	}

	protected static function __liftA5(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e);
	}

	protected static function __liftA6(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f);
	}

	protected static function __liftA7(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f, Reader $g): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g);
	}

	protected static function __liftA8(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f, Reader $g, Reader $h): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h);
	}

	protected static function __liftA9(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f, Reader $g, Reader $h, Reader $i): Reader {
		return Reader::fmap(Module::curry($fn), $a)->fapply($b)->fapply($c)->fapply($d)->fapply($e)->fapply($f)->fapply($g)->fapply($h)->fapply($i);
	}

	public function flatmap(callable $fn): Reader {
		return Reader::of(function ($env) use ($fn) {
			return $fn($this->runReader($env))->runReader($env);
		});
	}

	protected static function __fflatmap(callable $fn): callable {
		return function (Reader $a) use ($fn) {
			return $a->flatmap($fn);
		};
	}

	public function bind(callable $fn): Reader {
		return $this->flatmap($fn);
	}

	// Kleisli Composition
	protected static function __compose(callable $f, callable $g): callable {
		return Lambda::compose($f, Reader::fflatmap($g));
	}

	protected static function __liftM1(callable $fn, Reader $a): Reader {
		return $a->flatmap(function ($ma) {
			return Reader::pure($fn($ma));
		});
	}

	protected static function __liftM2(callable $fn, Reader $a, Reader $b): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return Reader::pure($fn($ma, $mb));
			});
		});
	}

	protected static function __liftM3(callable $fn, Reader $a, Reader $b, Reader $c): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return Reader::pure($fn($ma, $mb, $mc));
				});
			});
		});
	}

	protected static function __liftM4(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return Reader::pure($fn($ma, $mb, $mc, $md));
					});
				});
			});
		});
	}

	protected static function __liftM5(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return Reader::pure($fn($ma, $mb, $mc, $md, $me));
						});
					});
				});
			});
		});
	}

	protected static function __liftM6(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return Reader::pure($fn($ma, $mb, $mc, $md, $me, $mf));
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM7(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f, Reader $g): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return Reader::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg));
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM8(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f, Reader $g, Reader $h): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return Reader::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh));
									});
								});
							});
						});
					});
				});
			});
		});
	}

	protected static function __liftM9(callable $fn, Reader $a, Reader $b, Reader $c, Reader $d, Reader $e, Reader $f, Reader $g, Reader $h, Reader $i): Reader {
		return $a->flatmap(function ($ma) {
			return $b->flatmap(function ($mb) use ($ma) {
				return $c->flatmap(function ($mc) use ($ma, $mb) {
					return $d->flatmap(function ($md) use ($ma, $mb, $mc) {
						return $e->flatmap(function ($me) use ($ma, $mb, $mc, $md) {
							return $f->flatmap(function ($mf) use ($ma, $mb, $mc, $md, $me) {
								return $g->flatmap(function ($mg) use ($ma, $mb, $mc, $md, $me, $mf) {
									return $h->flatmap(function ($mh) use ($ma, $mb, $mc, $md, $me, $mf, $mg) {
										return $i->flatmap(function ($mi) use ($ma, $mb, $mc, $md, $me, $mf, $mg, $mh) {
											return Reader::pure($fn($ma, $mb, $mc, $md, $me, $mf, $mg, $mh, $mi));
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

	public function __toString(): string {
		return Strings::log("Reader::runReader", $this->runReader);
	}
}

?>