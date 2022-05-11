@props(['type' => 'button', 'class' => 'btn btn-info', 'id' => '', 'data_bs_toggle' => 'modal', 'data_bs_target' => '#meds-modal', 'data_bs_dismiss' => ''])

<button type="{{$type}}" class="{{$class}}" id="{{$id}}" data-bs-toggle="{{$data_bs_toggle}}" data-bs-target="{{$data_bs_target}}">{{$slot}}</button>
