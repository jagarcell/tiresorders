<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QbItemsIds extends Model
{
    //
    public function ClearItemsId()
    {
    	# code...
    	DB::table('qb_items_ids')->truncate();
    }
}
