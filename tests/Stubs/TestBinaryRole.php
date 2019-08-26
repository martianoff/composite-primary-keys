<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;

class TestBinaryRole extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'binary_roles';

    public $timestamps = false;

    protected $primaryKey = 'role_id';

    protected $binaryColumns = [
        'role_id',
    ];

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(TestBinaryUser::class, 'binary_role_id', 'role_id');
    }

    public function hex_users()
    {
        return $this->hasMany(TestBinaryUserHex::class, 'binary_role_id', 'role_id');
    }
}
