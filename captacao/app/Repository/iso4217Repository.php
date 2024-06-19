<?php

namespace App\Repository;

use App\Models\iso4217;

class iso4217Repository
{
    public function updateOrCreate($attributes, $values)
    {
        return iso4217::updateOrCreate($attributes, $values);
    }
}