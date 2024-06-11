<?php

namespace users;

class User
{
    private string $accessCode;
    private string $productName;

    public function __construct(string $accessCode, string $productName)
    {
        $this->accessCode = $accessCode;
        $this->productName = $productName;
    }

    public function getAccessCode(): string
    {
        return $this->accessCode;
    }

    public function getName(): string
    {
        return $this->productName;
    }
}