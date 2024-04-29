<ul class="menu nav nav-pills" id="pills-tab">

    <li class="nav-item nav-center-3">        
        <a class="nav-link {{ str_contains( Route::current()->getName(), 'admin-volunteering.profile') ? 'active' : ''}}"
            id="pills-home-tab" href="{{ route('admin-volunteering.profile.index') }}" role="tab"
            aria-controls="pills-home" aria-selected="true">Maintain Volunteer</a>
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
