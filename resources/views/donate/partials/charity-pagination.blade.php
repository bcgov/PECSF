{{-- @if ($charities->total())  --}}
<div class="pb-1">
    <small >{{ number_format($charities->total()) }} results</small>
</div>

@if ( $charities->total() ) 

    @foreach ($charities as $charity)
        <div class="d-flex list border-bottom">
            <div class="p-0">
                <i class="fas fa-hand-holding-heart text-info" style="visibility:hidden;"></i>
            </div>
            <div class="pl-2">
                <div class="d-flex flex-column ml-0">
                    <span>
                        <a href="#" class="charity-modal text-dark font-weight-bold" style="coxxxlor:#353535c4"
                            value="{{ $charity->id }}">
                            @php  
                                $text = $charity->capitalized_name();
                                foreach ($terms as $term) {
                                    $text = $term ? preg_replace('#' . preg_quote($term) . '#i', '<span class="text-danger">\\0</span>', $text) : $text;  
                                }
                            @endphp
                            {!!  $text !!}
                        </a>
                            
                    </span>
                    <small class="text-secondary">
                        {{-- $charity->designation_name() --}} 
                        {{ $charity->category_name }} | 
                        {{ $charity->city }} | 
                        {{ $charity->province }} | 
                        {{ $charity->country }}
                    </small>
                </div>
            </div>
          <div class="ml-auto p-2">
              <a class="charity-select-add" href="#" value="{{ $charity->id }}"
                  value-text="{{ $charity->charity_name }}">
                  <i class="fas fa-plus-circle fa-lg text-danger"></i>
                  
              </a>
          </div>
      </div>
    @endforeach

    <div id="pagination" class="my-3 d-flex justify-content-center">
        <small>
            {{ $charities->onEachSide(1)->links('pagination::bootstrap-4') }}
        </small>
    </div>

@endif
