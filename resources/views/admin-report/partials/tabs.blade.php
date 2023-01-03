<ul class="menu nav nav-pills mb-3" id="pills-tab" >

    <li class="nav-item dropdown">
      @php $active =  ( str_contains(Route::current()->getName(), 'reporting.donation-upload') ||
                        str_contains(Route::current()->getName(), 'reporting.donation-data') 
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Pay Period Report</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.donation-upload') ? 'active' : ''}}"
              href="{{ route('reporting.donation-upload.index') }}">Upload Donation Data</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'reporting.donation-data') ? 'active' : ''}}"
              href="{{ route('reporting.donation-data.index') }}">Review Donation Data </a>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link {{ str_contains( Route::current()->getName(), 'reporting.eligible-employees') ? 'active' : ''}}"
        {{-- id="pills-home-tab"  --}}
        href="{{ route('reporting.eligible-employees.index') }}" role="tab" aria-controls="pills-home" aria-selected="true">Eligible Employee Report</a>
    </li>

</ul>


