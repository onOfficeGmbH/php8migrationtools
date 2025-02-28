<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\GreaterOrEqual;
use PhpParser\Node\Expr\FuncCall;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RevertStringComparisonGreaterThanEquals extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "c_gte($a, $b)" with "$a >= $b"', [new CodeSample(
            <<<'CODE_SAMPLE'
if (c_gte($a, $b))
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
if ($a >= $b)
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [
            FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?GreaterOrEqual
    {
        if ($node instanceof FuncCall &&
            $node->name->getType() === 'Name_FullyQualified' &&
            (string) $node->name === 'c_gte') {
            return new GreaterOrEqual($node->args[0]->value, $node->args[1]->value);
        }

        return null;
    }
}
