<?php

namespace App\UseCase\TextSearch;



use Doctrine\Common\Collections\Collection;

class TextResultDto
{
    public function __construct(
        public Collection $value,
    )
    {
    }
}