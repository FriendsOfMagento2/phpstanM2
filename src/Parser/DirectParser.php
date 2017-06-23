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

		$nodes = $this->traverser->traverse($nodes);

		if ($nodes && $nodes[0] instanceof \PhpParser\Node\Stmt\Nop) {

			$autoAssignVariables = [
				'block',
				'this',
			];

			foreach ($nodes[0]->getAttribute('comments', []) as $comment) {
				if (preg_match('/@var\s+([A-Za-z0-9\\\]+)\s+\$(' . implode('|', $autoAssignVariables) . ')/im', $comment->getText(), $matches)) {
					$assign = new \PhpParser\Node\Expr\Assign(
						new \PhpParser\Node\Expr\Variable($matches[2]),
						new \PhpParser\Node\Expr\New_(
							new \PhpParser\Node\Name\FullyQualified(trim($matches[1], '\\')),
							[],
							['__skip_construct_test__' => true]
						)
					);
					array_unshift($nodes, $assign);
				}
			}
		}

		return $nodes;
	}

}
