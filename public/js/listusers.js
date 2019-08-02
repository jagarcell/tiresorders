$(document).ready(function listusersReady() {
	// body...
	$.get('/connect', function listusersConnectCallBack(data, status) {
		// body...
		if(data.authUrl != null){
			window.open(data.authUrl, '_parent', 'left=300, top=40, width=200, height=200')
		}
	})

	$('#usersDiv').show()
	$('#userEditDiv').hide()
	$('#buttonsDiv').hide()
	
	$('#qbNameDiv').hide()
	$('#qbAddressDiv').hide()
	$('#qbPhoneDiv').hide()

	$('#resendButton').click(resendButtonClick)
	$('#saveButton').click(saveButtonClick)
	$('#deleteUserButton').click(deleteUserButtonClick)

	$('#checkName').click(checkNameClick)
	$('#qbUseName').click(qbUseNameClick)

	$('#checkAddress').click(checkAddressClick)
	$('#qbUseAddress').click(qbUseAddressClick)

	$('#checkPhone').click(checkPhoneClick)
	$('#qbUsePhone').click(qbUsePhoneClick)

	$('#priceLevels').change(priceLevelsChange)
	$('#priceLists').change(priceListsChange)
})

function priceLevelsChange() {
	// body...
	if($('#priceLevels')[0].value != -1){
		$('#priceLists')[0].value = -1
	}
}

function priceListsChange() {
	// body...
	if($('#priceLists')[0].value != -1){
		$('#priceLevels')[0].value = -1
	}
}

function checkNameClick() {
	// body...
	$('#qbAddressDiv').hide()
	$('#qbPhoneDiv').hide()

	var qbCustomerId = $('#qbCustomerId').val()

	$.get('/customer', 
		{
			qbcustomerid:qbCustomerId
		}, 
		function qbCustomerCallBack(data, status) {
		// body...
			if(data.Customer != null){
				var Name = JSON.parse(data.Customer)[0].CompanyName
				$('#qbName')[0].innerHTML = Name
				$('#qbNameDiv').show()
			}
			else{
				alert('QB CUSTOMER NOT FOUND')
			}
		}
	)
}

function qbUseNameClick() {
	// body...
	var qbName = $('#qbName')[0].innerHTML
	$('#qbCustomer').val(qbName)
	$('#qbNameDiv').hide()
}

function checkAddressClick() {
	// body...
	$('#qbNameDiv').hide()
	$('#qbPhoneDiv').hide()

	var qbCustomerId = $('#qbCustomerId').val()

	$.get('/customer', 
		{
			qbcustomerid:qbCustomerId
		}, 
		function qbCustomerCallBack(data, status) {
		// body...
			if(data.Customer != null){
				var ShipAddr = JSON.parse(data.Customer)[0].ShipAddr

				var qbAddress = ''
				if(ShipAddr){
					if(ShipAddr.Line1){
						qbAddress += ShipAddr.Line1
					}
					if(ShipAddr.Line2){
						qbAddress += ShipAddr.Line2
					}
					if(ShipAddr.Line3){
						qbAddress += ShipAddr.Line3
					}
					if(ShipAddr.Line4){
						qbAddress += ShipAddr.Line4
					}
					if(ShipAddr.Line5){
						qbAddress += ShipAddr.Line5
					}
					if(ShipAddr.City){
						qbAddress += ',' + ShipAddr.City
					}
					if(ShipAddr.CountrySubDivisionCode){
						qbAddress += ',' + ShipAddr.CountrySubDivisionCode
					}
					if(ShipAddr.PostalCode){
						qbAddress += ',' + ShipAddr.PostalCode
						if(ShipAddr.PostalCodeSuffix){
							qbAddress += '-' + ShipAddr.PostalCodeSuffix
						}
					}
					if(ShipAddr.CountryCode){
						qbAddress += ',' + ShipAddr.CountryCode
					}
					$('#qbAddress')[0].innerHTML = qbAddress
					$('#qbAddressDiv').show()
				}
			}
			else{
				alert('QB CUSTOMER NOT FOUND')
			}
		}
	)
}

function qbUseAddressClick() {
	// body...
	var qbAddress = $('#qbAddress')[0].innerHTML
	$('#userAddress').val(qbAddress)
	$('#qbAddressDiv').hide()
}

function checkPhoneClick() {
	// body...
	$('#qbAddressDiv').hide()
	$('#qbNameDiv').hide()

	var qbCustomerId = $('#qbCustomerId').val()

	$.get('/customer', 
		{
			qbcustomerid:qbCustomerId
		}, 
		function qbCustomerCallBack(data, status) {
		// body...
			if(data.Customer != null){
				var Phone = JSON.parse(data.Customer)[0].PrimaryPhone.FreeFormNumber
				$('#qbPhone')[0].innerHTML = Phone
				$('#qbPhoneDiv').show()
			}
			else{
				alert('QB CUSTOMER NOT FOUND')
			}
		}
	)
}

