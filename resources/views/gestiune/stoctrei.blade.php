<x-layout>
    <x-subgestions :categories="$categories"/>
    <x-medstable :meds="$meds" :current_cat="$current_category" :items="$items"/>
</x-layout>