<?php

namespace App\Interface;

interface TokenFileProviderInterface
{
    public function getToken(): string;

}