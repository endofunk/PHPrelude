<?php

namespace Endofunk\Typeclass;

/**
 * Interface IFunctor
 */
interface IFunctor {
	// map :: Functor f => (a -> b) -> f a -> f b
	public function map(callable $f);
}
