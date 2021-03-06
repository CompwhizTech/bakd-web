<?php

namespace BAKD;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Spatie\Permission\Traits\HasRoles;
use \Spatie\Permission\Traits\HasPermissions;


class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    // TODO: Rename me
    public function bountyClaims()
    {
        return $this->hasMany('BAKD\BountyClaim');
    }

    public function bounties()
    {
        return $this->belongsTo('BAKD\Bounty');
    }

    public function getGravatar($size = '125')
    {
        $email = md5(strtolower(trim($this->email)));
        return "//www.gravatar.com/avatar/{$email}?s={$size}";
    }

    // TODO: Finish me.
    public function getFollowingCount()
    {
        return number_format(rand(200, 5000));
    }

    // TODO: Finish me.
    public function getFollowerCount()
    {
        return number_format(rand(100, 100000));
    }

    // TODO: Refactor into single query
    public function totalCoinsEarned()
    {
        $coins = 0;
        $query = \BAKD\BountyClaim::with('bounty')->where('user_id', $this->id)->where('confirmed', 1)->get();
        if (!$query->isEmpty()) {
            foreach ($query as $claim) {
                if (!$claim->bounty->isStakeRewardBounty()) {
                    $coins += $claim->bounty->reward;
                }
            }
        }
        return number_format($coins);
    }

    // TODO: Refactor into single query
    public function totalStakesEarned()
    {
        $stakes = 0;
        $query = \BAKD\BountyClaim::with('bounty')->where('user_id', $this->id)->where('confirmed', 1)->get();
        if (!$query->isEmpty()) {
            foreach ($query as $claim) {
                if ($claim->bounty->isStakeRewardBounty()) {
                    $stakes += $claim->stakes_received;
                }
            }
        }
        return number_format($stakes);
    }

    // TODO: Refactor claim status' into enum
    public function totalClaimsApproved()
    {
        $claims = 0;
        $claims = \BAKD\BountyClaim::with('bounty')->where('user_id', $this->id)->where('confirmed', 1)->count();
        return number_format($claims);
    }

    // TODO: Refactor claim status' into enum
    public function totalClaimsPending()
    {
        $claims = 0;
        $claims = \BAKD\BountyClaim::with('bounty')->where('user_id', $this->id)->where('confirmed', 0)->count();
        return number_format($claims);
    }

    // TODO: Refactor claim status' into enum
    public function totalClaimsRejected()
    {
        $claims = 0;
        $claims = \BAKD\BountyClaim::with('bounty')->where('user_id', $this->id)->where('confirmed', 2)->count();
        return number_format($claims);
    }

    // Get all user entries into a specific bounty
    public function getClaimsByBountyId($bountyId)
    {
        return $this->with('bountyClaims', function($query) {
            $query->where('bounty_id', $bountyId);
        })->get();
    }
}
