<table class="table">
    <tbody>
        <tr>
            <td class="font-weight-bold text-right">Type : </td>
            <td>{{ $user->source_type }}</td>
          </tr>    
      <tr>
        <td class="font-weight-bold text-right">User ID : </td>
        <td>{{ $user->id }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">User Name : </td>
        <td>{{ $user->name }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Employee ID : </td>
        <td>{{ $user->primary_job->emplid }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Email : </td>
        <td>{{ $user->primary_job->email }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">IDIR : </td>
        <td>{{ $user->idir }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Business Unit : </td>
        <td>{{ $user->primary_job->business_unit }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Department : </td>
        <td>{{ $user->primary_job->business_unit }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Organization : </td>
        <td>{{ $user->primary_job->organization }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Last signon at : </td>
        <td>{{ $user->last_signon_at }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Last sync at : </td>
        <td>{{ $user->last_sync_at }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Created at : </td>
        <td>{{ $user->created_at }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Updated at : </td>
        <td>{{ $user->updated_at }}</td>
      </tr>
      {{-- <tr>
        <td class="font-weight-bold text-right">program : </td>
        <td>{{ $user->primary_job->program }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Division : </td>
        <td>{{ $user->primary_job->level2_division }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Branch : </td>
        <td>{{ $user->primary_job->level3_branch }}</td>
      </tr>
      <tr>
        <td class="font-weight-bold text-right">Level 4 : </td>
        <td>{{ $user->primary_job->level4 }}</td>
      </tr> --}}
    </tbody>
  </table>