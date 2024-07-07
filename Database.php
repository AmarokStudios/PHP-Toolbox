<?php

class Database implements DatabaseInterface {
    private $PDO;
    private $Logger;
    private $Config;
    private $Cache = [];
    private $StmtCache = [];
    private $CacheExpiry = [];
    private $TransactionCounter = 0;
    private $DebugMode = false;

    public function __construct(Config $Config, Logger $Logger) {
        $this->Config = $Config;
        $this->Logger = $Logger;
        $this->connect();
    }

    private function connect() {
        try {
            $DSN = "{$this->Config->get('driver')}:host={$this->Config->get('host')};dbname={$this->Config->get('dbname')};charset=utf8mb4";
            $this->PDO = new PDO($DSN, $this->Config->get('username'), $this->Config->get('password'), [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $E) {
            $this->handleError($E);
        }
    }

    public function enableDebugMode() {
        $this->DebugMode = true;
    }

    public function disableDebugMode() {
        $this->DebugMode = false;
    }

    private function handleError($Exception) {
        $ErrorMessage = "Error: " . $Exception->getMessage();
        $this->Logger->log($ErrorMessage);
        throw new DatabaseException($ErrorMessage);
    }

    private function retryQuery($SQL, $Params, $Retries = 3) {
        $Attempt = 0;
        while ($Attempt < $Retries) {
            try {
                $Stmt = $this->prepareStmt($SQL);
                $Stmt->execute($Params);
                return $Stmt;
            } catch (PDOException $E) {
                $Attempt++;
                if ($Attempt >= $Retries) {
                    $this->handleError($E);
                }
            }
        }
    }

    public function query($SQL, $Params = []) {
        $StartTime = microtime(true);
        try {
            $Stmt = $this->prepareStmt($SQL);
            $Stmt->execute($Params);
            $ExecutionTime = round((microtime(true) - $StartTime) * 1000, 2);
            $this->Logger->logQuery($SQL, $Params, $ExecutionTime);
            return $Stmt;
        } catch (PDOException $E) {
            return $this->retryQuery($SQL, $Params);
        }
    }

    private function prepareStmt($SQL) {
        if (isset($this->StmtCache[$SQL])) {
            return $this->StmtCache[$SQL];
        }
        $Stmt = $this->PDO->prepare($SQL);
        $this->StmtCache[$SQL] = $Stmt;
        return $Stmt;
    }

    public function select($Table, $Columns = '*', $Where = '', $Params = [], $Cache = false, $CacheDuration = 60) {
        $SQL = "SELECT {$Columns} FROM {$Table}";
        if ($Where) {
            $SQL .= " WHERE {$Where}";
        }
        $CacheKey = $SQL . json_encode($Params);
        if ($Cache && isset($this->Cache[$CacheKey])) {
            if (time() - $this->CacheExpiry[$CacheKey] < $CacheDuration) {
                return $this->Cache[$CacheKey];
            } else {
                unset($this->Cache[$CacheKey]);
                unset($this->CacheExpiry[$CacheKey]);
            }
        }
        $Result = $this->query($SQL, $Params)->fetchAll();
        if ($Cache) {
            $this->Cache[$CacheKey] = $Result;
            $this->CacheExpiry[$CacheKey] = time();
        }
        return $Result;
    }

    public function insert($Table, $Data) {
        $Columns = implode(', ', array_keys($Data));
        $Placeholders = ':' . implode(', :', array_keys($Data));
        $SQL = "INSERT INTO {$Table} ({$Columns}) VALUES ({$Placeholders})";
        $this->query($SQL, $Data);
        return $this->PDO->lastInsertId();
    }

    public function batchInsert($Table, $DataArray) {
        $Columns = implode(', ', array_keys($DataArray[0]));
        $Placeholders = ':' . implode(', :', array_keys($DataArray[0]));
        $SQL = "INSERT INTO {$Table} ({$Columns}) VALUES ({$Placeholders})";

        $this->beginTransaction();
        try {
            $Stmt = $this->prepareStmt($SQL);
            foreach ($DataArray as $Data) {
                $Stmt->execute($Data);
                $this->Logger->logQuery($SQL, $Data);
            }
            $this->commit();
        } catch (PDOException $E) {
            $this->rollBack();
            $this->handleError($E);
        }
    }

    public function batchUpdate($Table, $DataArray, $WhereColumn) {
        $Columns = array_keys($DataArray[0]);
        $Set = implode(', ', array_map(function ($Column) {
            return "{$Column} = :{$Column}";
        }, $Columns));
        $SQL = "UPDATE {$Table} SET {$Set} WHERE {$WhereColumn} = :{$WhereColumn}";

        $this->beginTransaction();
        try {
            $Stmt = $this->prepareStmt($SQL);
            foreach ($DataArray as $Data) {
                $Stmt->execute($Data);
                $this->Logger->logQuery($SQL, $Data);
            }
            $this->commit();
        } catch (PDOException $E) {
            $this->rollBack();
            $this->handleError($E);
        }
    }

    public function update($Table, $Data, $Where, $Params = []) {
        $Set = '';
        foreach ($Data as $Key => $Value) {
            $Set .= "{$Key} = :{$Key}, ";
        }
        $Set = rtrim($Set, ', ');
        $SQL = "UPDATE {$Table} SET {$Set} WHERE {$Where}";
        $this->query($SQL, array_merge($Data, $Params));
    }

    public function renameColumn($Table, $OldColumnName, $NewColumnName, $ColumnType) {
        $SQL = "ALTER TABLE {$Table} CHANGE {$OldColumnName} {$NewColumnName} {$ColumnType}";
        $this->query($SQL);
    }

    public function dropColumn($Table, $ColumnName) {
        $SQL = "ALTER TABLE {$Table} DROP COLUMN {$ColumnName}";
        $this->query($SQL);
    }

    public function delete($Table, $Where, $Params = []) {
        $SQL = "DELETE FROM {$Table} WHERE {$Where}";
        $this->query($SQL, $Params);
    }

    public function countRows($Table, $Where = '', $Params = []) {
        $SQL = "SELECT COUNT(*) as count FROM {$Table}";
        if ($Where) {
            $SQL .= " WHERE {$Where}";
        }
        $Result = $this->query($SQL, $Params)->fetch();
        return $Result['count'];
    }

    public function getTableSchema($Table) {
        $SQL = "DESCRIBE {$Table}";
        return $this->query($SQL)->fetchAll();
    }

    public function createTable($Table, $Columns) {
        $ColumnDefs = [];
        foreach ($Columns as $Column => $Def) {
            $ColumnDefs[] = "{$Column} {$Def}";
        }
        $ColumnDefs = implode(', ', $ColumnDefs);
        $SQL = "CREATE TABLE IF NOT EXISTS {$Table} ({$ColumnDefs})";
        $this->query($SQL);
    }

    public function tableExists($Table) {
        try {
            $Result = $this->query("SELECT 1 FROM {$Table} LIMIT 1");
        } catch (PDOException $E) {
            return false;
        }
        return $Result !== false;
    }

    public function escapeIdentifier($Identifier) {
        return "`" . str_replace("`", "``", $Identifier) . "`";
    }

    public function backupDatabase($BackupFilePath) {
        $SQL = "SHOW TABLES";
        $Tables = $this->query($SQL)->fetchAll(PDO::FETCH_COLUMN);
        $BackupData = "";

        foreach ($Tables as $Table) {
            $CreateTableSQL = $this->query("SHOW CREATE TABLE {$Table}")->fetch(PDO::FETCH_COLUMN, 1);
            $BackupData .= "{$CreateTableSQL};\n\n";

            $Rows = $this->query("SELECT * FROM {$Table}")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($Rows as $Row) {
                $Values = array_map([$this->PDO, 'quote'], array_values($Row));
                $BackupData .= "INSERT INTO {$Table} VALUES (" . implode(", ", $Values) . ");\n";
            }
            $BackupData .= "\n\n";
        }

        file_put_contents($BackupFilePath, $BackupData);
    }

    public function restoreDatabase($BackupFilePath) {
        $SQLs = file_get_contents($BackupFilePath);
        $this->beginTransaction();
        try {
            $this->PDO->exec($SQLs);
            $this->commit();
        } catch (PDOException $E) {
            $this->rollBack();
            $this->handleError($E);
        }
    }

    public function beginTransaction() {
        if (!$this->TransactionCounter++) {
            $this->PDO->beginTransaction();
        }
    }

    public function commit() {
        if (!--$this->TransactionCounter) {
            $this->PDO->commit();
        }
    }

    public function rollBack() {
        if ($this->TransactionCounter >= 0) {
            $this->TransactionCounter = 0;
            $this->PDO->rollBack();
        }
    }

    public function savepoint($SavepointName) {
        $this->query("SAVEPOINT {$SavepointName}");
    }

    public function rollbackToSavepoint($SavepointName) {
        $this->query("ROLLBACK TO SAVEPOINT {$SavepointName}");
    }

    public function getConnection() {
        return $this->PDO;
    }

    public function exportToCSV($Table, $FilePath) {
        $Data = $this->select($Table);
        $File = fopen($FilePath, 'w');
        if (!empty($Data)) {
            fputcsv($File, array_keys($Data[0]));
            foreach ($Data as $Row) {
                fputcsv($File, $Row);
            }
        }
        fclose($File);
    }

    public function exportToJSON($Table, $FilePath) {
        $Data = $this->select($Table);
        file_put_contents($FilePath, json_encode($Data, JSON_PRETTY_PRINT));
    }

    public function importFromCSV($Table, $FilePath) {
        $File = fopen($FilePath, 'r');
        $Header = fgetcsv($File);
        $Data = [];
        while ($Row = fgetcsv($File)) {
            $Data[] = array_combine($Header, $Row);
        }
        fclose($File);
        foreach ($Data as $Row) {
            $this->insert($Table, $Row);
        }
    }

    public function callStoredProcedure($ProcedureName, $Params = []) {
        $Placeholders = implode(', ', array_map(function ($Key) {
            return ":{$Key}";
        }, array_keys($Params)));
        $SQL = "CALL {$ProcedureName}({$Placeholders})";
        return $this->query($SQL, $Params)->fetchAll();
    }

    public function buildSelectQuery($Table, $Columns = '*', $Conditions = [], $OrderBy = '', $Limit = '') {
        $SQL = "SELECT {$Columns} FROM {$Table}";
        if (!empty($Conditions)) {
            $SQL .= " WHERE " . implode(' AND ', array_map(function($Key) { return "{$Key} = :{$Key}"; }, array_keys($Conditions)));
        }
        if ($OrderBy) {
            $SQL .= " ORDER BY {$OrderBy}";
        }
        if ($Limit) {
            $SQL .= " LIMIT {$Limit}";
        }
        return $SQL;
    }

    // Migration System
    public function applyMigrations($MigrationsDirectory) {
        $AppliedMigrations = $this->getAppliedMigrations();

        $NewMigrations = [];
        foreach (scandir($MigrationsDirectory) as $File) {
            if (pathinfo($File, PATHINFO_EXTENSION) === 'php' && !in_array($File, $AppliedMigrations)) {
                $NewMigrations[] = $File;
            }
        }

        usort($NewMigrations, function($a, $b) {
            return strnatcmp($a, $b);
        });

        foreach ($NewMigrations as $Migration) {
            require_once $MigrationsDirectory . '/' . $Migration;
            $ClassName = pathinfo($Migration, PATHINFO_FILENAME);
            $MigrationInstance = new $ClassName();
            $MigrationInstance->up();
            $this->logMigration($Migration);
        }
    }

    private function getAppliedMigrations() {
        $this->query("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
        $Migrations = $this->query("SELECT migration FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
        return $Migrations;
    }

    private function logMigration($Migration) {
        $this->insert('migrations', ['migration' => $Migration]);
    }
}

?>
