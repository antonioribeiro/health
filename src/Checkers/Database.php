<?php

namespace PragmaRX\Health\Checkers;

use Illuminate\Support\Facades\DB;
use SebastianBergmann\Timer\Timer;
use PragmaRX\Health\Support\Result;

class Database extends Base
{
    /**
     * @return Result
     */
    public function check()
    {
        try {
            switch ($this->target->type) {
                case 'find_first_model':
                    return $this->findFirstModel();
                case 'raw_query':
                    return $this->rawQuery();
            }

            throw new \Exception(
                "Target type '{$this->target->type}' does not exists"
            );
        } catch (\Exception $exception) {
            report($exception);

            return $this->makeResultFromException($exception);
        }
    }

    protected function findFirstModel()
    {
        collect($this->target->models)->each(function ($model) {
            instantiate($model)->first();
        });

        return $this->makeHealthyResult();
    }

    protected function getConnectionName()
    {
        return $this->target->connection == 'default'
            ? config('database.default')
            : $this->target->connection;
    }

    protected function rawQuery()
    {
        Timer::start();

        DB::connection($this->getConnectionName())->select(
            DB::raw($this->target->query)
        );

        $took = round(Timer::stop(), 5);
        $tookHuman = "{$took}s";

        $this->target->setDisplay($this->target->name." ({$tookHuman})");

        $result =
            $took > $this->target->maximumTime
                ? $this->makeResult(
                    false,
                    sprintf(
                        $this->target->errorMessage,
                        $took,
                        $this->target->maximumTime
                    )
                )
                : $this->makeHealthyResult();

        $result->setValue($took)->setValueHuman($tookHuman);

        return $result;
    }
}
