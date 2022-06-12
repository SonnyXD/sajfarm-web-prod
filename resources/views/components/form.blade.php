@props(['action' => '', 'method' => 'POST', 'id' => 'action-form', 'class' => 'forms-sample', 'target' => ''])

<form action="{{ $action }}" method="{{ $method }}" id="{{ $id }}" class="{{ $class }}" target="{{$target}}">
    @csrf
    {{ $slot }}
</form>