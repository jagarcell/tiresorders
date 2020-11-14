$(document).ready(function placeAnOrderReady() {
	// body...
	$('#itemsTable').hide()
	$('#totalTable').hide()
	$('#addToOrderButton').hide()
	$('#noItemsFoundDiv').hide()

	$('#searchButton').click(searchButtonClick)
	$('#orderLink').click(orderLinkClick)
	$('#addToOrderLink').click(addToOrderLinkClick)
	$('#addSelected').click(addToOrderClick)
	$('#addToOrder').click(addToOrderClick)
	$('#addToOrderButton').click(addToOrderClick)

	window.onresize = resizeWindow

	var searchText = document.getElementById('searchText')
	if($(window).width() > 736){
		searchText.placeholder = 'Enter Your Search Here And/Or Hit Enter'
	}
	else{
		searchText.placeholder = 'Enter Your Search Here'
	}

	// For all the Browsers
	if(searchText.addEventListener){
		searchText.addEventListener('keyup', function() {
			if(event.key === 'Enter'){
				searchButtonClick()
				event.preventDefault()
			}
		})
	}
	// For IE 11 and below
	else{
		if(searchText.attachEvent){
			searchText.attachEvent('keyup', function() {
				if(event.key === 'Enter'){
					searchButtonClick()
					event.preventDefault()
				}
			})
		}
	}

	document.getElementById("searchText").focus()

	var ofertaNDivs = $(".ofertaNDiv")

	for(var i = 0; i < ofertaNDivs.length; i++){
		if(i == 0){
			ofertaNDivs[i].style.visibility = 'visible'
			ofertaNDivs[i].style.opacity = 100
			ofertaNDivs[i].checked = 1
		}
		else{
			ofertaNDivs[i].checked = 0
		}
	}

	window.setInterval(function(){
		var ofertaNDivs = $(".ofertaNDiv")
		var i = 0
		for(i = 0; i < ofertaNDivs.length; i++){
			if(ofertaNDivs[i].checked == 0)
			{
				ofertaNDivs[i].checked = 1
				ofertaNDivs[i].style.visibility = 'visible'
				ofertaNDivs[i].style.opacity = 100
				break;
			}
			else{
				ofertaNDivs[i].style.visibility = 'hidden'
				ofertaNDivs[i].style.opacity = 0
			}
		}
		if(ofertaNDivs.length > 0 && i == ofertaNDivs.length){
			ofertaNDivs[0].checked = 1
			ofertaNDivs[0].style.visibility = 'visible'
			ofertaNDivs[0].style.opacity = 100
			for(i = 1; i < ofertaNDivs.length; i++){
				ofertaNDivs[i].checked = 0
			}
		}

	}, 6000)
})

function specialClick(element) {
	// body...
	var ofertaNDivs = element.children[1].getElementsByClassName('ofertaNDiv')
	for (var i = ofertaNDivs.length - 1; i >= 0; i--) {
		if(ofertaNDivs[i].style.visibility == 'visible'){
			ofertaClick(ofertaNDivs[i])
			break;
		}
	}
}

