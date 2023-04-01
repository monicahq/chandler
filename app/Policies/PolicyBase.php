<?php

namespace App\Policies;

use Illuminate\Database\Eloquent\Model;

abstract class PolicyBase
{
    protected function id($model)
    {
        return is_subclass_of($model, Model::class) ? $model->getKey() : $model;
    }
}
