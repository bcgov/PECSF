<ul class="menu nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
      <a class="nav-link  {{ str_contains( Route::current()->getName(), 'system.schedule-job-audits') ? 'active' : ''}}"
        href="{{ route('system.schedule-job-audits.index') }}">Schedule Job Audit Logs</a>
    </li>

    {{-- <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.auditing') ? 'active' : ''}}"
        href="{{ route('system.auditing.index') }}">Auditing</a>
    </li> --}}

    <li class="nav-item dropdown">
      @php $active =  ( str_contains(Route::current()->getName(), 'system.users') ||
                        str_contains(Route::current()->getName(), 'system.administrators')
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Manage Users</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.users') ? 'active' : ''}}"
            href="{{ route('system.users.index') }}">Users</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.administrators') ? 'active' : ''}}"
            href="{{ route('system.administrators.index') }}">Administrators</a>
      </div>
    </li>

    <li class="nav-item dropdown">
      @php $active =  ( str_contains(Route::current()->getName(), 'system.page-visits-overview') ||
                        str_contains(Route::current()->getName(), 'system.transaction-counts-overview')  ||
                        str_contains(Route::current()->getName(), 'system.transaction-timings')
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
          Activity Analytics</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.page-visits-overview') ? 'active' : ''}}"
            href="{{ route('system.page-visits-overview') }}">Page Visits Overview</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.transaction-counts-overview') ? 'active' : ''}}"
          href="{{ route('system.transaction-counts-overview') }}">Transaction Counts Overview</a>
          <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.transaction-timings') ? 'active' : ''}}"
            href="{{ route('system.transaction-timings') }}">Transaction Timings</a>
      </div>
    </li>

    <li class="nav-item dropdown">
      @php $active =  ( str_contains(Route::current()->getName(), 'system.auditing') ||
                        str_contains(Route::current()->getName(), 'system.export-audits')
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Auditing</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.auditing') ? 'active' : ''}}"
            href="{{ route('system.auditing.index') }}">Table Audit Log</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.export-audits') ? 'active' : ''}}"
            href="{{ route('system.export-audits.index') }}">Export Audit Log</a>
      </div>
    </li>

    <li class="nav-item">
      <a class="nav-link  {{ str_contains( Route::current()->getName(), 'system.access-logs') ? 'active' : ''}}"
        href="{{ route('system.access-logs') }}">Access Logs</a>
    </li>

    <li class="nav-item dropdown">
      @php $active =  ( str_contains(Route::current()->getName(), 'system.settings') ||
                        str_contains(Route::current()->getName(), 'system.announcement')
                      ) ? 'active' : ''
      @endphp
      <a class="nav-link dropdown-toggle {{ $active }}" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Settings</a>
      <div class="dropdown-menu">
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.settings') ? 'active' : ''}}"
            href="{{ route('system.settings.index') }}">Planned Maintenance</a>
        <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.announcement') ? 'active' : ''}}"
            href="{{ route('system.announcement.index') }}">Announcement</a>
      </div>
    </li>

  </ul>
