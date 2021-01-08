<?php

declare(strict_types=1);

namespace onlymcglobal\translation;

use onlymcglobal\OnlyMCGlobal;
use onlymcglobal\OnlyMCGlobalTrait;

class TranslationFactory {

    use OnlyMCGlobalTrait;

    /** @var array */
    private array $translations = [];

    public function init(): void {
        $this->translations = OnlyMCGlobal::getInstance()->getConfiguration('translations.yml')->getAll();
    }

    /**
     * @param string $key
     * @param array $args
     * @return string
     */
    public function translateString(string $key, array $args): string {
        $text = $this->translations[$key] ?? null;

        if ($text == null) {
            return $key . implode(',', $args);
        }

        foreach ($args as $i => $arg) {
            $text = str_replace('{' . $i . '}', $arg, $text);
        }

        return $text;
    }

    /**
     * @param string $key
     * @param array $args
     * @return array
     */
    public function translateArray(string $key, array $args): array {
        $text = $this->translations[$key] ?? [];

        if (empty($text)) {
            return [];
        }

        foreach ($text as $t => $replace) {
            foreach ($args as $i => $arg) {
                $text[$t] = str_replace('{' . $i . '}', $arg, $replace);
            }
        }

        return $text;
    }
}