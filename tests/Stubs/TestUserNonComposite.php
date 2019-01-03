<?php

namespace MaksimM\CompositePrimaryKeys\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class TestUserNonComposite extends Model
{
    use \MaksimM\CompositePrimaryKeys\Http\Traits\HasCompositePrimaryKey;

    protected $table = 'non_composite_users';

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
    ];

    public function organization()
    {
        return $this->belongsTo('TestOrganization', 'organization_id', 'organization_id');
    }
}
