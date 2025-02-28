<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function array_pop;
use function strtolower;

class StringComparisonInArray extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('replace "in_array($a, $b)" with "c_inArray($a, $b)"', [
            new CodeSample(
                <<<'CODE_SAMPLE'
if (in_array($a, $b)
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
if (c_inArray($a, $b))
CODE_SAMPLE
            ),
        ]);
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
            (string) $node->name === 'in_array') {
            if (3 === count($node->args) &&
                $node->args[2]->value instanceof ConstFetch) {
                if ('true' === strtolower($node->args[2]->value->name)) {
                    return null;
                }

                if ('false' === strtolower($node->args[2]->value->name)) {
                    array_pop($node->args);
                }
            }

            return new FuncCall(new Name('c_inArray'), $node->args);
        }

        return null;
    }
}
