<?php

namespace LaTevaWeb\Translatable\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaTevaWeb\Translatable\Traits\Translatable;

class FakeSoftDeletes extends Model
{
    use Translatable, SoftDeletes;

    protected $fillable = ['greeting'];
    protected $guarded = ['id'];
    public static $translatable = ['greeting'];
}
