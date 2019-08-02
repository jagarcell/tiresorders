<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QbCustomers extends Model
{
    //
    public function CustomersSync($qbCustomers)
    {
    	# code...
    	DB::table('qb_customers')->truncate();
    	foreach ($qbCustomers as $key => $qbCustomer) {
    		# code...
    		$QbCustomers = new QbCustomers();
    		$QbCustomers->qb_customer_id = $qbCustomer->Id;
    		$QbCustomers->DisplayName = $qbCustomer->DisplayName;
    		try {
	    		$QbCustomers->save();
    		} catch (\Exception $e) {
    			
    		}
    	}
    }

    public function Customer($qbCustomerId)
    {
    	# code...
    	return $this->where('qb_customer_id', $qbCustomerId)->get();
    }
}
