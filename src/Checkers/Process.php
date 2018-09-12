<?php

namespace PragmaRX\Health\Checkers;

use DomainException;
use PragmaRX\Health\Support\Result;

class Process extends Base
{
    const METHOD_PROCESS_COUNT = 'process_count';

    const METHOD_PID_FILE = 'pid_file';

    /**
     * Check target.
     *
     * @return Result
     */
    public function check()
    {
        $message = $this->checkMinMax($this->getProcessesRunningCount());

        if (! empty($message)) {
            return $this->makeResult(false, $message);
        }

        return $this->makeHealthyResult();
    }

    private function checkMinMax($processes)
    {
        return $this->buildMessage('minimum', $processes)
            ?: $this->buildMessage('maximum', $processes);
    }

    private function buildMessage($type, $processes)
    {
        $instances = $this->target->instances;

        if (! $count = (int) $instances[$type]['count']) {
            return '';
        }

        if ($type == 'minimum') {
            $diff = $processes - $count;
        } else {
            $diff = $count - $processes;
        }

        if ($diff < 0) {
            return sprintf(
                $instances[$type]['message'],
                $this->target->processName,
                $processes,
                $count
            );
        }
    }

    private function checkPidFile()
    {
        return $this->processPidFileIsLocked() ? 1 : 0;
    }

    private function countRunningProcesses()
    {
        if ($command = $this->getCommand()) {
            exec($command, $count);

            return count($count);
        }

        return 0;
    }

    /**
     * @param $file
     * @return bool
     */
    private function checkPidFileExistence($file)
    {
        if (file_exists($file)) {
            return 1;
        }

        throw new DomainException(
            sprintf($this->target->pidFileMissingErrorMessage, $file)
        );
    }

    /**
     * @param $file
     * @return bool
     */
    private function checkPidFileLockState($file)
    {
        try {
            $locked = flock($filePointer = fopen($file, 'r+'), LOCK_EX);

            flock($filePointer, LOCK_UN);

            fclose($filePointer);

            if (! $locked) {
                throw new DomainException(
                    sprintf($this->target->pid_file_missing_not_locked, $file)
                );
            }
        } catch (\Exception $exception) {
            report($exception);
        }
    }

    /**
     * @return bool
     */
    private function processPidFileIsLocked()
    {
        $file = $this->target->pidFile;

        $this->checkPidFileExistence($file);

        $this->checkPidFileLockState($file);

        return 1;
    }

    private function getCommand()
    {
        $command = $this->target->command;

        $name = $this->target->processName;

        if ($command && $name) {
            return sprintf($command, $name);
        }
    }

    private function getProcessesRunningCount()
    {
        if ($this->target->method == static::METHOD_PROCESS_COUNT) {
            return $this->countRunningProcesses();
        }

        if ($this->target->method == static::METHOD_PID_FILE) {
            return $this->checkPidFile();
        }
    }
}
