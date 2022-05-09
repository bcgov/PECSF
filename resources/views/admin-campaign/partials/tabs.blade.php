<div class="row flex-row flex-nowrap mb-2" role="tablist" style="overflow-x: auto;">
    <div class="col-2 px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.campaignyears') ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.campaignyears.index')" style="">
            Campaign Years
        </x-button>
    </div>
    <div class="col-2 px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.regions') ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.regions.index')" style="">
            Regions
        </x-button>
    </div>
    <div class="col-3 px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.fund-supported-pools' ) ? 'border-primary' : ''}}">
    <x-button role="tab" :href="route('settings.fund-supported-pools.index')" style="">
            Fund Supported Pools 
    </x-button>
    </div>
    <div class="col-3 px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.administrators' ) ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.administrators.index')" style="">
                Administrators 
        </x-button>
        </div>
    
</div>
