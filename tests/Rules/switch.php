<?php

$array = [0, 1, 2, 3, 4, 5];

switch (array_shift($array)) {
    case 6:
        echo 6;
        break;
    case 5:
        echo 5;
        break;

    case 4:
        echo 4;
        break;

    case 3:
        echo 3;
        break;

    case 2:
        echo 2;
        break;

    case 1:
        echo 1;
        break;

    default:
    case 0:
        echo 0;
        break;

}


class T
{
    public const C = 3;

    /**
     * @var int
     */
    private $t;

    public function __construct(int $t)
    {
        $this->t = $t;
    }

    public function someFn(): ?int
    {
        switch ($this->t) {
            case 3:
                return 7;
            case 2:
                return 6;
            default:
                return null;

        }
    }

    public function someOtherFn(): bool
    {
        switch (self::C) {
            case 3:
                return true;
            default:
                return false;

        }
    }
}

$i = 2;

switch ($i++) {
    case 6:
        echo 6;
        break;
    case 5:
        echo 5;
        break;

    case 4:
        echo 4;
        break;

    case 3:
        echo 3;
        break;

    case 2:
        echo 2;
        break;

    case 1:
        echo 1;
        break;

    default:
    case 0:
        echo 0;
        break;

}

switch (['a' => $i++]) {
    case 6:
        echo 6;
        break;
    case 5:
        echo 5;
        break;
    default:
        echo 4;
        break;
}
