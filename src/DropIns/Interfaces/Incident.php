<?php

namespace onOffice\Migration\Php8\DropIns\Interfaces;

interface Incident
{
    public function getSubject(): string;

    public function getMessage(): string;

    public function addInformation(
        array $additionalIncidentInformation,
        bool  $prepend = false
    ): void;
}
