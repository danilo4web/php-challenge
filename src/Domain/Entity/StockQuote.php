<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity, Table(name: 'stock_market_quotes'), HasLifecycleCallbacks]
class StockQuote
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    public int $id;

    #[Column(type: 'json', nullable: false)]
    public string $json_response;

    #[Column(type: 'datetimetz', nullable: false)]
    public \DateTime|string $date;

    #[ManyToOne(targetEntity: User::class)]
    public User $user;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getJsonResponse(): string
    {
        return $this->json_response;
    }

    /**
     * @param string $json_response
     */
    public function setJsonResponse(string $json_response): void
    {
        $this->json_response = $json_response;
    }

    /**
     * @return \DateTime|string
     */
    public function getDate(): \DateTime|string
    {
        return $this->date;
    }

    #[PrePersist]
    public function setDate()
    {
        return $this->date = new \DateTime('now');
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
