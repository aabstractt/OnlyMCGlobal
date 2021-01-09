<?php

declare(strict_types=1);

namespace onlymcglobal\mysql;

use onlymcglobal\OnlyMCGlobal;
use onlymcglobal\OnlyMCGlobalTrait;
use pocketmine\Server;

class AsyncQueryUtils {

    use OnlyMCGlobalTrait;

    private string $host;
    private string $username;
    private string $password;
    private string $dbname;

    /** @var callable[] */
    private static array $callbacks = [];

    public function init(): void {
        $data = OnlyMCGlobal::getInstance()->getConfig()->get('mysql');

        $this->host = $data['host'];

        $this->username = $data['username'];

        $this->password = $data['password'];
    }

    /**
     * @param AsyncQuery $query
     * @param callable $callback
     *
     * @phpstan-param \Closure(AsyncQuery $query): void $callback
     */
    public function submitQuery(AsyncQuery $query, callable $callback): void {
        self::$callbacks[spl_object_hash($query)] = $callback;

        $query->host = $this->host;
        $query->user = $this->username;
        $query->password = $this->password;

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