@props(['text' => '', 'percent' => 50, 'color' =>'primary'])

<div class="d-flex align-items-center my-2">    
    <div class="bg-gray" style="height: 40px; width:calc(100% - 70px); border-top-right-radius: 20px;border-bottom-right-radius: 20px">
        <div class="d-flex align-items-center bg-{{$color}}" style="width: {{$percent}}%; height: 40px; border-top-right-radius: 20px;border-bottom-right-radius: 20px">
            <span class="pl-3">
                {{$text}}
            </span>
        </div>
    </div>
    <div class="d-flex flex-fill justify-content-center">
        <strong>{{$percent}}%</strong>
    </div>
</div>