<?php declare(strict_types = 1);

namespace PHPStan\Rules\Methods;

use PHPStan\Rules\FunctionCallParametersCheck;
use PHPStan\Rules\RuleLevelHelper;

class CallStaticMethodsRuleTest extends \PHPStan\Rules\AbstractRuleTest
{

	/**
	 * @var bool
	 */
	private $checkThisOnly;

	protected function getRule(): \PHPStan\Rules\Rule
	{
		$broker = $this->createBroker();
		return new CallStaticMethodsRule(
			$broker,
			new FunctionCallParametersCheck($broker, new RuleLevelHelper(true), true, true),
			$this->checkThisOnly
		);
	}

	public function testCallStaticMethods()
	{
		$this->checkThisOnly = false;
		$this->analyse([__DIR__ . '/data/call-static-methods.php'], [
			[
				'Call to an undefined static method CallStaticMethods\Foo::bar().',
				39,
			],
			[
				'Call to an undefined static method CallStaticMethods\Bar::bar().',
				40,
			],
			[
				'Call to an undefined static method CallStaticMethods\Foo::bar().',
				41,
			],
			[
				'Static call to instance method CallStaticMethods\Foo::loremIpsum().',
				42,
			],
			[
				'Call to private static method dolor() of class CallStaticMethods\Foo.',
				43,
			],
			[
				'CallStaticMethods\Ipsum::ipsumTest() calls parent::lorem() but CallStaticMethods\Ipsum does not extend any class.',
				63,
			],
			[
				'Static method CallStaticMethods\Foo::test() invoked with 1 parameter, 0 required.',
				65,
			],
			[
				'Call to protected static method baz() of class CallStaticMethods\Foo.',
				66,
			],
			[
				'Call to static method loremIpsum() on an unknown class CallStaticMethods\UnknownStaticMethodClass.',
				67,
			],
			[
				'Parent constructor invoked with 0 parameters, 1 required.',
				87,
			],
			[
				'Calling self::someStaticMethod() outside of class scope.',
				93,
			],
			[
				'Calling static::someStaticMethod() outside of class scope.',
				94,
			],
			[
				'Calling parent::someStaticMethod() outside of class scope.',
				95,
			],
			[
				'Call to protected static method baz() of class CallStaticMethods\Foo.',
				97,
			],
			[
				'Call to an undefined static method CallStaticMethods\Foo::bar().',
				98,
			],
			[
				'Static call to instance method CallStaticMethods\Foo::loremIpsum().',
				99,
			],
			[
				'Call to private static method dolor() of class CallStaticMethods\Foo.',
				100,
			],
			[
				'Static method Locale::getDisplayLanguage() invoked with 3 parameters, 1-2 required.',
				104,
			],
		]);
	}

	public function testCallInterfaceMethods()
	{
		$this->checkThisOnly = false;
		$this->analyse([__DIR__ . '/data/call-interface-methods.php'], [
			[
				'Call to an undefined static method InterfaceMethods\Baz::barStaticMethod().',
				27,
			],
		]);
	}

	public function testCallToIncorrectCaseMethodName()
	{
		$this->checkThisOnly = false;
		$this->analyse([__DIR__ . '/data/incorrect-static-method-case.php'], [
			[
				'Call to static method IncorrectStaticMethodCase\Foo::fooBar() with incorrect case: foobar',
				10,
			],
		]);
	}

	public function testStaticCallsToInstanceMethods()
	{
		$this->checkThisOnly = false;
		$this->analyse([__DIR__ . '/data/static-calls-to-instance-methods.php'], [
			[
				'Static call to instance method StaticCallsToInstanceMethods\Foo::doFoo().',
				10,
			],
			[
				'Static call to instance method StaticCallsToInstanceMethods\Bar::doBar().',
				16,
			],
			[
				'Static call to instance method StaticCallsToInstanceMethods\Foo::doFoo().',
				36,
			],
			[
				'Call to method StaticCallsToInstanceMethods\Foo::doFoo() with incorrect case: dofoo',
				42,
			],
			[
				'Method StaticCallsToInstanceMethods\Foo::doFoo() invoked with 1 parameter, 0 required.',
				43,
			],
			[
				'Call to private method doPrivateFoo() of class StaticCallsToInstanceMethods\Foo.',
				45,
			],
			[
				'Method StaticCallsToInstanceMethods\Foo::doFoo() invoked with 1 parameter, 0 required.',
				48,
			],
		]);
	}

	public function testStaticCallOnExpression()
	{
		$this->checkThisOnly = false;
		$this->analyse([__DIR__ . '/data/static-call-on-expression.php'], [
			[
				'Call to an undefined static method StaticCallOnExpression\Foo::doBar().',
				16,
			],
		]);
	}

	public function testStaticCallOnExpressionWithCheckDisabled()
	{
		$this->checkThisOnly = true;
		$this->analyse([__DIR__ . '/data/static-call-on-expression.php'], []);
	}

	public function testReturnStatic()
	{
		$this->checkThisOnly = false;
		$this->analyse([__DIR__ . '/data/return-static-static-method.php'], [
			[
				'Call to an undefined static method ReturnStaticStaticMethod\Bar::doBaz().',
				24,
			],
		]);
	}

}
