$(document).ready(function registerReady() {
	// body...

	checkRegisteredUserTypes()

	getPriceLevels()

	getPriceListsHeaders()

	$('#pricelevels_id').change(changepricelevelsid)

	$('#pricelists_id').change(changepricelistsid)

	$('#contentDiv').show()
})

function changepricelevelsid() {
	// body...
	if($('#pricelevels_id')[0].value != -1){
		$('#pricelists_id')[0].value = -1
	}
}

function changepricelistsid() {
	// body...
	if($('#pricelists_id')[0].value != -1){
		$('#pricelevels_id')[0].value = -1
	}
}

function typeChange(select) {
	// body...
	setUserLayout(select.options[select.selectedIndex].value)
}

function setUserLayout(userType) {
	// body...
	if(userType == 'admin'){
		$('#qbNameDiv').hide()
		$('#nameDiv').show()
		$('#pricelevelsDiv').hide()
		$('#pricelistsDiv').hide()
		var selectCustomer = $('#qb_customer_id')
		selectCustomer.selectedIndex = -1
		$('#name').val('')
		selectCustomer[0].value = -1
		$('#pricelevels_id')[0].selectedIndex = -1
	}
	else{
		$('#qbNameDiv').show()
		$('#qb_customer_id')[0].selectedIndex = -1
		$('#nameDiv').hide()
		$('#pricelevelsDiv').show()
		$('#pricelistsDiv').show()
		customers()
	}
}

function checkRegisteredUserTypes() {
	// body...
	$.get('/findusersbytype', 
		{type:'admin'}, 
		function (data, status) {
		// body...
			if(data.length == 0){
				$('#type')[0].innerHTML =
	                '<option value="admin">Admin</option>'
	            setUserLayout('admin')            }
            else{
				$('#type')[0].innerHTML =
	                '<option value="admin">Admin</option>' +
	                '<option value="user">User</option>'
                $('#type').val('user')
                setUserLayout('user')
            }
		}
	)
}

function qbCustomerSelectChange(select) {
	// body...
	$('#address').val('')
	$('#name').val(select.options[select.selectedIndex].innerHTML)
	var qbCustomerId = select.options[select.selectedIndex].value
	$.get('/customer', {qbcustomerid:qbCustomerId}, function customerCallBack(data, status) {
		// body...
		var Customer = JSON.parse(data.Customer)[0]
		if(Customer){
				if(Customer.ShipAddr)
				{
					var PostalCode = Customer.ShipAddr.PostalCode
					if(Customer.ShipAddr.PostalCodeSuffix){
						PostalCode = PostalCode + '-' + Customer.ShipAddr.PostalCodeSuffix
					}
					var address = ''
					if(Customer.ShipAddr.Line1){
						address = address + Customer.ShipAddr.Line1
					}
					if(Customer.ShipAddr.Line2){
						address = address + Customer.ShipAddr.Line2
					}
					if(Customer.ShipAddr.Line3){
						address = address + Customer.ShipAddr.Line3
					}
					if(Customer.ShipAddr.Line4){
						address = address + Customer.ShipAddr.Line4
					}
					if(Customer.ShipAddr.Line5){
						address = address + Customer.ShipAddr.Line5
					}
					if(Customer.ShipAddr.City){
						address = address + ','	+ Customer.ShipAddr.City
					}
					if(Customer.ShipAddr.CountrySubDivisionCode){
						address = address + ',' + Customer.ShipAddr.CountrySubDivisionCode
					}
					if(Customer.ShipAddr.PostalCode){
						address = address + ',' + Customer.ShipAddr.PostalCode
					}
					if(Customer.ShipAddr.PostalCodeSuffix){
						address = address + '-' + Customer.ShipAddr.PostalCodeSuffix
					}
					if(Customer.ShipAddr.CountryCode){
						address = address + ','	+ Customer.ShipAddr.CountryCode
					}
					$('#address').val(address)
				}
				else{
					$('#address').val('')
				}

				if(Customer.PrimaryPhone && Customer.PrimaryPhone.FreeFormNumber){
					$('#phone').val(Customer.PrimaryPhone.FreeFormNumber)
				}
			}
	})
}

function customers() {
	// body...
	$.get('/customersregister', function customersCallBack(data, status) {
		// body...
		if(data.authUrl == null){
			var result = JSON.parse(data)
			var qbCustomersSelect = document.getElementById('qb_customer_id')

			qbCustomersSelect.innerHTML = ''
			for (var i = 0; i < result.length; i++) {
				qbCustomersSelect.innerHTML += 
	                '<option value=' + result[i].Id + '>' +
	    			result[i].DisplayName +	
	                '</option>'
			}
			var select =  $('#qb_customer_id')[0]

			select.options.selectedIndex = -1
			$('#name')[0].value = select.options[select.options.selectedIndex].innerHTML
		}
	})
}

function customerRowClick(row) {
	// body...
}

function getPriceLevels() {
	// body...
	var priceLevelsSelect = $('#pricelevels_id')
	priceLevelsSelect[0].innerHTML = '<option value=-1>None</option>'

	$.get('/getpricelevels', function(data, status){

		for (var i = 0; i < data.length; i++) {
			priceLevelsSelect[0].innerHTML += 
                '<option value=' + data[i].id + '>' +
    			data[i].description +	
                '</option>'
		}
	})
}

function getPriceListsHeaders() {
	// body...
	var priceListsSelect = $('#pricelists_id')
	priceListsSelect[0].innerHTML = '<option value=-1>None</option>'

	$.get('/getpricelistsheaders', function(data, status){
		if(data.status == 'ok'){
			for (var i = 0; i < data.priceListHeaders.length; i++) {
				priceListsSelect[0].innerHTML += 
	                '<option value=' + data.priceListHeaders[i].id + '>' +
	    			data.priceListHeaders[i].description +	
	                '</option>'
			}
		}
	})
}