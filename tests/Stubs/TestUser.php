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
        'name', 'counter', 'organization_id', 'referred_by_user_id', 'referred_by_organization_id',
    ];

    public function organization()
    {
        return $this->belongsTo(TestOrganization::class, 'organization_id', 'organization_id');
    }

    public function referrer()
    {
        return $this->belongsTo(self::class, [
            'referred_by_user_id',
            'referred_by_organization_id',
        ], [
            'user_id',
            'organization_id',
        ]);
    }

    public function wrongConfiguredReferrer()
    {
        return $this->belongsTo(self::class, [
            'referred_by_user_id',
            'referred_by_organization_id',
        ], [
            'user_id',
        ]);
    }

    public function automaticReferrer()
    {
        return $this->belongsTo(self::class);
    }
}
