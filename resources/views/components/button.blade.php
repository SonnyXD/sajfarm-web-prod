@props(['type' => 'submit', 'class' => 'btn btn-primary me-2', 'id' => 'print', 'disabled' => ''])

<button type="{{$type}}" class="{{$class}}" id="{{$id}}" {{$disabled}}>{{$slot}}</button>
