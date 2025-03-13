<ul class="menu nav nav-pills" id="pills-tab">
{{-- 
    <li class="nav-item nav-center-3">        
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'admin-volunteering.profile') ? 'active' : ''}}"
            id="pills-home-tab" href="{{ route('admin-volunteering.profile.index') }}" role="tab"
            aria-controls="pills-home" aria-selected="true">Maintain Volunteer</a>
    </li> --}}

    <li class="nav-item dropdown">
        @php $active =  ( str_contains(Route::current()->getName(), 'admin-volunteering.profile') ||
                          str_contains(Route::current()->getName(), 'admin-volunteering.upload-profile')
                        ) ? 'active' : ''
        @endphp
        <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Volunteer Profile</a>
        <div class="dropdown-menu">
            <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'admin-volunteering.profile') ? 'active' : ''}}"
                href="{{ route('admin-volunteering.profile.index') }}">Maintain Volunteer Profile</a>
            <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'admin-volunteering.upload-profile') ? 'active' : ''}}"
                href="{{ route('admin-volunteering.upload-profile.index') }}">Upload Volunteer Profile</a>
        </div>
    </li>

    <li class="nav-item nav-center-3">
        <a class="nav-link  {{ str_contains(Route::current()->getName(), 'TBA') ? 'active' : '' }}"
            id="pills-home-tab" href="{{ '' }}" role="tab"
            aria-controls="pills-home" aria-selected="true">Training</a>
    </li>
    <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'TBA') ? 'active' : '' }} {{ str_contains(Route::current()->getName(), 'admin-pledge.maintain-event') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ '' }}" role="tab"
           aria-controls="pills-home" aria-selected="true">Communications</a>
    </li>

    
</ul>
