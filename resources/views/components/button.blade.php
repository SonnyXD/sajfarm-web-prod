@props(['type' => 'submit', 'class' => 'btn btn-primary me-2', 'id' => 'print'])

<button type="{{$type}}" class="{{$class}}" id="{{$id}}">{{$slot}}</button>
