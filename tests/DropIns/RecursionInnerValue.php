<?php

namespace onOffice\Migration\Php8\Tests\DropIns;

class RecursionInnerValue
{
    private $bool;

    private $outerValue;

    public function __construct(bool $bool, RecursionOuterValue $outerValue = null)
    {
        $this->bool = $bool;
        $this->outerValue = $outerValue;
    }

    public function isTrue(): bool
    {
        return $this->bool;
    }

    public function setOuterValue(?RecursionOuterValue $outerValue): void
    {
        $this->outerValue = $outerValue;
    }

    public function getString(): ?RecursionOuterValue
    {
        return $this->outerValue;
    }
}
