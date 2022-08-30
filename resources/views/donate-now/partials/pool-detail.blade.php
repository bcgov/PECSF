<div class="container">
<ul class="list-group list-group-flush  ">
    @foreach ($charities as $pool_charity)    
        <li class="list-group-item">
            <div class="row">
                <div class="col-1"><img src="{{ asset('img/uploads/'.$pool_charity->image ) }}" 
                        class="card-img-top" alt="..."
                    min-height="100"></div>
                <div class="col-11">
                    <div class="row text-primary">    
                        <div class="col-10 h5">{{ $pool_charity->name }}</div>
                        
                    </div>
                    <div class="row text-dark">
                        <div class="col">{{ $pool_charity->description }}</div>
                    </div> 
                    <div class="row justify-content-between pt-2 text-secondary">
                        <div class="col-sm">{{ $pool_charity->charity->registration_number }}</div>
                        <div class="col-3">Allocation: {{ number_format($pool_charity->percentage,2) }}%</div>
                    </div> 
                </div>    
                
            </div>
        </li>
    @endforeach      
</ul>
</div>