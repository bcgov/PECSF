<?php

namespace App\AuditResolvers;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;
use Jenssegers\Agent\Agent;

class UserAgentResolver implements Resolver
{
    public static function resolve(Auditable $auditable)
    {
        // TODO: Implement resolve() method.
        $agent = new Agent();

        $browser = $agent->browser();
        $browser_ver = $agent->version($browser);

        $platform = $agent->platform();
        $platform_ver = $agent->version($platform);
        $device_type = $agent->deviceType(); 

        return ($browser . ' (' . $browser_ver . ') - ' . $platform . ' (' . $platform_ver . ') - ' . $device_type );
    }
}
