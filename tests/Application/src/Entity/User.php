<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Application\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Column]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    private int $id = -1;

    #[ORM\Column]
    public string $firstname;

    #[ORM\Column]
    public string $lastname;

    public function __construct(string $firstname, string $lastname)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
