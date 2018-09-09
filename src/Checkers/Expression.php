<?php

namespace PragmaRX\Health\Checkers;

use PragmaRX\Health\Support\Result;

class Expression extends Base
{
    /**
     * Check resource.
     *
     * @return Result
     */
    public function check()
    {
        return $this->expressionIsOk()
            ? $this->makeHealthyResult()
            : $this->makeResult(false, $this->target->getErrorMessage());
    }

    public function expressionIsOk()
    {
        $expressionResult = $this->executeExpression(
            $this->target->expression_value
        );

        if ($this->target->should_return === true) {
            return (bool) $expressionResult;
        }

        if ($this->target->should_return === false) {
            return ! $expressionResult;
        }

        return preg_match(
            "|{$this->target->should_return}|",
            $expressionResult
        );
    }

    public function executeExpression($expression)
    {
        return eval("return {$expression} ;");
    }
}