function ofertaClick(element){
	// body...
	document.getElementById('tireAnimImg').classList.add('tireAnim')
	/*
	** When Search Button is Clicked we look in the Inventory
	** for the records matching the searchText criteria
	**
	*/

	/*
	** Get Items table to be able to show or hide it
	*/	
	var itemsTable = $('#itemsTable')
	/* Get the items table body in order to be able
	** to add rows only to the table body
	*/
	var itemsTableBody = $('#itemsTable tbody')[0]

	/* Get just the table body rows in order
	** to be able to delete just these rows
	*/
	var itemsTableRows = $('#itemsTable tbody tr')
	
	var searchText = $('#searchText').val()


	var totalTable = $('#totalTable')

	/* Let's hide the order to show the items
	** matching the search criteria
	*/
	$( "#orderDialog" ).hide()

	/*
	** Search the inventory
	*/
	$.get('/searchitembyid', 
		{
			id:element.id,
		}, 
		function seacrhItemByIdCallBack(data, status) {
		// body...
			// Clear the Items table body rows
			itemsTableRows.remove()
			if(data != null && data.length > 0){
				/*
				** If new matching items where found
				** let's create the items table rows
				*/
				for (var i = 0; i < data.length; i++) {
					var inpurchaseorders = data[i].inpurchaseorders
					var instock = data[i].instock
					if(inpurchaseorders == null){
						inpurchaseorders = 0
					}
					if(instock === null){
						instock = 0
					}
					var totalstock = Number(inpurchaseorders) +
									Number(instock)

					if(totalstock > 0){
						var row = itemsTableBody.insertRow(-1)
						row.id = data[i].qbitemid
						var price = data[i].price
						var oferta = data[i]. oferta
						
						if(oferta > 0){
							price = oferta
							console.log(price)
						}

						var CallToConfirm = ''
						if(totalstock < 2)
						{
							CallToConfirm = 'Call To Confirm'
						}

						if(price === null){
							price = 0
						}

						var imgpath = data[i].imgpath
						if(imgpath.length == 0){
							imgpath = 'img/noimg.jpg'
						}
						if(!Number.parseFloat){
							Number.parseFloat = window.parseFloat
						}
						row.innerHTML +=
							'<td id="description_' + row.id + '" class="firstCol borderBottom">' +
								'<div class="itemDescription"><img src="public/' + imgpath + '" class="productImage"></div>' +
								'<div>' + data[i].name + '</div>' +
							'</td>' +
							'<td id="instock_' + row.id + '" class="secondCol borderBottom instock">' +
								(totalstock > 24 ? "24+" : Number.parseFloat(totalstock).toFixed(0)) + 
								'<div hidden="true" id="instock1_' + row.id + '">' + Number.parseFloat(totalstock).toFixed(0) + '</div>' + 
								'<div class="CallToConfirm">' + 
								CallToConfirm + '</div>' +
							'</td>' +
							'<td id="qty1_' + row.id + '" class="thirdCol borderBottom"><input id="qty2_' + row.id + '" type="number" value="1" min="0" max="' + totalstock + '" class="alignRight qtyInput" onchange="qtyChange(this)" onkeyup="qtyChange(this)"></td>' +
							'<td id="price_' + row.id + '" class="fourthCol borderBottom price">' +
								Number.parseFloat(price).toFixed(2) +
							'</td>' +
							'<td id="subtotal_' + row.id + '" class="fifthCol borderBottom price">' +
								Number.parseFloat(price).toFixed(2) +
							'</td>' +
							'<td id="selected_' + row.id + '" class="sixCol textCentered"><input type="checkbox" class="editSelectedBox" onchange="selectChanged(this)"></td>'
					}
				}

				// Items found, show the items table
				itemsTable.show()
				totalTable.show()
				//Hide the No Items Found Message
				$('#noItemsFoundDiv').hide()
			}
			else{
				// No items found, hide the items table
				itemsTable.hide()
				totalTable.hide()
				// Show No Items Found
				$('#noItemsFoundDiv').show()
			}
			document.getElementById('tireAnimImg').classList.remove('tireAnim')
		}
	)
}

