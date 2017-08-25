<?php declare(strict_types = 1);

namespace PHPStan\Parser;

use PhpParser\NodeTraverser;

class DirectParser implements Parser
{

	/**
	 * @var \PhpParser\Parser
	 */
	private $parser;

	/**
	 * @var \PhpParser\NodeTraverser
	 */
	private $traverser;

	public function __construct(\PhpParser\Parser $parser, NodeTraverser $traverser)
	{
		$this->parser = $parser;
		$this->traverser = $traverser;
	}

	/**
	 * @param string $file path to a file to parse
	 * @return \PhpParser\Node[]
	 */
	public function parseFile(string $file): array
	{
		return $this->parseString(file_get_contents($file));
	}

	/**
	 * @param string $sourceCode
	 * @return \PhpParser\Node[]
	 */
	public function parseString(string $sourceCode): array
	{
		$nodes = $this->parser->parse($sourceCode);
		if ($nodes === null) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		$autoAssignVariables = [
			'block',
			'this',
		];

		$nodes = $this->traverser->traverse($nodes);

		$addedNodes = [];
		foreach ($nodes as $node) {
			foreach ($node->getAttribute('comments', []) as $comment) {
				if (preg_match_all('/@var\s+([A-Za-z0-9\\\]+)\s+\$(' . implode('|', $autoAssignVariables) . ')/im', $comment->getText(), $matches)) {
					foreach ($matches[2] as $index => $variable) {
						if (!isset($addedNodes[$variable])) {
							$addedNodes[$variable] = true;
							$className = trim($matches[1][$index]);

							$assign = new \PhpParser\Node\Expr\Assign(
								new \PhpParser\Node\Expr\Variable($variable),
								new \PhpParser\Node\Expr\New_(
									new \PhpParser\Node\Name\FullyQualified($className),
									[],
									['__skip_construct_test__' => true]
								)
							);
							array_unshift($nodes, $assign);
						}
					}
				}
			}
		}

		return $nodes;
	}

}
