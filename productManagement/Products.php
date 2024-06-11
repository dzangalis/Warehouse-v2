<?php

namespace productManagement;

use Carbon\Carbon;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

class Products implements jsonSerializable
{
    private string $id;
    private string $productName;
    private float $productPrice;
    public int $units;
    private Carbon $creationTime;
    public Carbon $updateTime;
    private Carbon $expirationDate;

    public function __construct(
        string  $id,
        string  $productName,
        float   $productPrice,
        int     $units,
        ?string $creationTime = null,
        ?string $updateTime = null,
        ?string $expirationDate = null
    )
    {
        $this->id = Uuid::uuid4()->toString();
        $this->productName = $productName;
        $this->productPrice = $productPrice;
        $this->units = $units;
        $this->creationTime = $creationTime ? Carbon::parse($creationTime) : Carbon::now();
        $this->updateTime = $updateTime ? Carbon::parse($updateTime) : Carbon::now();
        $this->expirationDate = $expirationDate ? Carbon::parse($expirationDate) : Carbon::now();
    }

    public
    function id(): string
    {
        return $this->id;
    }

    public
    function productName(): string
    {
        return $this->productName;
    }

    public
    function productPrice(): float
    {
        return $this->productPrice;
    }

    public
    function units(): int
    {
        return $this->units;
    }

    public
    function creationTime(): Carbon
    {
        return $this->creationTime;
    }

    public
    function updateTime(): Carbon
    {
        return $this->updateTime;
    }

    public
    function expirationDate(): Carbon
    {
        return $this->expirationDate;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id(),
            'productName' => $this->productName(),
            'productPrice' => $this->productPrice(),
            'units' => $this->units(),
            'creationTime' => $this->creationTime()->format('Y-m-d H:i:s'),
            'updateTime' => $this->updateTime()->format('Y-m-d H:i:s'),
            'expirationDate' => $this->expirationDate()->format('Y-m-d H:i:s'),
        ];
    }
}