<?php

namespace Logeecom\CleverReachPlugin\BusinessLogic\Services\Utility;

use Doctrine\DBAL\Connection;
use Logeecom\CleverReachPlugin\Migration\Migration1675170855CreateConfigTable;

class DatabaseHandler
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function removeCleverReachTables(): void
    {
        $this->removeTable(Migration1675170855CreateConfigTable::CONFIG_TABLE);
    }

    private function removeTable(string $tableName): void
    {
        $sql = "DROP TABLE IF EXISTS `{$tableName}`";
        $this->connection->executeUpdate($sql);
    }
}