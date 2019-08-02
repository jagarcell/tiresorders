$(document).ready(function listordersReady() {
	// body...
	$('#ordersList').show()
	$('#orderDetails').hide()
	$('#deleteSelectedButton').hide()
	$('#specialInstructionsDiv').hide()
	$('#deliveryDiv').hide()

	$('#deleteSelectedButton').click(deleteSelected)
	$('#sendInvoice').click(sendInvoice)
	$('.statusCheck').change(statusCheckChange)
	$('.refreshButton').click(refreshButtonClick)
	refreshButtonClick()
})

function refreshButtonClick() {
	// body...
	var open = $('#open')[0].checked ? 'open' : 'none'
	var pickup = $('#pickup')[0].checked ? 'pickup' : 'none'
	var delivery = $('#delivery')[0].checked ? 'delivery' : 'none'
	var invoiced = $('#invoiced')[0].checked ? 'invoiced' : 'none'

	$.get('/listordersbystatus', 
		{
			open:open,
			pickup:pickup,
			delivery:delivery,
			invoiced:invoiced,
		}, 
		function refreshButtonClickCallBack(data, status) {
		// body...
			var listOrdersTableBody = $('#listordersTable tbody')[0]
			$('#listordersTable tbody tr').remove()
			var orders = data['orders']
			if(orders.length == 0){
				listOrdersTableBody.innerHTML = '<div class="noPendingOrders">THERE ARE NOT PENDING ORDERS FOR YOUR SELECTION</div>'
			}
			else{
				listOrdersTableBody.innerHTML =''
			}

			$.each(orders, function(index, order){
				var row = listOrdersTableBody.insertRow(-1)
				row.id = order.id

				if(!Number.parseFloat){
					Number.parseFloat = window.parseFloat
				}

				row.innerHTML = 
					'<td class="customerColumn">' + order.customer + '</td>' +
					'<td class="dateColumn">' + order.orderdate + '</td>' +
					'<td class="totalColumn">' + Number.parseFloat(order.total).toFixed(2) + '</td>' +
					'<td class="statusColumn">' + order.status + '</td>'

				$('#' + row.id).click(function(){
					orderById(this.id)
				})
			})
	})
}

function statusCheckChange(checkbox) {
	// body...
	if(checkbox.currentTarget.value == 'all'){
		if(checkbox.currentTarget.checked){
			var checkBoxes = $('.statusCheck')
			$.each(checkBoxes, function(index, checkBox){
				checkBox.checked = true
			})
		}
	}
	else{
		var individualStatus = $('.individualStatus')
		$('.statusAll')[0].checked = true

		$.each(individualStatus, function(index, status){
			if(!status.checked){
				($('.statusAll')[0]).checked = false
			}
		})
	}
	refreshButtonClick()
}

function sendInvoice() {
	var orderId = $('#orderId').val()
	window.open('/ordertoprint/' + orderId, '_blank ', "titlebar=no,scrollbars=yes,resizable=yes,top=50,left=100,width=700,height=600");
}

function deleteSelected() {
	var checkedLines = $('.deleteCheckBox:checked')
	var orderLinesIds = new Array()
	for (var i = 0; i < checkedLines.length; i++) {
		orderLinesIds.push(checkedLines[i].parentNode.parentNode.id)
	}

	$.get('/deleteorderlines', 
		{orderLinesIds:orderLinesIds}, 
		function deleteorderlinesCallBack(data, status) {
			if(data.orderisempty){
				window.open('/listorders', '_parent')
			}
			else{
				orderById(data.orderid)
			}
			$('#deleteSelectedButton').hide()
		}
	)
}

function rowClick(row) {
	// body...
	orderById(row.id)
}

function orderById(orderId) {
	$('#orderId').val(orderId)
	$.get('/orderbyid', {orderId:orderId}, function orderByIdCallBack(data, status) {

		if(data.status == 'ok'){
			$('#orderCustomer')[0].innerHTML = data.order.customer
			$('#orderDate')[0].innerHTML = data.order.orderdate

			var orderLinesTableBody = $('#orderLines tbody')[0]
			var orderLinesRows = $('#orderLines tbody tr')
			orderLinesRows.remove()

			if(!Number.parseFloat){
				Number.parseFloat = window.parseFloat
			}

			for (var i = 0; i < data.order.lines.length; i++) {
				var orderLinesRow = orderLinesTableBody.insertRow(-1)
				orderLinesRow.classList.add('orderLinesResponsive')

				orderLine = data.order.lines[i]
				orderLinesRow.id = orderLine.id
				orderLinesRow.innerHTML =
					'<td class="itemColumn  orderLinesResponsive">' +  orderLine.name + '</td>' +
					'<td class="qtyColumn  orderLinesResponsive">' + orderLine.qty + '</td>' +
					'<td class="priceColumn  orderLinesResponsive">' + Number.parseFloat(orderLine.price).toFixed(2) + '</td>' +
					'<td class="subTotalColumn  orderLinesResponsive">' + Number.parseFloat(orderLine.subTotal).toFixed(2) + '</td>' +
					'<td class="selectColumn  orderLinesResponsive"><input type="checkbox" class="deleteCheckBox" onchange="deleteCheckBoxChange()"></td>'
			}
			if(data.order.specialinstructions != null && data.order.specialinstructions.length > 0){
				$('#specialInstructionsText')[0].innerHTML = data.order.specialinstructions
				$('#specialInstructionsDiv').show()
			}

			if(data.order.status == 'delivery'){
				$('#deliveryAddressDiv')[0].innerHTML = data.order.address
				$('#deliveryDiv').show()
			}

			var orderTotal = $('#ordertotal')[0]
			orderTotal.innerHTML = Number.parseFloat(data.order.orderTotal).toFixed(2)		
			$('#ordersList').hide()
			$('#orderDetails').show()
		}
		else{
			alert(data.message)
		}
	})
}

function deleteCheckBoxChange(checkbox) {
	// body...
	var checkBoxes = $('.deleteCheckBox')

	var deleteSelectedButtonHide = true
	$.each(checkBoxes, function(index, checkbox){
		if(checkbox.checked){
			deleteSelectedButtonHide = false
		}
	})

	if(deleteSelectedButtonHide){
		$('#deleteSelectedButton').hide()
	}
	else{
		$('#deleteSelectedButton').show()
	}
}