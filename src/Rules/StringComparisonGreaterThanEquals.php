<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class StringComparisonGreaterThanEquals extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "$a >= $b" with "c_gte($a, $b)"', [new CodeSample(
            <<<'CODE_SAMPLE'
if ($a >= $b)
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
if (c_gte($a, $b))
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [
            GreaterOrEqual::class,
        ];
    }

    public function refactor(Node $node): FuncCall
    {
        return new FuncCall(new Name('c_gte'), [new Arg($node->left), new Arg($node->right)]);
    }
}
