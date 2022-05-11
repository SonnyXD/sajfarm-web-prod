@props(['class' => 'form-control', 'id' => '', 'name' => ''])

<select id="{{$id}}" class="{{$class}}" name="{{$name}}">
    {{ $slot }}
</select>
