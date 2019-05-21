<?php

namespace BFW\Test\Bin\Ressources;

use Exception;
use Dubture\Monolog\Reader\LogReader;

trait LogLineTesterTrait
{
    protected $logRecords = [];

    protected function obtainMonologRecords($logFilePath): array
    {
        $logReader  = new LogReader($logFilePath);
        $mmLogLines = [];
        $startMMLog = false;

        foreach ($logReader as $logLine) {
            if ($logLine['context'] === ['action' => 'BfwApp_run_moduleManager']) {
                $startMMLog = true;
                continue;
            }

            if ($startMMLog === false) {
                continue;
            }

            if ($logLine['context'] === ['action' => 'BfwApp_done_moduleManager']) {
                break;
            }

            $mmLogLines[] = $logLine;
        }

        return $mmLogLines;
    }

    protected function checkLogLineExist($logIdx)
    {
        if (!isset($this->logRecords[$logIdx])) {
            throw new Exception('There are no line for idx '.$logIdx);
        }
    }

    protected function checkLogLineMsg($logIdx, $expectedMsg)
    {
        $this->checkLogLineExist($logIdx);

        $logMsg = $this->logRecords[$logIdx]['message'];
        if ($logMsg !== $expectedMsg) {
            throw new Exception(
                '[LM] Msg on line idx '.$logIdx.' is not equal to expected'
            );
        }
    }

    protected function checkLogLineContextKeys($logIdx, $expectedKeys)
    {
        $this->checkLogLineExist($logIdx);

        $context = $this->logRecords[$logIdx]['context'];

        foreach ($expectedKeys as $expectedKey) {
            if (!array_key_exists($expectedKey, $context)) {
                throw new Exception(
                    '[LCK] Context for line idx '.$logIdx.' not contain the key '.$expectedKey
                );
            }
        }
    }

    protected function checkLogLineContextKeyEqual($logIdx, $keyName, $expectedValue)
    {
        $this->checkLogLineExist($logIdx);

        $context = $this->logRecords[$logIdx]['context'];
        if (!isset($context[$keyName])) {
            throw new Exception(
                '[LCKE] Context for line idx '.$logIdx.' not contain the key '.$keyName
            );
        }

        if ($context[$keyName] !== $expectedValue) {
            throw new Exception(
                '[LCKE] The key '.$keyName.' in context for line idx '.$logIdx.' is not equal to the expected value'
            );
        }
    }

    protected function checkLogLineContextKeyContain($logIdx, $keyName, $expectedValue)
    {
        $this->checkLogLineExist($logIdx);

        $context = $this->logRecords[$logIdx]['context'];
        if (!isset($context[$keyName])) {
            throw new Exception(
                '[LCKC] Context for line idx '.$logIdx.' not contain the key '.$keyName
            );
        }

        if (strpos($context[$keyName], $expectedValue) === false) {
            throw new Exception(
                '[LCKC] The key '.$keyName.' in context for line idx '.$logIdx.' not contain the expected value'
            );
        }
    }
}
