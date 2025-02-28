<?php

namespace onOffice\Migration\Php8\DropIns\Incident;

/**
 * A fast backtrace ignoring function arguments
 */
class ShortBacktrace
{
    /** @var array */
    private $entries;

    public function __construct()
    {
        $this->entries = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    }

    private function formatEntry(array $entry): string
    {
        if (isset($entry['file'])) {
            $line = realpath($entry['file']);

            if (isset($entry['line'])) {
                $line .= ' [' . $entry['line'] . ']';
            }
        } else {
            $line = '(unknown file)';
        }

        if (isset($entry['class']) &&
            isset($entry['function'])) {
            $line .= ' ' . $entry['class'] . '::' . $entry['function'] . '()';
        } elseif (isset($entry['function'])) {
            $line .= ' ' . $entry['function'] . '()';
        } else {
            $line .= ' (global scope)';
        }

        return $line;
    }

    public function __toString(): string
    {
        $trace = '';

        foreach ($this->entries as $entry) {
            $trace .= $this->formatEntry($entry) . "\n";
        }

        return $trace;
    }
}
