<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RevertStringComparisonArrayKeys extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "c_arrayKeys($a, $b)" with "array_keys($a, $b)"', [new CodeSample(
            <<<'CODE_SAMPLE'
if (c_arrayKeys($a, $b))
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
if (array_keys($a, $b))
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [
            FuncCall::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof FuncCall &&
            $node->name->getType() === 'Name_FullyQualified' &&
            (string) $node->name === 'c_arrayKeys') {
            return new FuncCall(new Name('array_keys'), $node->args);
        }

        return null;
    }
}
