<div class="row flex-row flex-nowrap mb-2" role="tablist" style="overflow-x: auto;">
    <div class="col-3 px-4 py-1 mr-2 border-bottom {{Route::current()->getName() == 'settings.regions.index' ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.regions.index')" style="">
            PECSF Maintain Oragnizations
        </x-button>
    </div>
    <div class="col-3 px-4 py-1 mr-2 border-bottom {{Route::current()->getName() == 'settings.regions.index' ? 'border-primary' : ''}}">
        <x-button role="tab" :href="route('settings.regions.index')" style="">
        PECSF Maintain Regions
        </x-button>
    </div>
    <div class="col-3 px-4 py-1 mr-2 border-bottom {{ str_contains( Route::current()->getName(), 'settings.regions.index' ) ? 'border-primary' : ''}}">
    <x-button role="tab" :href="route('settings.regions.index')" style="">
            PECSF Volunteer Training 
    </x-button>
    </div>
</div>
