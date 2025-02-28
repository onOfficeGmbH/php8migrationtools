<?php

namespace onOffice\Migration\Php8\DropIns\Incident;

class TecIncident extends GenericIncident
{
    /** @var string */
    private $_subject;

    public function __construct(string $message, string $file, int $line)
    {
        // use the short (without function arguments) backtrace for hash calculation
        $pShortBacktrace = new ShortBacktrace();

        $this->_subject = '[tec] ' . $message;

        $info = [
            'File' => $file,
            'Line' => $line,
            'Error' => $message,
            'Trace' => $pShortBacktrace,
        ];

        $this->addInformation($info);
    }

    public function getSubject(): string
    {
        return $this->_subject;
    }
}
