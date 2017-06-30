<?php

namespace AppBundle\DQL\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * ReplaceFunction ::= "REPLACE" "(" StringPrimary "," StringPrimary "," StringPrimary ")"
 */
class replace extends FunctionNode
{
	public $firstString;
	public $secondString;
	public $thirdString;

	/**
	 * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
	 *
	 * @return string
	 */
	public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
	{
		return 'replace('
		. $this->firstString->dispatch($sqlWalker) . ','
		. $this->secondString->dispatch($sqlWalker) . ','
		. $this->thirdString->dispatch($sqlWalker)
		. ')';
	}

	/**
	 * @param \Doctrine\ORM\Query\Parser $parser
	 *
	 * @return void
	 */
	public function parse(\Doctrine\ORM\Query\Parser $parser)
	{
		$parser->match(Lexer::T_IDENTIFIER); // replace
		$parser->match(Lexer::T_OPEN_PARENTHESIS); // (
		$this->firstString = $parser->StringPrimary(); // first string value
		$parser->match(Lexer::T_COMMA); // ,
		$this->secondString = $parser->StringPrimary(); // second string value
		$parser->match(Lexer::T_COMMA); // ,
		$this->thirdString = $parser->StringPrimary(); // third string value
		$parser->match(Lexer::T_CLOSE_PARENTHESIS); // )
	}
}