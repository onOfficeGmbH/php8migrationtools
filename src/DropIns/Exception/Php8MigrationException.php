<?php

namespace onOffice\Migration\Php8\DropIns\Exception;

use LogicException;

class Php8MigrationException extends LogicException
{
    /** @var string $details */
    private $details = null;

    public function setDetails(
        string $details
    ): void {
        $this->details = $details;
    }

    public function __toString(): string
    {
        return
            ($this->details !== null ? 'For details see below.' . "\n\n" : '')
            . parent::__toString()
            . ($this->details !== null ? "\n\n" . 'Details:' . "\n" . $this->details : '');
    }
}