function searchButtonClick() {
	// body...
	document.getElementById('tireAnimImg').classList.add('tireAnim')
	/*
	** When Search Button is Clicked we look in the Inventory
	** for the records matching the searchText criteria
	**
	*/

	/*
	** Get Items table to be able to show or hide it
	*/	
	var itemsTable = $('#itemsTable')
	/* Get the items table body in order to be able
	** to add rows only to the table body
	*/
	var itemsTableBody = $('#itemsTable tbody')[0]

	/* Get just the table body rows in order
	** to be able to delete just these rows
	*/
	var itemsTableRows = $('#itemsTable tbody tr')
	
	var searchText = $('#searchText').val()


	var totalTable = $('#totalTable')

	/* Let's hide the order to show the items
	** matching the search criteria
	*/
	$( "#orderDialog" ).hide()

	/*
	** Search the inventory
	*/
	$.get('/searchinventory', 
		{
			description:searchText, 
		}, 
		function seacrhInventoryCallBack(data, status) {
		// body...
			// Clear the Items table body rows
			itemsTableRows.remove()
			if(data != null && data.length > 0){
				/*
				** If new matching items where found
				** let's create the items table rows
				*/
				for (var i = 0; i < data.length; i++) {
					var inpurchaseorders = data[i].inpurchaseorders
					var instock = data[i].instock
					if(inpurchaseorders == null){
						inpurchaseorders = 0
					}
					if(instock === null){
						instock = 0
					}
					var totalstock = Number(inpurchaseorders) +
									Number(instock)

					if(totalstock > 0){
						var row = itemsTableBody.insertRow(-1)
						row.id = data[i].qbitemid
						var price = data[i].price
						var oferta = data[i]. oferta
						
						if(oferta > 0){
							price = oferta
							console.log(price)
						}

						var CallToConfirm = ''
						if(totalstock < 2)
						{
							CallToConfirm = 'Call To Confirm'
						}

						if(price === null){
							price = 0
						}

						var imgpath = data[i].imgpath
						if(imgpath.length == 0){
							imgpath = 'img/noimg.jpg'
						}
						if(!Number.parseFloat){
							Number.parseFloat = window.parseFloat
						}
						row.innerHTML +=
							'<td id="description_' + row.id + '" class="firstCol borderBottom">' +
								'<div class="itemDescription"><img src="public/' + imgpath + '" class="productImage"></div>' +
								'<div>' + data[i].name + '</div>' +
							'</td>' +
							'<td id="instock_' + row.id + '" class="secondCol borderBottom instock">' +
								(totalstock > 24 ? "24+" : Number.parseFloat(totalstock).toFixed(0)) + 
								'<div hidden="true" id="instock1_' + row.id + '">' + Number.parseFloat(totalstock).toFixed(0) + '</div>' + 
								'<div class="CallToConfirm">' + 
								CallToConfirm + '</div>' +
							'</td>' +
							'<td id="qty1_' + row.id + '" class="thirdCol borderBottom"><input id="qty2_' + row.id + '" type="number" value="1" min="0" max="' + totalstock + '" class="alignRight qtyInput" onchange="qtyChange(this)" onkeyup="qtyChange(this)"></td>' +
							'<td id="price_' + row.id + '" class="fourthCol borderBottom price">' +
								Number.parseFloat(price).toFixed(2) +
							'</td>' +
							'<td id="subtotal_' + row.id + '" class="fifthCol borderBottom price">' +
								Number.parseFloat(price).toFixed(2) +
							'</td>' +
							'<td id="selected_' + row.id + '" class="sixCol textCentered"><input type="checkbox" class="editSelectedBox" onchange="selectChanged(this)"></td>'
					}
				}

				// Items found, show the items table
				itemsTable.show()
				totalTable.show()
				//Hide the No Items Found Message
				$('#noItemsFoundDiv').hide()
			}
			else{
				// No items found, hide the items table
				itemsTable.hide()
				totalTable.hide()
				// Show No Items Found
				$('#noItemsFoundDiv').show()
			}
			document.getElementById('tireAnimImg').classList.remove('tireAnim')
		}
	)
}

function addToOrderClick() {
	// body...
	var addItemTable = $('#itemsTable tbody')[0]

	var selectedQbItemId = []
	$.each(addItemTable.rows, function(index, row){
		var selected = row.children['selected_' + row.id].children[0].checked

		if(selected){
			var qbitemid = row.id
			var price = (row.children['price_' + row.id].innerHTML)
			var qty = $('#qty2_' + row.id).val()
			selectedQbItemId.push({qbitemid:qbitemid, price:price, qty:qty})
		}
	})

	if(selectedQbItemId.length > 0){
		$.get('/additemtoorder', 
			{
				linesdata:selectedQbItemId
			}, function additemtoorderCallBack(data, status) {
				if(data.status == 'success'){
					$('#addItemDialog').hide()
					window.open('/viewtheorder', '_parent')
				}
			}
		)
	}
}

