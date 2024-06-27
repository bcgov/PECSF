<ul class="menu nav nav-pills" id="pills-tab">
    {{-- <li class="nav-item nav-center-3">
        <a class="nav-link  {{ str_contains(Route::current()->getName(), 'settings.challenge') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('settings.challenge') }}" role="tab"
           aria-controls="pills-home" aria-selected="true">Statistics Page</a>
    </li> --}}
    <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'settings.volunteering') ? 'active' : '' }} {{ str_contains(Route::current()->getName(), 'admin-pledge.maintain-event') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('settings.volunteering') }}" role="tab"
           aria-controls="pills-home" aria-selected="true"> Volunteering</a>
    </li>
    {{-- <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.donate-now') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('admin-pledge.donate-now.index') }}" role="tab"
           aria-controls="pills-home" aria-selected="true">Other Tab 1</a>
    </li> --}}
    {{-- <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.special-campaign') ? 'active' : '' }}"
           id="pills-home-tab"
           href="{{ route('admin-pledge.special-campaign.index') }}"
           role="tab"
           aria-controls="pills-home" aria-selected="true">Other Tab 2</a>
    </li> --}}
</ul>
