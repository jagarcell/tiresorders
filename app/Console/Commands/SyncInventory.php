<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Inventory;
use Illuminate\Http\Request;


class SyncInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncInventory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronise QuickBooks With The App Inventory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        (new Inventory())->SyncronizeInventories(new Request());
    }
}
