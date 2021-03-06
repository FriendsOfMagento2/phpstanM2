<?php

if (foo()) {
	$ifVar = 1;
	$issetFoo = new Foo();
	$maybeDefinedButLaterCertainlyDefined = 1;
	if ($test) {
		$ifNestedVar = 1;
		$ifNotNestedVar = 1;
	} elseif (fooBar()) {
		$ifNotNestedVar = 2;
		$variableOnlyInEarlyTerminatingElse = 1;
		throw $e;
	} else {
		$ifNestedVar = 2;
	}
	$ifNotVar = 1;
} elseif (bar()) {
	$ifVar = 2;
	$issetFoo = null;
	$ifNestedVar = 2;
	$ifNotNestedVar = 2;
	$ifNotVar = 2;
} elseif ($ifNestedVar = baz()) {
	$ifVar = 3;
	$ifNotNestedVar = 3;
} else {
	$variableOnlyInEarlyTerminatingElse = 1;
	return;
}

if (foo()) {
	$maybeDefinedButLaterCertainlyDefined = 2;
} else {
	$maybeDefinedButLaterCertainlyDefined = 3;
}

try {
	$inTry = 1;
	$inTryNotInCatch = 1;
	$fooObjectFromTryCatch = new InTryCatchFoo();
	$mixedVarFromTryCatch = 1;
	$nullableIntegerFromTryCatch = 1;
	$anotherNullableIntegerFromTryCatch = null;
	$someVariableThatWillGetOverrideInFinally = 1;
} catch (\SomeConcreteException $e) {
	$inTry = 1;
	$fooObjectFromTryCatch = new InTryCatchFoo();
	$mixedVarFromTryCatch = 1.0;
	$nullableIntegerFromTryCatch = null;
	$anotherNullableIntegerFromTryCatch = 1;
} catch (\Exception $e) {
	throw $e;
} finally {
	$someVariableThatWillGetOverrideInFinally = 'foo';
	restore_error_handler();
}

$lorem = 1;
$arrOne[] = 'one';
$arrTwo['test'] = 'two';
$anotherArray['test'][] = 'another';
doSomething($one, $callParameter = 3);
$arrTwo[] = new Foo([
	$inArray = 1,
]);
$arrThree = null;
$arrThree[] = 'three';
preg_match('#.*#', 'foo', $matches);
if ((bool) preg_match('#.*#', 'foo', $matches3)) {
	foo();
} elseif (preg_match('#.*#', 'foo', $matches4)) {
	foo();
}
$someArray = [];
list($listedOne, , $listedTwo['two'], list($listedThree, $listedFour['four'])) = $someArray;

$trueOrFalseFromSwitch = true;
switch (foo()) {
	case 1:
		$switchVar = 1;
		$noSwitchVar = 1;
		$trueOrFalseFromSwitch = false;
		break;
	case 2:
		$switchVar = 2;
		break;
	case 3:
		$anotherNoSwitchVar = 1;
	case 4:
	default:
		$switchVar = 3;
}

$trueOrFalseInSwitchWithDefault = false;
$nullableTrueOrFalse = null;
switch ('foo') {
	case 'foo':
		$trueOrFalseInSwitchWithDefault = true;
		$nullableTrueOrFalse = true;
		break;
	case 'bar';
		$nullableTrueOrFalse = false;
		break;
	default:
		break;
}

$trueOrFalseInSwitchInAllCases = false;
switch ('foo') {
	case 'foo':
		$trueOrFalseInSwitchInAllCases = true;
		break;
	case 'bar';
		$trueOrFalseInSwitchInAllCases = true;
		break;
}
$trueOrFalseInSwitchInAllCasesWithDefault = false;
switch ('foo') {
	case 'foo':
		$trueOrFalseInSwitchInAllCasesWithDefault = true;
		break;
	case 'bar';
		$trueOrFalseInSwitchInAllCasesWithDefault = true;
		break;
	default:
		break;
}
$trueOrFalseInSwitchInAllCasesWithDefaultCase = false;
switch ('foo') {
	case 'foo':
		$trueOrFalseInSwitchInAllCasesWithDefaultCase = true;
		break;
	case 'bar';
		$trueOrFalseInSwitchInAllCasesWithDefaultCase = true;
		break;
	default:
		$trueOrFalseInSwitchInAllCasesWithDefaultCase = true;
		break;
}

