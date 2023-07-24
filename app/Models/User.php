<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasFactory, Notifiable, HasRoles;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'identity_provider',
        'keycloak_id',
        'samaccountname',
        'guid',
        'idir',
        'source_type',
        'idir_email_addr',
        'acctlock',
        'last_signon_at',
        'last_sync_at',
        'organization_id',
        'emplid',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_signon_at' => 'datetime',
    ];

    // Static function for gettig the list of status
    public static function source_type_options() {

        return self::select('source_type')
                ->distinct()
                ->whereNotNull('source_type')
                ->orderBy('source_type')
                ->pluck('source_type');
    }

    public function isVolunteer() {
        return $this->volunteer->count() > 0;
    }

    public function volunteer() {
        return $this->hasMany(Volunteer::class);
    }

    public function organization() 
    {
        return $this->belongsTo(Organization::Class, 'organization_id', 'id');
    }

    public function primary_job() 
    {
        return $this->belongsTo(EmployeeJob::Class, 'emplid', 'emplid')
                            ->where( function($query) {
                                $query->where('employee_jobs.empl_rcd', '=', function($q) {
                                        $q->from('employee_jobs as J2') 
                                            ->whereColumn('J2.emplid', 'employee_jobs.emplid')
                                            ->selectRaw('min(J2.empl_rcd)');
                                    });
                            })->withDefault();
    }

    public function active_employee_jobs() {
        return $this->hasMany(EmployeeJob::class, 'guid', 'guid')->whereNull('date_deleted');
    }
    
    public function access_logs() {
        return $this->hasMany(AccessLog::class, 'user_id', 'id');
    }


}
