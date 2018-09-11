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
            $this->target->expressionValue
        );

        if ($this->target->shouldReturn === true) {
            return (bool) $expressionResult;
        }

        if ($this->target->shouldReturn === false) {
            return !$expressionResult;
        }

        return preg_match("|{$this->target->shouldReturn}|", $expressionResult);
    }

    public function executeExpression($expression)
    {
        return eval("return {$expression} ;");
    }
}
