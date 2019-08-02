$(document).ready(function orderToPrintReady() {
	// body...
	$('#printOrder').click(printOrderClick)
	var orderid = $('#orderId').val()

	$('#specialInstructionsDiv').hide()
	$('#deliveryDiv').hide()

	orderById(orderid)
})

function printOrderClick() {
	// body...
	window.print()
}

function orderById(orderId) {
	$.get('/orderbyid', {orderId:orderId}, function orderByIdCallBack(data, status) {
		// body...
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
				orderLine = data.order.lines[i]
				orderLinesRow.id = orderLine.id
				orderLinesRow.innerHTML =
					'<td class="itemColumn">' +  orderLine.name + '</td>' +
					'<td class="qtyColumn">' + orderLine.qty + '</td>' +
					'<td class="priceColumn">' + Number.parseFloat(orderLine.price).toFixed(2) + '</td>' +
					'<td class="subTotalColumn">' + Number.parseFloat(orderLine.subTotal).toFixed(2) + '</td>'
			}
			if(data.order.specialinstructions.length > 0){
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

			$.get('/invoiceorder', {orderid:orderId}, function invoiceorderCallBack(data, status) {
				// body...
				if(data.status == 'OK'){
				}
			})
		}
		else{
			alert(data.message)
		}
	})
}
