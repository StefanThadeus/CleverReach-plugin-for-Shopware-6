<?php declare(strict_types=1);

namespace Logeecom\CleverReachPlugin;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Logeecom\CleverReachPlugin\BusinessLogic\Services\Utility\DatabaseHandler;

class CleverReachPlugin extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            $this->removeAllCleverReachTables();
        }
    }

    private function removeAllCleverReachTables(): void
    {
        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);
        $databaseHandler = new DatabaseHandler($connection);
        $databaseHandler->removeCleverReachTables();
    }
}
