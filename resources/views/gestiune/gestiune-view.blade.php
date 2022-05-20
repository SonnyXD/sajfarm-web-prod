<x-layout>
    <x-subgestions/>
    <x-medstable :category_name="$current_category->name" :items="$all_items" :item_stock="$items" :inventory_name="$inventory_name" :inventory_id="$inventory_id" :minimum_quantities_farm="$minimum_quantities_farm" :minimum_quantities_stoc3="$minimum_quantities_stoc3"/>
</x-layout>
<script src="/js/inventories.js"></script>