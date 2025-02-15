<?php

namespace Alemian95\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Alemian95\LaravelEncryptableAttributes\HasEncryptableAttributes;

class FakeUser extends Model
{
    use HasEncryptableAttributes;

    protected $encryptable = ['secret'];
    protected $fillable = ['name', 'secret'];
}