switch ('foo') {
	case 'foo':
		$variableDefinedInSwitchWithOtherCasesWithEarlyTermination = true;
		break;
	case 'bar':
		throw new \Exception();
	default:
		throw new \Exception();
}

switch ('foo') {
	case 'foo':
		throw new \Exception();
	case 'bar':
		$anotherVariableDefinedInSwitchWithOtherCasesWithEarlyTermination = true;
		break;
	default:
		throw new \Exception();
}

switch ('foo') {
	case 'foo':
		$variableDefinedOnlyInEarlyTerminatingSwitchCases = true;
		throw new \Exception();
	case 'bar':
		$variableDefinedOnlyInEarlyTerminatingSwitchCases = true;
		return;
	default:
		$variableDefinedOnlyInEarlyTerminatingSwitchCases = true;
		return;
}

do {
	$doWhileVar = 1;
} while (something());

$integerOrNullFromFor = null;
for ($previousI = 0, $previousJ = 0; $previousI < 1; $previousI++) {
	$integerOrNullFromFor = 1;
	$nonexistentVariableOutsideFor = 1;
}

$integerOrNullFromWhile = null;
while (($frame = $that->getReader()->consumeFrame($that->getReadBuffer())) === null) {
	$integerOrNullFromWhile = 1;
	$nonexistentVariableOutsideWhile = 1;
}

$integerOrNullFromForeach = null;
foreach ($someArray as $someValue) {
	$integerOrNullFromForeach = 1;
	$nonexistentVariableOutsideForeach = null;
}

$nullableIntegers = [1, 2, 3];
$nullableIntegers[] = null;

$union = [1, 2, 3];
$union[] = 'foo';

$$lorem = 'ipsum';

$trueOrFalse = true;
$falseOrTrue = false;
$true = true;
$false = false;
if (doFoo()) {
	$trueOrFalse = false;
	$falseOrTrue = true;
	$true = true;
	$false = false;
}

/** @var string|null $notNullableString */
$notNullableString = 'foo';
if ($notNullableString === null) {
	return;
}

/** @var string|null $anotherNotNullableString */
$anotherNotNullableString = 'foo';
if ($anotherNotNullableString !== null) {
	$alsoNotNullableString = $anotherNotNullableString;
} else {
	return;
}

/** @var Foo|null $notNullableObject */
$notNullableObject = doFoo();
if ($notNullableObject === null) {
	$notNullableObject = new Foo();
}

/** @var string|null $nullableString */
$nullableString = 'foo';
if ($nullableString !== null) {
	$whatever = $nullableString;
}

$arrayOfIntegers = [1, 2, 3];

$arrayAccessObject = new \ObjectWithArrayAccess\Foo();
$arrayAccessObject[] = 1;
$arrayAccessObject[] = 2;

$width = 1;
$scale = 2.0;
$width *= $scale;

try {
	$inTryTwo = 1;
} catch (\Exception $e) {
	$exception = $e;
	if (something()) {
		bar();
	} elseif (foo() || $foo = exists() || preg_match('#.*#', $subject, $matches2) || isset($issetFoo, $issetBar)) {
		$anotherF = 1;
		for ($i = 0; $i < 5; $i++, $f = $i, $anotherF = $i) {
			$arr = [
				[1, 2],
			];
			foreach ($arr as list($listOne, $listTwo)) {
				if (is_array($arrayOfIntegers)) {
					(bool)preg_match('~.*~', $attributes, $ternaryMatches) ? die : null;
				}
			}
		}
	}
}
