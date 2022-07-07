
<ul class="nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'reporting.donation-upload') ? 'active' : ''}}"
        {{-- id="pills-home-tab"  --}}
        href="{{ route('reporting.donation-upload.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Pay Period Report</a>
    </li>
</ul>
