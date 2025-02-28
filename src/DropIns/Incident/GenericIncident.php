<?php

namespace onOffice\Migration\Php8\DropIns\Incident;

use onOffice\Migration\Php8\DropIns\Interfaces\Incident;

abstract class GenericIncident implements Incident
{
    /** @var array */
    private $_infos = [];

    final public function getMessage(): string
    {
        $infos = [];
        foreach ($this->_infos as $info) {
            $infoSnippet = '';
            foreach ($info as $key => $value) {
                $infoSnippet .= ' - '. $key . ': ' . $value . "\n";
            }
            $infos[] = $infoSnippet;
        }

        return implode("\n", $infos);
    }

    final public function addInformation(
        array $additionalIncidentInformation,
        bool  $prepend = false
    ): void {
        if ($prepend) {
            array_unshift($this->_infos, $additionalIncidentInformation);
        } else {
            $this->_infos[] = $additionalIncidentInformation;
        }
    }
}
