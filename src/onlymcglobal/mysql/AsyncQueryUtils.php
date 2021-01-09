<?php

declare(strict_types=1);

namespace onlymcglobal\mysql;

use onlymcglobal\OnlyMCGlobal;
use onlymcglobal\OnlyMCGlobalTrait;
use pocketmine\Server;

class AsyncQueryUtils {

    use OnlyMCGlobalTrait;

    /** @var string */
    private string $host;
    /** @var string */
    private string $username;
    /** @var string */
    private string $password;
    /** @var string */
    private string $dbname;
    /** @var int */
    private int $port;

    /** @var callable[] */
    private static array $callbacks = [];

    public function init(): void {
        $data = OnlyMCGlobal::getInstance()->getConfig()->get('mysql');

        $this->host = $data['host'];

        $this->username = $data['username'];

        $this->password = $data['password'];

        $this->dbname = $data['dbname'];

        $this->port = $data['port'];
    }

    /**
     * @param AsyncQuery $query
     * @param callable|null $callback
     */
    public function submitQuery(AsyncQuery $query, callable $callback = null): void {
        if ($callback !== null) {
            self::$callbacks[spl_object_hash($query)] = $callback;
        }

        $query->host = $this->host;
        $query->user = $this->username;
        $query->password = $this->password;
        $query->database = $this->dbname;
        $query->port = $this->port;

        Server::getInstance()->getAsyncPool()->submitTask($query);
    }

    /**
     * @param AsyncQuery $query
     */
    public static function submitCallback(AsyncQuery $query): void {
        $callable = self::$callbacks[spl_object_hash($query)] ?? null;

        if(!is_callable($callable)) return;

        $callable($query);
    }
}