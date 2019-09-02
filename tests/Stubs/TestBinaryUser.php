<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;

class TestBinaryUser extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'binary_users';

    public $timestamps = false;

    protected $binaryColumns = [
        'user_id',
    ];

    protected $primaryKey = [
        'user_id',
        'organization_id',
    ];

    protected $fillable = [
        'name',
    ];

    public function organization()
    {
        return $this->belongsTo(TestOrganization::class, 'organization_id', 'organization_id');
    }

    public function role()
    {
        return $this->belongsTo(TestRole::class, 'role_id', 'role_id');
    }

    public function binary_role()
    {
        return $this->belongsTo(TestBinaryRole::class, 'binary_role_id', 'role_id');
    }

    public function hex_binary_role()
    {
        return $this->belongsTo(TestBinaryRoleHex::class, 'binary_role_id', 'role_id');
    }
}
