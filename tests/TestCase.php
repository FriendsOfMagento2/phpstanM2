<?php declare(strict_types = 1);

namespace PHPStan;

use Nette\DI\Container;
use PHPStan\Broker\Broker;
use PHPStan\Cache\Cache;
use PHPStan\Cache\MemoryCacheStorage;
use PHPStan\File\FileHelper;
use PHPStan\Parser\DirectParser;
use PHPStan\Parser\FunctionCallStatementFinder;
use PHPStan\Parser\Parser;
use PHPStan\Reflection\Annotations\AnnotationsPropertiesClassReflectionExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\FunctionReflectionFactory;
use PHPStan\Reflection\Php\PhpClassReflectionExtension;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\Php\PhpMethodReflectionFactory;
use PHPStan\Reflection\Php\UniversalObjectCratesClassReflectionExtension;
use PHPStan\Reflection\PhpDefect\PhpDefectClassReflectionExtension;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\Type;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{

	/**
	 * @var \Nette\DI\Container
	 */
	private static $container;

	/** @var \PHPStan\Parser\Parser */
	private $parser;

	/**
	 * @var \PHPStan\File\FileHelper
	 */
	private $fileHelper;

	public function getContainer(): \Nette\DI\Container
	{
		return self::$container;
	}

	public static function setContainer(Container $container)
	{
		self::$container = $container;
	}

	public function getParser(): \PHPStan\Parser\Parser
	{
		if ($this->parser === null) {
			$traverser = new \PhpParser\NodeTraverser();
			$traverser->addVisitor(new \PhpParser\NodeVisitor\NameResolver());
			$this->parser = new DirectParser(new \PhpParser\Parser\Php7(new \PhpParser\Lexer()), $traverser);
		}

		return $this->parser;
	}

	/**
	 * @param \PHPStan\Type\DynamicMethodReturnTypeExtension[] $dynamicMethodReturnTypeExtensions
	 * @param \PHPStan\Type\DynamicStaticMethodReturnTypeExtension[] $dynamicStaticMethodReturnTypeExtensions
	 * @return \PHPStan\Broker\Broker
	 */
	public function createBroker(array $dynamicMethodReturnTypeExtensions = [], array $dynamicStaticMethodReturnTypeExtensions = []): Broker
	{
		$functionCallStatementFinder = new FunctionCallStatementFinder();
		$parser = $this->getParser();
		$cache = new Cache(new MemoryCacheStorage());
		$methodReflectionFactory = new class($parser, $functionCallStatementFinder, $cache) implements PhpMethodReflectionFactory {
			/** @var \PHPStan\Parser\Parser */
			private $parser;

			/** @var \PHPStan\Parser\FunctionCallStatementFinder */
			private $functionCallStatementFinder;

			/** @var \PHPStan\Cache\Cache */
			private $cache;

			/** @var \PHPStan\Broker\Broker */
			public $broker;

			public function __construct(
				Parser $parser,
				FunctionCallStatementFinder $functionCallStatementFinder,
				Cache $cache
			)
			{
				$this->parser = $parser;
				$this->functionCallStatementFinder = $functionCallStatementFinder;
				$this->cache = $cache;
			}

			public function create(
				ClassReflection $declaringClass,
				\ReflectionMethod $reflection,
				array $phpDocParameterTypes,
				Type $phpDocReturnType = null
			): PhpMethodReflection
			{
				return new PhpMethodReflection(
					$declaringClass,
					$reflection,
					$this->broker,
					$this->parser,
					$this->functionCallStatementFinder,
					$this->cache,
					$phpDocParameterTypes,
					$phpDocReturnType
				);
			}
		};
		$fileTypeMapper = new FileTypeMapper($parser, $this->createMock(Cache::class));
		$phpExtension = new PhpClassReflectionExtension($methodReflectionFactory, $fileTypeMapper);
		$functionReflectionFactory = new class($this->getParser(), $functionCallStatementFinder, $cache) implements FunctionReflectionFactory {
			/** @var \PHPStan\Parser\Parser */
			private $parser;

			/** @var \PHPStan\Parser\FunctionCallStatementFinder */
			private $functionCallStatementFinder;

			/** @var \PHPStan\Cache\Cache */
			private $cache;

			public function __construct(
				Parser $parser,
				FunctionCallStatementFinder $functionCallStatementFinder,
				Cache $cache
			)
			{
				$this->parser = $parser;
				$this->functionCallStatementFinder = $functionCallStatementFinder;
				$this->cache = $cache;
			}

			public function create(
				\ReflectionFunction $function,
				array $phpDocParameterTypes,
				Type $phpDocReturnType = null
			): FunctionReflection
			{
				return new FunctionReflection(
					$function,
					$this->parser,
					$this->functionCallStatementFinder,
					$this->cache,
					$phpDocParameterTypes,
					$phpDocReturnType
				);
			}
		};
		$broker = new Broker(
			[
				$phpExtension,
				new AnnotationsPropertiesClassReflectionExtension($fileTypeMapper),
				new UniversalObjectCratesClassReflectionExtension([\stdClass::class]),
				new PhpDefectClassReflectionExtension(),
			],
			[$phpExtension],
			$dynamicMethodReturnTypeExtensions,
			$dynamicStaticMethodReturnTypeExtensions,
			$functionReflectionFactory,
			new FileTypeMapper($this->getParser(), $this->createMock(Cache::class))
		);
		$methodReflectionFactory->broker = $broker;

		return $broker;
	}

	public function getFileHelper(): FileHelper
	{
		if ($this->fileHelper === null) {
			$this->fileHelper = $this->getContainer()->getByType(FileHelper::class);
		}

		return $this->fileHelper;
	}

}
