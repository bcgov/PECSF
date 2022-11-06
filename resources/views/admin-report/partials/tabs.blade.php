
<ul class="nav nav-pills" id="pills-tab" >
    <li class="nav-item nav-center-4">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'reporting.donation-upload') ? 'active' : ''}}"
        {{-- id="pills-home-tab"  --}}
        href="{{ route('reporting.donation-upload.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Pay Period Report</a>
    </li>
    <li class="nav-item nav-center-4">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'reporting.donation-data') ? 'active' : ''}}"
        {{-- id="pills-home-tab"  --}}
        href="{{ route('reporting.donation-data.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Donation Data</a>
    </li>
</ul>


