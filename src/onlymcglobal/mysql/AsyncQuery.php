<?php

declare(strict_types=1);

namespace onlymcglobal\mysql;

use Exception;
use mysqli;
use pocketmine\scheduler\AsyncTask;

abstract class AsyncQuery extends AsyncTask {

    /** @var string */
    public string $host;
    /** @var string */
    public string $user;
    /** @var string */
    public string $password;
    /** @var string */
    private string $database;
    /** @var int */
    private int $port;

    final public function onRun(): void {
        try {
            $this->query($mysqli = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port));

            $mysqli->close();
        }
        catch (Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    public function onCompletion(): void {
        AsyncQueryUtils::submitCallback($this);
    }

    /**
     * @param mysqli $mysqli
     */
    abstract public function query(mysqli $mysqli): void;
}