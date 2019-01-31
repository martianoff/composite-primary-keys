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
        return $this->hasMany(TestUser::class, 'organization_id', 'organization_id');
    }
}
