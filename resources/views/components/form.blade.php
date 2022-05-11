@props(['action' => '', 'method' => 'POST', 'id' => 'action-form', 'class' => 'forms-sample'])

<form action="{{ $action }}" method="{{ $method }}" id="{{ $id }}" class="{{ $class }}">
    @csrf
    {{ $slot }}
</form>