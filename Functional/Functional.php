<?php
// --- Core    ------------------------------------------------
include dirname(__FILE__) . "/Core/Module.php";

// --- Control ------------------------------------------------
// include dirname(__FILE__) . "/Control/Extractable.php";
include dirname(__FILE__) . "/Control/Pattern.php";

// --- Core/Exception -----------------------------------------
include dirname(__FILE__) . "/Core/Exception/ElementNotFoundException.php";
include dirname(__FILE__) . "/Core/Exception/EmptyListException.php";
include dirname(__FILE__) . "/Core/Exception/FunctionNotFoundException.php";
include dirname(__FILE__) . "/Core/Exception/IncompletePatternMatchException.php";
include dirname(__FILE__) . "/Core/Exception/IndexOutOfBoundsException.php";
include dirname(__FILE__) . "/Core/Exception/InvalidPatternMatchException.php";
include dirname(__FILE__) . "/Core/Exception/UndefinedPropertyException.php";

// --- Typeclass ----------------------------------------------
include dirname(__FILE__) . "/Typeclass/IFunctor.php";
include dirname(__FILE__) . "/Typeclass/IApplicative.php";
include dirname(__FILE__) . "/Typeclass/IMonad.php";

// --- Data    ------------------------------------------------
include dirname(__FILE__) . "/Data/Either.php";
include dirname(__FILE__) . "/Data/Identity.php";
include dirname(__FILE__) . "/Data/Maybe.php";
include dirname(__FILE__) . "/Data/Reader.php";
include dirname(__FILE__) . "/Data/Result.php";
include dirname(__FILE__) . "/Data/Validation.php";

// --- Lib     ------------------------------------------------
include dirname(__FILE__) . "/Lib/Arrays.php";
include dirname(__FILE__) . "/Lib/Lambda.php";
include dirname(__FILE__) . "/Lib/Logic.php";
include dirname(__FILE__) . "/Lib/Math.php";
include dirname(__FILE__) . "/Lib/Objects.php";
include dirname(__FILE__) . "/Lib/Strings.php";
include dirname(__FILE__) . "/Lib/SQL.php";

?>