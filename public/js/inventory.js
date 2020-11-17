$(document).ready(function InventoryReady() {

	/* Events Handlers */
	var d = new Date()
	var n = d.getTimezoneOffset()

	$('#updateInventory').click(updateInventory)
	$('#searchButton').click(searchButtonClick)

	$('#noItemsFoundDiv').hide()

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

	// APPLY THE DROPZONES
/*	Dropzone.discover()
*/
//	Dropzone.autoDiscover = false
	var dropZones = $('.dropzone')

	$.each(dropZones, function(index, dropzone){
		Dropzone.options[dropzone.id] = {
			uploadMultiple : false,
			dictDefaultMessage : 'Drop An Image Or Click To Search One',
	//				forceFallback : true,
			init : function dropzoneInit() {
				// body...
				this.on('addedfile', function (file) {
					// body...
					filesAccepted = this.getAcceptedFiles()
					if(filesAccepted.length > 0){
						this.removeFile(filesAccepted[0])
					}
				})
			},
		}
	})
})

function searchButtonClick() {


	var searchText = document.getElementById('searchText')

	document.getElementById('tireAnimImg').classList.add('tireAnim')
	/*
	** When Search Button is Clicked we look in the Inventory
	** for the records matching the searchText criteria
	**
	*/

	/*
	** Get Items table to be able to show or hide it
	*/	
	var InventoryTable = $('#InventoryTable')
	/* Get the items table body in order to be able
	** to add rows only to the table body
	*/
	var InventoryTableBody = $('#InventoryTable tbody')[0]

	/* Get just the table body rows in order
	** to be able to delete just these rows
	*/
	var InventoryTableRows = $('#InventoryTable tbody tr')
	
	var searchText = $('#searchText').val()


	var totalTable = $('#totalTable')

	/* Let's hide the order to show the items
	** matching the search criteria
	*/
	$( "#orderDialog" ).hide()

	/*
	** Search the inventory
	*/
	$.get('/searchfor', 
		{
			description:searchText,
		},
		function SeacrhForCallBack(data, status) {
			if(data.status == 'ok'){
				if(data.items.length > 0){

					$('#noItemsFoundDiv').hide()
					$('#InventoryTable').show()

					var formCSRF = document.getElementsByName('csrf-token')
					var csrf = formCSRF[0].content

					var InventoryTableBody = $('#InventoryTable tbody')[0]
					var InventoryTableBodyRows = $('#InventoryTable tbody tr')
					InventoryTableBodyRows.remove()
					$.each(data.items, function inventoryRow(index, invRow) {
						var row = InventoryTableBody.insertRow(-1)
						row.id = invRow.id
						invRow.instock = invRow.instock == null ? 0 : invRow.instock
						invRow.inorders = invRow.inorders == null ? 0 : invRow.inorders
						invRow.price = invRow.price == null ? 0 : invRow.price
						invRow.oferta = invRow.oferta == null ? 0 : invRow.oferta
						invRow.name = invRow.name == null ? '' : invRow.name
						var imgDiv =
							'<div>' +
								'<div  class="imgDiv">' +
									'<img src="public/' + invRow.imgpath + '" class="prodImg" onclick="imgClick(this)" title="CLICK TO CHANGE THE PHOTO">' +
								'</div>' +
							'</div>'
						if(invRow.imgpath.length == 0){
							imgDiv =
								'<div>' +
									'<form action="/fileupload" method="post" enctype="multipart/form-data" class="dropzone" style="width: 100%; height: 60px; border-style: none !important;" id="dropzone' + invRow.id + '">' +
										'<input type="hidden" name="_token" value="' + csrf + '">' +
										'<input type="text" name="itemid" hidden="" value="' + invRow.id  + '">' +
									'</form>' +
								'</div>'
						}	

						if(!Number.parseFloat){
							Number.parseFloat = window.parseFloat
						}

						row.innerHTML =
							'<td class="firstCol">' + 
								imgDiv +
								'<div>' + invRow.name + '</div>' +
							'</td>' +
							'<td class="secondCol alignRight">' + Number.parseFloat(invRow.inpurchaseorders).toFixed(2) + '</td>' +
							'<td class="secondCol alignRight">' + Number.parseFloat(invRow.instock).toFixed(2) + '</td>' +
							'<td class="thirdCol alignRight">' + Number.parseFloat(invRow.inorders).toFixed(2) + '</td>' +
							'<td class="fourthCol"><input type="text" value="' + Number.parseFloat(invRow.price).toFixed(2) + '" class="alignRight" onchange="priceChange(this)"></td>' +
							'<td class="fourthCol"><input type="text" value="' + Number.parseFloat(invRow.oferta).toFixed(2) + '" class="alignRight" onchange="ofertaChange(this)"></td>'

							Dropzone.options[invRow.id] = {
								uploadMultiple : false,
								dictDefaultMessage : 'Drop An Image Or Click To Search One',
						//				forceFallback : true,
								init : function dropzoneInit() {
									// body...
									this.on('addedfile', function (file) {
										// body...
										filesAccepted = this.getAcceptedFiles()
										if(filesAccepted.length > 0){
											this.removeFile(filesAccepted[0])
										}
									})
								},
							}

						row.style.color = invRow.pricemodified ? 'black' : 'red'				
					})

					Dropzone.discover()
				}
				else{
					// No items found, hide the items table
					InventoryTable.hide()

					// Show No Items Found
					$('#InventoryTable').hide()
					$('#noItemsFoundDiv').show()
				}
			}
			else{
				alert("SOMETHING WENT WRONG")
			}
			document.getElementById('tireAnimImg').classList.remove('tireAnim')
		}
	)

}

