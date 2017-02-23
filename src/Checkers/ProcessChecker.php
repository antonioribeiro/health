<?php

namespace PragmaRX\Health\Checkers;

use Clearitie\Exceptions\Exception;

class ProcessChecker extends BaseChecker
{
    const METHOD_PROCESS_COUNT = 'process_count';

    const METHOD_PID_FILE = 'pid_file';

    /**
     * Check resource.
     *
     * @return bool
     */
    public function check()
    {
        $message = $this->checkMinMax($this->getProcessesRunningCount());

        if ($message) {
            return $this->makeResult(false, $message);
        }

        return $this->makeHealthyResult();
    }

    private function checkMinMax($processes)
    {
        return $this->buildMessage('minimum', $processes) ?: $this->buildMessage('maximum', $processes);
    }

    private function buildMessage($type, $processes)
    {
        $instances = $this->resource['instances'];

        if (! $count = (int) $instances[$type]['count']) {
            return '';
        }

        if ($type == 'minimum') {
            $diff = $processes - $count;
        } else {
            $diff = $count - $processes;
        }

        if ($diff < 0) {
            return sprintf($instances[$type]['message'], $this->resource['process_name'], $processes, $count);
        }
    }

    private function checkPidFile()
    {
        return $this->processPidFileIsLocked()
                ? 1
                : 0;
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

        throw new Exception(sprintf($this->resource['pid_file_missing_error_message'], $file));
    }

    /**
     * @param $file
     * @return bool
     */
    private function checkPidFileLockState($file)
    {
        try {
            $filePointer = fopen($file, 'r+');
        } catch (\Exception $exception) {
            return;
        }

        try {
            $locked = flock($filePointer, LOCK_EX);

            flock($filePointer, LOCK_UN);
        } catch (\Exception $exception) {
        }

        fclose($filePointer);

        if ($locked) {
            return;
        }

        throw new Exception(sprintf($this->resource['pid_file_missing_not_locked'], $file));
    }

    /**
     * @return bool
     */
    private function processPidFileIsLocked()
    {
        $file = $this->resource['pid_file'];

        $this->checkPidFileExistence($file);

        $this->checkPidFileLockState($file);

        return 1;
    }

    private function getCommand()
    {
        $command = $this->resource['command'];

        $name = $this->resource['process_name'];

        if ($command && $name) {
            return sprintf($command, $name);
        }
    }

    private function getProcessesRunningCount()
    {
        if ($this->resource['method'] == static::METHOD_PROCESS_COUNT) {
            return $this->countRunningProcesses();
        }

        if ($this->resource['method'] == static::METHOD_PID_FILE) {
            return $this->checkPidFile();
        }
    }
}
