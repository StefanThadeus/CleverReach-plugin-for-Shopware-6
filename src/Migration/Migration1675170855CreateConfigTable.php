<?php declare(strict_types=1);

namespace Logeecom\CleverReachPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1675170855CreateConfigTable extends MigrationStep
{
    public const CONFIG_TABLE = 'cleverreach_config';

    public function getCreationTimestamp(): int
    {
        return 1675170855;
    }

    public function update(Connection $connection): void
    {
        $sqlCreate = 'CREATE TABLE IF NOT EXISTS `' . self::CONFIG_TABLE . '` (
            `id` BIGINT unsigned NOT NULL AUTO_INCREMENT,
            `key` VARCHAR(255),
            `value` TEXT,
            PRIMARY KEY (`id`)
        ) 
        ENGINE = InnoDB
        DEFAULT CHARSET = utf8
        COLLATE = utf8_general_ci;';

        $connection->executeStatement($sqlCreate);
    }

    public function updateDestructive(Connection $connection): void
    {
        $sql = 'DROP TABLE IF EXISTS `' . self::CONFIG_TABLE . '`';
        $connection->executeUpdate($sql);
    }
}
