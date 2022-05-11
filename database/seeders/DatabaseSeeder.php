<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;
use \App\Models\Institution;
use \App\Models\MinimumQuantity;
use \App\Models\Assistent;
use \App\Models\Ambulancier;
use \App\Models\InvoiceItem;
use \App\Models\AvizEntry;
use \App\Models\AvizEntryItem;
use \App\Models\Checklist;
use \App\Models\ChecklistItem;
use \App\Models\Consumption;
use \App\Models\Returning;
use \App\Models\ReturningItem;
use \App\Models\Transfer;
use \App\Models\TransferItem;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory(1)->create();
        Institution::factory(1)->create();
        MinimumQuantity::factory(3)->create();
        Assistent::factory(2)->create();
        Ambulancier::factory(2)->create();
        InvoiceItem::factory(5)->create();
        AvizEntry::factory(3)->create();
        AvizEntryItem::factory(3)->create();
        Consumption::factory(3)->create();
        ChecklistItem::factory(3)->create();
        Returning::factory(3)->create();
        ReturningItem::factory(3)->create();
        Transfer::factory(3)->create();
        TransferItem::factory(3)->create();
    }
}