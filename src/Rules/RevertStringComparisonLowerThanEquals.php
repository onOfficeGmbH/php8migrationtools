<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\SmallerOrEqual;
use PhpParser\Node\Expr\FuncCall;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RevertStringComparisonLowerThanEquals extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "c_lte($a, $b)" with "$a <= $b"', [new CodeSample(
            <<<'CODE_SAMPLE'
if (c_lte($a, $b))
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
if ($a <= $b)
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [
            FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?SmallerOrEqual
    {
        if ($node instanceof FuncCall &&
            $node->name->getType() === 'Name_FullyQualified' &&
            (string) $node->name === 'c_lte') {
            return new SmallerOrEqual($node->args[0]->value, $node->args[1]->value);
        }

        return null;
    }
}
