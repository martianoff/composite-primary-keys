<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestUser extends Model
{
    use \MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;

    protected $table = 'users';

    protected $primaryKey = [
        'user_id',
        'organization_id',
    ];

    protected $fillable = [
        'name', 'counter', 'organization_id',
    ];

    public function organization()
    {
        return $this->belongsTo('TestOrganization', 'organization_id', 'organization_id');
    }
}
