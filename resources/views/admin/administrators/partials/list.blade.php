
@if ($message = Session::get('success'))

    <div class="alert alert-success alert-dismissible">
        <a href="#" class="close" style="text-decoration: none;"  data-dismiss="alert" aria-label="close">&times;</a>
        <strong>Success!</strong> {{ $message }}
    </div>
    
@endif

@error('user')
<div class="alert alert-danger alert-dismissible">
    <a href="#" class="close" style="text-decoration: none;"  data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Error!</strong> {{  $message }}
</div>    
@enderror

<div class="card">
    
    <div class="d-flex mt-3">
        <h4></h4>
        <div class="px-4">
            
            <form action="{{ route('administrators.store') }}" class="form-inline" method="post">
                @csrf
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                      <label  class="col-form-label">Assign User</label>
                    </div>
                    <div class="col-auto">
                      
                      <select class="form-control select2" style="height: 28px; width:300px;" name="user_id" id="user_id">
                     {{--    
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                     --}}    
                      </select>

                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-secondary"  type="submit" >Add</button>
                    </div>
                </div>
              </form>

        </div>    
        <div class="flex-fill"></div>
        <div class="px-4">
            
        </div>
    </div>


    <div class="card-body">
                <table class="table table-sm table-bordered rounded">
                    <thead>
                    <tr class="text-center bg-light">
                        <th>Administrator Name</th>
                        <th>Email</th>
                        <th width="280px">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($administrators as $administrator)
                     
                            <tr class="text-center">
                                <td class="text-left">{{$administrator->name}} </td>
                                <td class="text-left">{{$administrator->email }}</td>

                                <td>
                                    <a class="btn btn-danger" onclick="return confirm('Are you sure to remove user {{ $administrator->name }} ?')" href="/administrators/{{ $administrator->id }}/delete""><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        
                        @endforeach

                    </tbody>
                </table>
            </div>
    </div>

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.1.1/dist/select2-bootstrap-5-theme.min.css" />
    
     <style>
        .select2-container .select2-selection--single 
        {
                height: 38px;  !important;
        }
     </style>

@endpush
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $('#user_id').select2({

        ajax: {
            url: '/administrators/users'
            , dataType: 'json'
            , delay: 250
            , data: function(params) {
                var query = {
                    'q': params.term
                , }
                return query;
            }
            , processResults: function(data) {
                return {
                    results: data
                    };
            }
            , cache: false
        }
    });

    </script>
@endpush
