<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Switch_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class RevertSwitchWithPhp7Behavior extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace switch with compatibility function back to its original', [new CodeSample(
            <<<'CODE_SAMPLE'
class SomeObject
{
    public function run($value)
    {
        $result = 0;
        switch (true) {
            case c_eq($value, 'abc'):
            $result = 1000;
            case c_eq($value, ''):
            $result = 2000;
        }
        return $result;
    }
}
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
class SomeObject
{
    public function run($value)
    {
        $result = 0;
        switch ($value) {
            case 'abc':
            $result = 1000;
            case '':
            $result = 2000;
        }
        return $result;
    }
}
CODE_SAMPLE
        )]);
    }


    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Switch_::class];
    }

    /**
     * @param Switch_ $node
     * @return Node|null
     */
    public function refactor(Node $node)
    {
        $switchCondition = $node->cond;
        if (! $this->valueResolver->isTrue($switchCondition)) {
            return null;
        }

        $originalSwitchCondition = null;

        /** @var Case_ $case */
        foreach ((array)$node->cases as $case) {
            $caseCondition = $case->cond;

            // default case
            if ($caseCondition === null) {
                continue;
            }

            if (! $caseCondition instanceof FuncCall ||
                $caseCondition->name->toLowerString() !== 'c_eq' ||
                count($caseCondition->args) !== 2) {
                return null;
            }

            if ($originalSwitchCondition === null) {
                $originalSwitchCondition = $caseCondition->args[0]->value;
            } elseif (!$this->nodeComparator->areNodesEqual($originalSwitchCondition, $caseCondition->args[0]->value)) {
                return null;
            }
        }

        if ($originalSwitchCondition !== null) {
            $node->cond = $originalSwitchCondition;
            /** @var Case_ $case */
            foreach ((array) $node->cases as $case) {
                /** @var FuncCall $caseCondition */
                $caseCondition = $case->cond;

                // default case
                if ($caseCondition === null) {
                    continue;
                }

                $case->cond = $caseCondition->args[1]->value;
            }
        }

        return $node;
    }
}
