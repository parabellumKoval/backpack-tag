<?php

namespace Backpack\Tag\Facades;

use Illuminate\Support\Facades\Facade;

class Tag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'tag';
    }
}
