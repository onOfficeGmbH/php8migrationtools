<?php

namespace onOffice\Migration\Php8\DropIns\Incident;

class Php8MigrationIncident extends GenericIncident
{
    /** @var string */
    private $subject;

    /** @var ShortBacktrace */
    private $backtrace;

    public function __construct(
        string $subject,
        $a,
        $b,
        $computed,
        $native
    ) {
        $this->subject = '[PHP 8 migration] ' . $subject;
        $this->backtrace = new ShortBacktrace();

        $info = [
            'Trace' => $this->backtrace,
            'Computed' => var_export($computed, true),
            'Native' => var_export($native, true),
            'a' => var_export($a, true),
            'b' => var_export($b, true),
        ];

        $this->addInformation($info);
    }

    public function getSubject(): string
    {
        return $this->subject;
    }
}
