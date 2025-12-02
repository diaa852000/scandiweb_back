<?php

namespace App\Repository;

use App\Repository\BaseRepository;

class CurrencyRepository extends BaseRepository
{
    public function getCurrencyByLabel(string $label): ?object
    {
        return $this->findOneBy(['label' => $label]);
    }
}