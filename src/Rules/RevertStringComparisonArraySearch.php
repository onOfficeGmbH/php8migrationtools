<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RevertStringComparisonArraySearch extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "c_arraySearch($a, $b)" with "array_search($a, $b)"', [new CodeSample(
            <<<'CODE_SAMPLE'
if (c_arraySearch($a, $b))
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
if (array_search($a, $b))
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
            (string) $node->name === 'c_arraySearch') {
            return new FuncCall(new Name('array_search'), $node->args);
        }

        return null;
    }
}
