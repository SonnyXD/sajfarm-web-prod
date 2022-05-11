<x-layout>
    <x-subgestions :categories="$categories"/>
    <x-medstable :meds="$meds" :current_cat="$current_category" :current_sub="$current_sub" :items="$items"/>
</x-layout>