function qbUsePhoneClick() {
	// body...
	var qbPhone = $('#qbPhone')[0].innerHTML
	$('#userPhone').val(qbPhone)
	$('#qbPhoneDiv').hide()
}

function qbCustomerSelectChange(select) {
	// body...
	$('#userName').val(select.options[select.selectedIndex].innerHTML)
}

function userClick(row) {
	// body...
	$.get('/useredit', 
		{
			userId:row.id
		}, function usereditCallBack(data, status) {
		// body...
			var user = data[0];

			$('#userId').val(user.id)
			$('#userName').val(user.name)	
			$('#userAddress').val(user.address)
			$('#userEmail').val(user.email)
			$('#userPhone').val(user.phone)
			$('#priceLevels').val(user.pricelevels_id)
			$('#priceLists').val(user.pricelist_id)
			$('#emailVerifyButton').hide()
/*			
			if(user.email_verified_at == null){
				$('#emailVerifyButton').show()
			}
			else{
				$('#emailVerifyButton').hide()
			}
*/
			if(user.type == 'admin'){
				$('#priceLevelsDiv').hide()
				$('#priceListsDiv').hide()
			}
			else{
				$('#priceLevelsDiv').show()
				$('#priceListsDiv').show()
			}

			$('#qbCustomer').val(user.name)
			$('#qbCustomerId').val(user.qb_customer_id)
			
			if(user.type == 'admin'){
				$('#qbUserFieldDiv').hide()
				$('#userFieldDiv').show()
				$('#checkAddress').hide()
				$('#userAddress')[0].classList.remove('checkQbAddressButton')
				$('#checkPhone').hide()
				$('#userPhone')[0].classList.remove('checkQbPhoneButton')
			}
			else{
				$('#qbUserFieldDiv').show()
				$('#userFieldDiv').hide()
				$('#checkAddress').show()
				$('#userAddress')[0].classList.add('checkQbAddressButton')
				$('#checkPhone').show()
				$('#userPhone')[0].classList.add('checkQbPhoneButton')
			}
			$('#usersDiv').hide()
			$('#userEditDiv').show()
			$('#buttonsDiv').show()
		}
	)
}

function resendButtonClick() {
	// body...
	$('#actionMessage')[0].style.color = 'green'
	$('#actionMessage')[0].innerHTML = 'RESENDING VERIFICATION EMAIL'
	$.get('/resendverify', 
		{userId:$('#userId').val()}, 
		function resendCallBack(data, status) {
			// body...
			if(data.status == 'ok'){
				$('#actionMessage')[0].style.color = 'green'
				$('#actionMessage')[0].innerHTML = 'EMAIL SENT TO ' + data.user.email
				setTimeout(function(){
					$('#actionMessage')[0].innerHTML = ''
				}, 5000)
			}
			else{
				$('#actionMessage')[0].style.color = 'red'
				$('#actionMessage')[0].innerHTML = 'ERROR SENDING EMAIL TO ' + data.user.email
				setTimeout(function(){
					$('#actionMessage')[0].innerHTML = ''
				}, 5000)
			}
		}
	)
}

function deleteUserButtonClick(){
	if(confirm('DELETING THIS USER WILL DELETE ALL THE RELATED ORDERS')){
		$('#actionMessage')[0].style.color = 'green'
		$('#actionMessage')[0].innerHTML = 'SAVING CHANGES'
		$.get('/deleteuser',
			{userId:$('#userId').val()}, 
			function deleteUserCallBack(data, status) {
			// body...
				if(data.status == 'ok'){
					$('#actionMessage')[0].style.color = 'green'
					$('#actionMessage')[0].innerHTML = 'THE USER WAS DELETED'
					setTimeout(function(){
						window.open('/listusers', '_self')
					}, 5000)
				}
				else{
					$('#actionMessage')[0].style.color = 'red'
					$('#actionMessage')[0].innerHTML = 'ERROR DELETING USER'
					setTimeout(function(){
						$('#actionMessage')[0].innerHTML = ''
					}, 5000)
				}
			}
		)
	}
	else{

	}
}

function saveButtonClick() {
	// body...
	$.get('/usereditsave', 
		{
			userId:$('#userId').val(),
			name:$('#userName').val(),	
			address:$('#userAddress').val(),
			email:$('#userEmail').val(),
			phone:$('#userPhone').val(),
			pricelevels_id:$('#priceLevels').val(),
			qb_customer_id:$('#qbCustomerId').val(),
			pricelist_id:$('#priceLists').val(),
		}, function saveButtonClickCallBack(data, status) {
		// body...
			if(data.usersAdded > 0){
				$('#savedMessage')[0].style.color ='green'
			}
			else{
				$('#savedMessage')[0].style.color = 'red'
			}
			$('#savedMessage')[0].innerHTML = data.message
			setTimeout(function(){
				$('#savedMessage')[0].innerHTML = ''
			}, 3000)
		}
	)
}