function imgClick(img) {
	// body...
	var id = img.parentNode.parentNode.parentNode.parentNode.id
	var td = img.parentNode.parentNode
	td.innerHTML =
		'<form action="/fileupload" method="post" enctype="multipart/form-data"class="dropzone" style="width: 100%; height: 40px; border-style: none !important;" id="dropzone' + id + '"">' +
		'<input type="hidden" name="_token" value="' + document.getElementsByName('_token')[0].value + '">' +
			'<input type="text" name="itemid" hidden="" value="' + id + '">' +
		'</form>'
	var myDropzone = new Dropzone("form#dropzone" + id, 
		{ 
			url: "/fileupload", 
			dictDefaultMessage : 'Drop An Image Or Click To Search One',
			init : function dropzoneInit() {
				// body...
				this.on('addedfile', function (file) {
					// body...
					filesAccepted = this.getAcceptedFiles()
					if(filesAccepted.length > 0){
						this.removeFile(filesAccepted[0])
					}
				})
			},
		});
}

function priceChange(element) {
	var itemId = element.parentNode.parentNode.id
	var price = element.value
	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}
	element.value = Number.parseFloat(element.value).toFixed(2)
	$.get('/updateitem', {id:itemId, price:price}, function updateItemCallBack(data, status) {
		updateMessage(data.message)
		element.parentNode.parentNode.style.color = 'black'
		element.style.color = 'black'
	})
}

function ofertaChange(element) {
	var itemId = element.parentNode.parentNode.id
	var oferta = element.value
	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}
	element.value = Number.parseFloat(element.value).toFixed(2)
	$.get('/updateoferta', {id:itemId, oferta:oferta}, function updateItemCallBack(data, status) {
		updateMessage(data.message)
	})
}

function updateInventory() {
	$('#updateInventory').prop('disabled', true)
	$('#updateMessage')[0].style.color = 'red'
	$('#updateMessage')[0].innerHTML = 'SYNCHRONIZING INVENTORIES. PLEASE WAIT ...'
	$('#updateMessage').show()
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
				width = 1
			}
		  width += k;
		  elem.style.width = width + '%'; 
		}
	}
	$.get('/syncronizeinventories', function syncronizeinventoriesCallBack(data, status) {
		if(data.status == 'ok'){
			var InventoryTableBody = $('#InventoryTable tbody')[0]
			var InventoryTableBodyRows = $('#InventoryTable tbody tr')
			InventoryTableBodyRows.remove()
			$.each(data.LocalInventory, function inventoryRow(index, invRow) {
				var row = InventoryTableBody.insertRow(-1)
				row.id = invRow.id
				invRow.instock = invRow.instock == null ? 0 : invRow.instock
				invRow.inorders = invRow.inorders == null ? 0 : invRow.inorders
				invRow.price = invRow.price == null ? 0 : invRow.price
				invRow.name = invRow.name == null ? '' : invRow.name
				var imgDiv =
					'<div>' +
						'<div  class="imgDiv">' +
							'<img src="public/' + invRow.imgpath + '" class="prodImg" onclick="imgClick(this)" title="CLICK TO CHANGE THE PHOTO">' +
						'</div>' +
					'</div>'
				if(invRow.imgpath.length == 0){
					imgDiv =
						'<div>' +
							'<form action="/fileupload" method="post" enctype="multipart/form-data" class="dropzone" style="width: 100%; height: 60px; border-style: none !important;" id="dropzone' + invRow.id + '">' +
								'<input type="text" name="itemid" hidden="" value="' + invRow.id  + '">' +
							'</form>' +
						'</div>'
				}	

				if(!Number.parseFloat){
					Number.parseFloat = window.parseFloat
				}

				row.innerHTML =
					'<td class="firstCol">' + 
						imgDiv +
						'<div>' + invRow.name + '</div>' +
					'</td>' +
					'<td class="secondCol alignRight">' + Number.parseFloat(invRow.inpurchaseorders).toFixed(2) + '</td>' +
					'<td class="secondCol alignRight">' + Number.parseFloat(invRow.instock).toFixed(2) + '</td>' +
					'<td class="thirdCol alignRight">' + Number.parseFloat(invRow.inorders).toFixed(2) + '</td>' +
					'<td class="fourthCol"><input type="text" value="' + Number.parseFloat(invRow.price).toFixed(2) + '" class="alignRight" onchange="priceChange(this)"></td>'

					Dropzone.options[invRow.id] = {
						uploadMultiple : false,
						dictDefaultMessage : 'Drop An Image Or Click To Search One',
				//				forceFallback : true,
						init : function dropzoneInit() {
							// body...
							this.on('addedfile', function (file) {
								// body...
								filesAccepted = this.getAcceptedFiles()
								if(filesAccepted.length > 0){
									this.removeFile(filesAccepted[0])
								}
							})
						},
					}

				row.style.color = invRow.pricemodified ? 'black' : 'red'				
			})

			Dropzone.discover()
			clearInterval(id);
			elem.style.width = '0%';
			$('#updateMessage')[0].style.color = 'green'
			updateMessage('INVENTORIES SYNCHRONIZED')
		}
		else{
			clearInterval(id);
			elem.style.width = '0%';
			if(data.message == 'CONCURRENT EDITION'){
				$('#updateMessage')[0].style.color = 'red'
				updateMessage('THE INVENTORY IS ALREADY BEING SYNCHRONIZED')
			}
			else{
				alert("THE INVENTORY UPDATE HAS FAILED. CHECK THE FOLLOWING MESSAGE: " + data.message)
			}
		}
		$('#updateInventory').prop('disabled', false)

	})
}

function updateMessage(message) {
	$('#updateMessage')[0].innerHTML = message
	$('#updateMessage').show()
	window.setTimeout(function(){$('#updateMessage').hide()}, 3000)
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