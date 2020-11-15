$(document).ready(function viewtheorderReady() {
	// body...
	$('#deleteSelectedButton').click(deleteSelectedClick)
	$('#deliveryButton').click(submitButtonClick)
	$('#pickUpButton').click(submitButtonClick)
	$('#continueShoppingButton').click(continueShoppingButtonClick)
	$('.submitButton').click(continueShoppingButtonClick)

	$('#submitMessage').hide()
	$('#deleteSelectedButton').hide()
})

function continueShoppingButtonClick() {
	// body...
	var specialInstructions = ''
	
	if(typeof $('#additionalInstructionsText')[0] === 'undefined'){
		window.open('/placeanorder', '_parent')
		return
	}
	
	specialInstructions = $('#additionalInstructionsText')[0].value

	$.get('/continueshopping', 
		{
			specialinstructions: specialInstructions,
		},
		function continueshoppingCallBack(data, status) {
		// body...
			window.open('/placeanorder', '_parent')
		}
	)
}

function checkForDeleteChange(element) {
	// body...
	var checkBoxes = $('.checkBoxInput')
	var hideDeleteButton = true
	$.each(checkBoxes, function(index, checkBox){
		if(checkBox.checked){
			hideDeleteButton = false
		}
	})
	if(hideDeleteButton){
		$('#deleteSelectedButton').hide()
	}
	else{
		$('#deleteSelectedButton').show()
	}
}

function deleteSelectedClick() {
	// body...
	var orderTable = $('#orderTable tbody')
	var orderTotal = parseFloat('0.0')
	var selectdQbItemId = []

	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}

	$.each(orderTable[0].rows, function(index, row){
		if(row.id != null && row.id.length > 0){
			var selected = $('#checkbox_' + row.id)[0].checked
			subtotal = row.cells[3].innerHTML
			if(selected){
				selectdQbItemId.push(row.id)
			}
			else{
				orderTotal = parseFloat(orderTotal) + parseFloat(subtotal)
			}
		}
	})

	if(selectdQbItemId.length > 0){
		$.get('/deletelinebyqbitemid', 
			{
				qbItemIds:selectdQbItemId
			}, 
			function deletelinebyqbitemidCallBack(data, status) {
			// body...
				if(data.status == 'success'){
					$('#deleteSelectedButton').hide()
					$.each(data.deletedlines, function(index, deletedline){
						if(deletedline.status == 'success'){
							var orderTable = $('#orderTable')
							row = $('#' + deletedline.qbitemid)
							orderTable[0].deleteRow(row[0].rowIndex)
						}
					})
				}
			}
		)
	}
	$('#orderTotal')[0].innerHTML = parseFloat(orderTotal).toFixed(2)
}

function submitButtonClick(submitButton) {
	// body...
	buttonId = submitButton.currentTarget.id
	$('#submitMessage')[0].style.color = 'red'
	$('#submitMessage')[0].innerHTML = 'YOUR ORDER IS BEING SUBMITTED'
	$('#submitMessage').show()
	var elem = document.getElementById("myBar");   
	var width = 1;
	var id = setInterval(frame, 100);
	var k = 1
	function frame() {
		if (width >= 100) {
			width = 99;
			k = k * (-1)
		} else {
			if(width == 0){
				k = k * (-1)
			}
		  width += k;
		  elem.style.width = width + '%'; 
		}
	}
	var specialInstructions = $('#additionalInstructionsText')[0].value
	$.get('/submitorder', 
		{
			buttonid: buttonId,
			specialinstructions: specialInstructions
		},
		 function submitorderCallBack(data, status) {
			$('#submitMessage')[0].style.color = 'green'
			clearInterval(id);
			elem.style.width = '0%';
			$('#submitMessage')[0].innerHTML = 'YOUR ORDER HAS BEEN SUBMITTED'
			setTimeout(function function_name(argument) {
				$('#submitMessage').hide()
			}, 3000)
			window.open('/placeanorder', '_self')
		}
	)
}

function move() {
}
