<?php

namespace onOffice\Migration\Php8\DropIns\Interfaces;

interface IncidentManager
{
    public function handle(Incident $pIncident): void;
}
