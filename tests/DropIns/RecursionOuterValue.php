<?php

namespace onOffice\Migration\Php8\Tests\DropIns;

class RecursionOuterValue
{
    private $someint;

    private $aStringIsh;
    private $innerObject;

    public function __construct(int $someint, ?string $aStringIsh, ?RecursionInnerValue $innerObject = null)
    {
        $this->someint = $someint;
        $this->aStringIsh = $aStringIsh;
        $this->innerObject = $innerObject;
    }

    public function setInnerObject(RecursionInnerValue $innerObject): void
    {
        $this->innerObject = $innerObject;
    }


    public function getSomeint(): int
    {
        return $this->someint;
    }

    public function getAStringIsh(): ?string
    {
        return $this->aStringIsh;
    }

    public function getInnerObject(): ?RecursionInnerValue
    {
        return $this->innerObject;
    }
}
