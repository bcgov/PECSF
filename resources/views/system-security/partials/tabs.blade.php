<ul class="menu nav nav-pills mb-3" id="pills-tab" >
    <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.schedule-job-audits') ? 'active' : ''}}"
        href="{{ route('system.schedule-job-audits.index') }}">Schedule Job Audit Logs</a>
    </li>

    <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.users') ? 'active' : ''}}"
        href="{{ route('system.users.index') }}">Users</a>
    </li>

    <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.auditing') ? 'active' : ''}}"
        href="{{ route('system.auditing.index') }}">Auditing</a>
    </li>

    <li class="nav-item">
      <a class="dropdown-item {{ str_contains( Route::current()->getName(), 'system.access-logs') ? 'active' : ''}}"
        href="{{ route('system.access-logs') }}">Access Logs</a>
    </li>

</ul>
