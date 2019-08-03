<?php

namespace Endofunk\Typeclass;

/**
 * Interface IMonad
 */
interface IMonad extends IApplicative {
	// flatmap :: Monad m => (a -> m b) -> m a -> m b
	public function flatmap(Callable $f);
}
