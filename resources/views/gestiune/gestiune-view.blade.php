<x-layout>
    <x-subgestions/>
    <x-medstable :category_name="$current_category->name" :items="$all_items" :item_stock="$items"/>
</x-layout>
<script src="/js/inventories.js"></script>