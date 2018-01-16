<?php

use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use PHPMD\AbstractRenderer;
use PHPMD\PHPMD;
use PHPMD\Report;

class MongoDbRenderer extends AbstractRenderer
{
    /**
     * Custom command line options
     *
     * @var array
     */
    protected $options = [];

    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * This method will be called when the engine has finished the source analysis
     * phase.
     *
     * @param \PHPMD\Report $report
     * @return void
     */
    public function renderReport(Report $report)
    {
        $jsonData = [];
        $jsonData['version'] = PHPMD::VERSION;
        $jsonData['timestamp'] = date('c');

        foreach ($report->getRuleViolations() as $violation) {
            $rule = $violation->getRule();

            $fileName = $violation->getFileName();
            $fileNameHash = md5($fileName);
            $violationData = [
                'filename' => $fileName,
                'beginline' => $violation->getBeginLine(),
                'endline' => $violation->getEndLine(),
                'rule' => $rule->getName(),
                'ruleset' => $rule->getRuleSetName(),
                'priority' => $rule->getPriority(),
                'description' => $violation->getDescription()
            ];

            if ($value = $violation->getNamespaceName()) {
                $violationData['package'] = $value;
            }

            if ($value = $rule->getExternalInfoUrl()) {
                $violationData['externalInfoUrl'] = $value;
            }

            if ($value = $violation->getFunctionName()) {
                $violationData['function'] = $value;
            }

            if ($value = $violation->getClassName()) {
                $violationData['class'] = $value;
            }

            if ($value = $violation->getMethodName()) {
                $violationData['method'] = $value;
            }

            $jsonData['violations'][$fileNameHash][] = $violationData;
        }

        foreach ($report->getErrors() as $error) {
            $jsonData['errors'][] = [
                'filename' => $error->getFile(),
                'msg' => $error->getMessage()
            ];
        }

        $mongoManager = new Manager($this->options['mongo-uri']);
        $bulk = new BulkWrite();
        $bulk->insert($jsonData);
        $mongoManager->executeBulkWrite($this->options['mongo-namespace'], $bulk);
    }
}
