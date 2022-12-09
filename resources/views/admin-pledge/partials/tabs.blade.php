<ul class="menu nav nav-pills" id="pills-tab">
    <li class="nav-item nav-center-3">
        <a class="nav-link  {{ str_contains(Route::current()->getName(), 'admin-pledge.campaign') ? 'active' : '' }}"
            id="pills-home-tab" href="{{ route('admin-pledge.campaign.index') }}" role="tab"
            aria-controls="pills-home" aria-selected="true">Maintain EE Pledges</a>
    </li>
    <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.submission-queue') ? 'active' : '' }} {{ str_contains(Route::current()->getName(), 'admin-pledge.maintain-event') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('admin-pledge.maintain-event.index') }}" role="tab"
           aria-controls="pills-home" aria-selected="true"> Maintain Event Pledges</a>
    </li>
    <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.donate-now') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('admin-pledge.donate-now.index') }}" role="tab"
           aria-controls="pills-home" aria-selected="true">Maintain Donate Now Pledge</a>
    </li>
    <li class="nav-item nav-center-3">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.special-campaign') ? 'active' : '' }}"
           id="pills-home-tab" 
           href="{{ route('admin-pledge.special-campaign.index') }}" 
           role="tab"
           aria-controls="pills-home" aria-selected="true">Maintain Special Campaign Pledge</a>
    </li>
</ul>
