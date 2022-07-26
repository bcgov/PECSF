<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

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
        'employee_job_id',
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
    ];

    // Static function for gettig the list of status
    public static function source_type_options() {

        return self::select('source_type')
                ->distinct()
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
        return $this->belongsTo(EmployeeJob::Class, 'employee_job_id', 'id')->withDefault();
    }

    public function active_employee_jobs() {
        return $this->hasMany(EmployeeJob::class, 'guid', 'guid')->whereNull('date_deleted');
    }
    
    public function access_logs() {
        return $this->hasMany(AccessLog::class, 'user_id', 'id');
    }


}
