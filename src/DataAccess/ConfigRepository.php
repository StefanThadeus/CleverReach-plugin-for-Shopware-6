<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Doctrine\DBAL\Exception;
use Logeecom\CleverReachPlugin\BusinessLogic\DTO\TokensDTO;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConfigRepositoryInterface;
use Logeecom\CleverReachPlugin\Migration\Migration1675170855CreateConfigTable;
use Doctrine\DBAL\Connection;

class ConfigRepository implements ConfigRepositoryInterface
{
    private Connection $connection;
    private string $tableName;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->tableName = Migration1675170855CreateConfigTable::CONFIG_TABLE;
    }

    public function getTokens(): TokensDTO
    {
        $sqlAuthToken = "SELECT * FROM " . $this->tableName . " WHERE " . $this->tableName . ".key = 'authorization_token'";
        $sqlRefreshToken = "SELECT * FROM " . $this->tableName . " WHERE " . $this->tableName . ".key = 'refresh_token'";
        $authResult = $this->connection->fetchAssociative($sqlAuthToken);
        $refreshResult = $this->connection->fetchAssociative($sqlRefreshToken);

        $authResult = ($authResult) ? $authResult['value'] : null;
        $refreshResult = ($refreshResult) ? $refreshResult['value'] : null;
        return new TokensDTO($authResult, $refreshResult);
    }

    /**
     * @throws Exception
     */
    public function saveTokens(TokensDTO $tokens): void
    {
        $dataBaseTokens = $this->getTokens();
        if ($dataBaseTokens->getAccessToken() && $dataBaseTokens->getRefreshToken()) {
            $this->connection->update($this->tableName, [$this->tableName . '.value' => $tokens->getAccessToken()], [$this->tableName . ".key" => 'authorization_token']);
            $this->connection->update($this->tableName, [$this->tableName . '.value' => $tokens->getRefreshToken()], [$this->tableName . ".key" => 'refresh_token']);
            return;
        }

        $this->connection->insert($this->tableName, [$this->tableName . '.key' => 'authorization_token', 'value' => $tokens->getAccessToken()]);
        $this->connection->insert($this->tableName, [$this->tableName . '.key' => 'refresh_token', 'value' => $tokens->getRefreshToken()]);
    }

    public function getSyncStatus(): ?string
    {
        $sql = "SELECT * FROM " . $this->tableName . " WHERE " . $this->tableName . ".key = 'sync_status'";
        $sqlResult = $this->connection->fetchAssociative($sql);
        return ($sqlResult) ? $sqlResult['value'] : null;
    }

    public function setSyncStatus(string $status): void
    {
        if ($this->getSyncStatus()) {
            $this->connection->update($this->tableName, [$this->tableName . '.value' => $status], [$this->tableName . ".key" => 'sync_status']);
            return;
        }

        $this->connection->insert($this->tableName, [$this->tableName . '.key' => 'sync_status', 'value' => $status]);
    }

    public function getReceiverGroupId(): ?string
    {
        $sql = "SELECT * FROM " . $this->tableName . " WHERE " . $this->tableName . ".key = 'receiver_group_id'";
        $sqlResult = $this->connection->fetchAssociative($sql);
        return ($sqlResult) ? $sqlResult['value'] : null;
    }

    public function setReceiverGroupId(string $groupId): void
    {
        if ($this->getReceiverGroupId()) {
            $this->connection->update($this->tableName, [$this->tableName . '.value' => $groupId], [$this->tableName . ".key" => 'receiver_group_id']);
            return;
        }

        $this->connection->insert($this->tableName, [$this->tableName . '.key' => 'receiver_group_id', 'value' => $groupId]);
    }

    public function saveWebhookData(string $eventName, string $callToken, string $secret): void
    {
        $this->connection->insert($this->tableName, [$this->tableName . '.key' => $eventName . '_call_token', 'value' => $callToken]);
        $this->connection->insert($this->tableName, [$this->tableName . '.key' => $eventName . '_secret', 'value' => $secret]);
    }

    public function getEventCallToken(string $eventName): string
    {
        $sql = "SELECT * FROM " . $this->tableName . " WHERE " . $this->tableName . ".key = '" . $eventName . "_call_token'";
        $sqlResult = $this->connection->fetchAssociative($sql);
        return $sqlResult['value'];
    }
}