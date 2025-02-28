<?php

namespace onOffice\Migration\Php8\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\NullsafePropertyFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt\Case_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Switch_;
use Rector\Core\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

class SwitchWithPhp7Behavior extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Replace switch with usage of compatibility function', [new CodeSample(
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
            ,
            <<<'CODE_SAMPLE'
class SomeObject
{
    public function run($value)
    {
        $result = 1;
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
     * @return Node[]
     */
    public function refactor(Node $node)
    {
        $nodes = [];
        $switchCondition = $node->cond;
        if ($this->valueResolver->isTrue($switchCondition)) {
            return null;
        }

        // Everything besides these could have side effects
        if (!$switchCondition instanceof ConstFetch &&
            !$switchCondition instanceof ClassConstFetch &&
            !$switchCondition instanceof NullsafePropertyFetch &&
            !$switchCondition instanceof PropertyFetch &&
            !$switchCondition instanceof Variable &&
            !$switchCondition instanceof Scalar) {
            $switchConditionVariable = new Variable('switchTmp'.$node->getLine());
            $assignmentExpr = new Assign($switchConditionVariable, $switchCondition);
            $switchCondition = $switchConditionVariable;
            $nodes []= new Expression($assignmentExpr);
        }

        $node->cond = new ConstFetch(new Name('true'));

        /** @var Case_ $case */
        foreach ((array)$node->cases as $case) {
            $caseCondition = $case->cond;

            // default case
            if ($caseCondition === null) {
                continue;
            }

            $case->cond = new FuncCall(new Name('c_eq'), [new Arg($switchCondition), new Arg($caseCondition)]);
        }

        $nodes []= $node;

        return $nodes;
    }
}
