<?php

class BackupExporter extends Dbh
{
    public function writeSqlBackup($output)
    {
        $pdo = $this->connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $databaseName = (string)$pdo->query('SELECT DATABASE()')->fetchColumn();
        $tables = $pdo->query(
            "SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'"
        )->fetchAll(PDO::FETCH_NUM);

        fwrite($output, "-- EARN-NOW database backup\n");
        fwrite($output, '-- Database: ' . $databaseName . "\n");
        fwrite($output, '-- Created: ' . gmdate('Y-m-d H:i:s') . " UTC\n\n");
        fwrite($output, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $tableRow) {
            $table = (string)$tableRow[0];
            $quotedTable = $this->quoteIdentifier($table);
            $create = $pdo->query("SHOW CREATE TABLE " . $quotedTable)->fetch(PDO::FETCH_NUM);
            if (!$create || !isset($create[1])) {
                throw new RuntimeException('A table definition could not be read.');
            }

            fwrite($output, "DROP TABLE IF EXISTS " . $quotedTable . ";\n");
            fwrite($output, $create[1] . ";\n\n");

            $rows = $pdo->query("SELECT * FROM " . $quotedTable);
            $columns = [];
            $batch = [];

            while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                if ($columns === []) {
                    $columns = array_keys($row);
                }

                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        $quotedValue = $pdo->quote((string)$value);
                        if ($quotedValue === false) {
                            throw new RuntimeException('A database value could not be escaped.');
                        }
                        $values[] = $quotedValue;
                    }
                }

                $batch[] = '(' . implode(', ', $values) . ')';
                if (count($batch) >= 100) {
                    $this->writeInsert($output, $quotedTable, $columns, $batch);
                    $batch = [];
                }
            }

            if ($batch !== []) {
                $this->writeInsert($output, $quotedTable, $columns, $batch);
            }

            fwrite($output, "\n");
        }

        fwrite($output, "SET FOREIGN_KEY_CHECKS=1;\n");
        return count($tables);
    }

    private function writeInsert($output, $table, array $columns, array $rows)
    {
        $quotedColumns = array_map([$this, 'quoteIdentifier'], $columns);
        fwrite(
            $output,
            'INSERT INTO ' . $table . ' (' . implode(', ', $quotedColumns) . ") VALUES\n"
            . implode(",\n", $rows) . ";\n"
        );
    }

    private function quoteIdentifier($identifier)
    {
        return '`' . str_replace('`', '``', (string)$identifier) . '`';
    }
}
