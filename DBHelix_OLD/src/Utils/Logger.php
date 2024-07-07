<?php

class Logger {
    private $LogFile;

    public function __construct($LogFile) {
        $this->LogFile = $LogFile;
    }

    public function log($message) {
        file_put_contents($this->LogFile, $message . PHP_EOL, FILE_APPEND);
    }

    public function logQuery($SQL, $Params, $ExecutionTime = null) {
        $LogEntry = "Query: $SQL\nParameters: " . json_encode($Params) . "\n";
        if ($ExecutionTime !== null) {
            $LogEntry .= "Execution Time: " . $ExecutionTime . "ms\n";
        }
        $this->log($LogEntry);
    }
}

?>
