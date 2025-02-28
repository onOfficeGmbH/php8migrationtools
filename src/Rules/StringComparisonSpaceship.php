<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\Spaceship;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class StringComparisonSpaceship extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "$a <=> $b" with "c_spaceship($a, $b)"', [new CodeSample(
            <<<'CODE_SAMPLE'
if ($a <=> $b)
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
if (c_spaceship($a, $b))
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [
            Spaceship::class,
        ];
    }

    public function refactor(Node $node): FuncCall
    {
        return new FuncCall(new Name('c_spaceship'), [new Arg($node->left), new Arg($node->right)]);
    }
}
