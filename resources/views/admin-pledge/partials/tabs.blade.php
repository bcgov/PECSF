<ul class="nav nav-pills mb-3" id="pills-tab">
    <li class="nav-item">
        <a class="nav-link {{ str_contains(Route::current()->getName(), 'admin-pledge.campaign') ? 'active' : '' }}"
            id="pills-home-tab" href="{{ route('admin-pledge.campaign.index') }}" role="tab"
            aria-controls="pills-home" aria-selected="true">Campaign Pledge</a>
    </li>

</ul>
