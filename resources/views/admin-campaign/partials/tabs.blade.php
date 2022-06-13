{{-- <div class="row flex-row flex-nowrap mb-2 text-sm" role="tablist" style="overflow-x: hidden;">
    <div class="col-2 text-center px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.campaignyears') ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.campaignyears.index')" style="">
            Campaign Years
        </x-button>
    </div>
    <div class="col-2 text-center px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.organizations') ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.organizations.index')" style="">
            Organizations
        </x-button>
    </div>
    <div class="col-2 text-center px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.regions') ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.regions.index')" style="">
            Regions
        </x-button>
    </div>
    <div class="col-2 text-center px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.fund-supported-pools' ) ? 'border-primary' : ''}}">
    <x-button role="tab" :href="route('settings.fund-supported-pools.index')" style="">
            Fund Supported Pools
    </x-button>
    </div>
    <div class="col-2 text-center px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.administrators' ) ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.administrators.index')" style="">
                Administrators
        </x-button>
        </div>

</div> --}}

<ul class="nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'settings.campaignyears') ? 'active' : ''}}"
        {{-- id="pills-home-tab"  --}}
        href="{{ route('settings.campaignyears.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Campaign Year</a>
    </li>

    <li class="nav-item">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'settings.fund-supported-pools') ? 'active' : '' }}"
        {{-- id="pills-profile-tab"  --}}
        href="{{ route('settings.fund-supported-pools.index') }}"  aria-controls="pills-profile" aria-selected="false">Fund Supported Pools</a>
    </li>

    <li class="nav-item dropdown">
        @php $active =  ( str_contains(Route::current()->getName(), 'settings.business-units') ||
                          str_contains(Route::current()->getName(), 'settings.organizations') ||
                          str_contains(Route::current()->getName(), 'settings.regions')
                        ) ? 'active' : ''
        @endphp
        <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Foundation Tables</a>
        <div class="dropdown-menu">
          <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'settings.business-units') ? 'active' : ''}}"
                href="{{ route('settings.business-units.index') }}">Business Units</a>
          <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'settings.organizations') ? 'active' : ''}}"
                href="{{ route('settings.organizations.index') }}">Organization</a>
          <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'settings.regions') ? 'active' : ''}}"
                href="{{ route('settings.regions.index') }}">Regions</a>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'settings.administrators') ? 'active' : '' }}"
         {{-- id="pills-contact-tab"  --}}
          href="{{ route('settings.administrators.index') }}"  aria-controls="pills-contact" aria-selected="false">Adminstrators</a>
      </li>
    <li class="nav-item">
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'settings.charity-list-maintenance') ? 'active' : '' }}"
           {{-- id="pills-contact-tab"  --}}
           href="{{ route('settings.charity-list-maintenance.index') }}"  aria-controls="pills-contact" aria-selected="false">CRA List Maintenance</a>
    </li>

  </ul>
