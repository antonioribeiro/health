<?php

namespace PragmaRX\Health\Checkers;

class Expression extends Base
{
    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        return $this->expressionIsOk()
            ? $this->makeHealthyResult()
            : $this->makeResult(false, $this->resource['error_message']);
    }

    public function expressionIsOk()
    {
        $expressionResult = $this->executeExpression($this->resource['expression_value']);

        if ($this->resource['should_return'] === true) {
            return (bool) $expressionResult;
        }

        if ($this->resource['should_return'] === false) {
            return ! $expressionResult;
        }

        return preg_match("|{$this->resource['should_return']}|", $expressionResult);
    }

    public function executeExpression($expression)
    {
        return eval("return {$expression} ;");
    }
}
