<?php

declare(strict_types=1);

namespace onlymcglobal;

trait OnlyMCGlobalTrait {

    private static ?self $instance = null;

    /**
     * @return self
     */
    public static function getInstance(): self {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}