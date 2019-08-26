<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestRole extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $primaryKey = 'role_id';

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(TestBinaryUser::class, 'role_id', 'role_id');
    }

    public function hex_users()
    {
        return $this->hasMany(TestBinaryUserHex::class, 'role_id', 'role_id');
    }
}