function addRow(row) {
	// body...
	/*
	** Item row clicked, the user wants to add the item to the order
	*/
	var qbitemid = row.id
	var description = row.children['description_' + row.id].innerHTML
	var price = row.children['price_' + row.id].innerHTML
	addItemDialog(qbitemid, description, price)
}

function addItemDialog(qbitemid, description, price) {
	// body...
	/* Item to order data entry
	**
	*/

	/*
	** Set the item data to display to the user
	*/
	var addItemTable = $('#addItemTable tbody')[0]
	var addItemTableRow = addItemTable.insertRow(-1)
	addItemTableRow.id = qbitemid
	addItemTableRow.innerHTML =
		'<td id="description_' + addItemTableRow.id + '" class="firstCol">' + description + '</td>' +
		'<td id="price_' + addItemTableRow.id + '" class="secondCol">' + price + '</td>' +
		'<td><input id="qty_' + addItemTableRow.id + '" type="number" class="itemQty thirdCol" value="1"></td>' +
		'<td id="subtotal_' + addItemTableRow.id + '" class="fourthCol">' + price + '</td>' +
		'<td id="selectedtoadd_' + addItemTableRow.id + '" class="fifthCol"><input type="checkbox" checked="true"</td>'

	// Show the item data input
	$('#itemsTable').hide()
	$('#addItemDialog').show()
}

function addToOrderLinkClick() {
	// body...
	$('#addItemDialog').hide()
	var row = $(this)[0].parentNode.parentNode
	var qbitemid = row.id
	var price = (row.children['price'].innerHTML)
	var qty = $('#qty').val()

	$.get('/additemtoorder', 
		{
			qbitemid:qbitemid,
			price:price,
			qty:qty,
		}, function additemtoorderCallBack(data, status) {
			// body...
			console.log(data)
		}
	)
}

function orderLinkClick() {
	// body...
	var itemsTable = $('#itemsTable')
	itemsTable.hide()
	if($( "#orderDialog" )[0].style.display == 'none'){
		$( "#orderDialog" ).show()
	}
	else{
		$( "#orderDialog" ).hide()
	}
}

function addSelectedClick() {
	// body...
	var addItemTableRows = $('#addItemTable tbody')
	addItemTableRows[0].innerHTML = ''

	var itemsTableRows = $('#itemsTable tbody tr')
	$.each(itemsTableRows, function (index, row) {
		// body...
		var selected = row.children['selected_' + row.id].children[0].checked
		if(selected){
			addRow(row)
		}
	})
}

function qtyChange(element) {
	var row = element.parentNode.parentNode
	row.children['selected_' + row.id].children[0].checked = true
	selectChanged(row.children['selected_' + row.id].children[0])
}

function selectChanged(checkB) {
	var checkedBoxes = $('.editSelectedBox:checked')
	var total = 0
	var addToOrderButtonHide = true

	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}

	$.each(checkedBoxes, function function_name(index, checkbox) {
		if(checkbox.checked){
			addToOrderButtonHide = false;
		}
		var row = checkbox.parentNode.parentNode

		var instock = $('#instock1_' + row.id)[0].textContent

		var price = row.children['price_' + row.id].innerHTML

		var qty = row.children['qty1_' + row.id].children[0].value

		qty = Number.parseFloat(qty)
		instock = Number.parseFloat(instock)

		if(qty > instock){

			qty = instock
			row.children['qty1_' + row.id].children[0].value = qty
		}

		var subtotal = qty * price
		row.children['subtotal_' + row.id].innerHTML = Number.parseFloat(subtotal).toFixed(2)
		total += qty*price
	})

	$('#orderTotal')[0].innerHTML = Number.parseFloat(total).toFixed(2)
	if(addToOrderButtonHide){
		$('#addToOrderButton').hide()
	}
	else{
		$('#addToOrderButton').show()
	}
}

function resizeWindow(){

	var searchText = document.getElementById('searchText')
	if($(window).width() > 736){
		searchText.placeholder = 'Enter Your Search Here And/Or Hit Enter'
	}
	else{
		searchText.placeholder = 'Enter Your Search Here'
	}
}
