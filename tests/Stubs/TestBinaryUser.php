<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestBinaryUser extends Model
{
    use \MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;

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
        return $this->belongsTo('TestOrganization', 'organization_id', 'organization_id');
    }
}
