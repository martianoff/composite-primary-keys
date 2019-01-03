<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestOrganization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany('TestUser', 'user_id', 'user_id');
    }
}
