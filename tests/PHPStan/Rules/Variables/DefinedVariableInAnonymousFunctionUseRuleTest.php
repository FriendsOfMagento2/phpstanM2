<?php declare(strict_types = 1);

namespace PHPStan\Rules\Variables;

class DefinedVariableInAnonymousFunctionUseRuleTest extends \PHPStan\Rules\AbstractRuleTest
{

	/**
	 * @var bool
	 */
	private $checkMaybeUndefinedVariables;

	protected function getRule(): \PHPStan\Rules\Rule
	{
		return new DefinedVariableInAnonymousFunctionUseRule($this->checkMaybeUndefinedVariables);
	}

	public function testDefinedVariables()
	{
		$this->checkMaybeUndefinedVariables = true;
		$this->analyse([__DIR__ . '/data/defined-variables-anonymous-function-use.php'], [
			[
				'Undefined variable: $bar',
				5,
			],
			[
				'Undefined variable: $wrongErrorHandler',
				22,
			],
			[
				'Variable $onlyInIf might not be defined.',
				23,
			],
			[
				'Variable $forI might not be defined.',
				24,
			],
			[
				'Undefined variable: $forJ',
				25,
			],
		]);
	}

}
