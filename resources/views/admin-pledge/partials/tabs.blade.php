<ul class="nav nav-pills justify-content-center" id="pills-tab">
    <li class="nav-item nav-center-4">
        <a class="nav-link  {{ str_contains(Route::current()->getName(), 'admin-pledge.campaign') ? 'active' : '' }}"
            id="pills-home-tab" href="{{ route('admin-pledge.campaign.index') }}" role="tab"
            aria-controls="pills-home" aria-selected="true">PECSF Maintain EE Pledges</a>
    </li>
    <li class="nav-item nav-center-4">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.maintain-event') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('admin-pledge.maintain-event.index') }}" role="tab"
           aria-controls="pills-home" aria-selected="true">PECSF Maintain Event Pledges</a>
    </li>
    <li class="nav-item nav-center-4">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.donate-now') ? 'active' : '' }}"
           id="pills-home-tab" href="{{ route('admin-pledge.campaign.index') }}" role="tab"
           aria-controls="pills-home" aria-selected="true">PECSF Maintain Donate Now Pledge</a>
    </li>
</ul>
