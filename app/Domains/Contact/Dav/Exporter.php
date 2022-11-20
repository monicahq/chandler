<?php

namespace App\Domains\Contact\Dav;

abstract class Exporter
{
    protected function escape($value): string
    {
        return ! empty((string) $value) ? trim((string) $value) : (string) null;
    }
}
