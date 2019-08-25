var keyup
var savePricesFlag

$(document).ready(function pricelistsReady() {
	// body...
	$('#newListButton').click(newListButtonClick)
	$('#deleteListButton').click(deleteListButtonClick)
	$('#goButtonId').click(goButtonClick)
	$('#searchButton').click(searchButtonClick)

	$('#descriptionSelect').change(descriptionSelectChange)

	$('#newListDescription').hide()
	$('#deletedMessage').hide()
	$('#noItemsFoundDiv').hide()

	var newListDescription = document.getElementById('newListDescription')

	// For all the Browsers
	if(newListDescription.addEventListener){
		newListDescription.addEventListener('keyup', function() {
			if(event.key === 'Escape'){
				var id = $('#descriptionSelect').children("option:selected").val()
				getPriceList(id)
				$('#newListDescription').hide()
				$('#priceChangeFactor').show()
				$('#descriptionSelect').show()
				keyup = true
				event.preventDefault()
			}
		})
	}
	// For IE 11 and below
	else{
		if(newListDescription.attachEvent){
			newListDescription.attachEvent('keyup', function() {
				if(event.key === 'Escape'){
					var id = $('#descriptionSelect').children("option:selected").val()
					getPriceList(id)
					$('#newListDescription').hide()
					$('#priceChangeFactor').hide()
					$('#descriptionSelect').show()
					keyup = true
					event.preventDefault()
				}
			})
		}
	}

	savePrices = false

	set_layout()

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

})

function searchButtonClick() {
	document.getElementById('tireAnimImg').classList.add('tireAnim')
	/*
	** When Search Button is Clicked we look in the Inventory
	** for the records matching the searchText criteria
	**
	*/

	/*
	** Get Items table to be able to show or hide it
	*/	
	var priceListTable = $('#priceListTable')
	/* Get the items table body in order to be able
	** to add rows only to the table body
	*/
	var priceListTableBody = $('#priceListTable tbody')[0]

	/* Get just the table body rows in order
	** to be able to delete just these rows
	*/
	var priceListTableRows = $('#priceListTable tbody tr')
	
	var searchText = $('#searchText').val()

	/* Let's hide the order to show the items
	** matching the search criteria
	*/
	$( "#orderDialog" ).hide()

	var pricelistheaderid = $('#descriptionSelect').val()

	/*
	** Search the inventory
	*/
	$.get('/searchinlist', 
		{
			pricelistheaderid:pricelistheaderid,
			searchtext:searchText,
		},
		function SeacrhInListCallBack(data, status) {
			if(data.status == 'ok'){
				var priceListTableBody = $('#priceListTable tbody')[0]
				var priceListTableBodyRows = $('#priceListTable tbody tr')
				priceListTableBodyRows.remove()

				$('#noItemsFoundDiv').hide()
				$('#listDiv').show()

				if(data.items.length > 0){
					$.each(data.items, function inventoryRow(index, invRow) {
						var row = priceListTableBody.insertRow(-1)
						row.id = invRow.id
						invRow.instock = invRow.instock == null ? 0 : invRow.instock
						invRow.inorders = invRow.inorders == null ? 0 : invRow.inorders
						invRow.price = invRow.price == null ? 0 : invRow.price
						invRow.name = invRow.name == null ? '' : invRow.name

						if(!Number.parseFloat){
							Number.parseFloat = window.parseFloat
						}

						row.innerHTML = 
							'<td class="itemColumn">' + invRow.name + '</td>' +
							'<td class="priceColumnValue"><input type="number" value="' + Number.parseFloat(invRow.price).toFixed(2) + '" class="priceInput"  onchange="priceValueChange(this)"></td>'

						row.style.color = invRow.modified ? 'black' : 'red'				
					})
				}
				else{
					// No items found, hide the items table

					// Show No Items Found
					$('#listDiv').hide()
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

function set_layout() {
	// body...
	$.get('listqty', function listqtyCallBack(data, status) {
		// body...
		if(data.status == 'ok'){

			if(data.listqty > 0){
				$('#thereAreNotLists').hide()
				$('#thereAreLists').show()
			}
			else{
				$('#thereAreNotLists').show()
				$('#thereAreLists').hide()
			}
		}
	})
}

function goButtonClick() {
	// body...
	savePricesFlag = true
	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}

	var priceInputs = $('.priceInput')
	var percentage = Number.parseFloat($('#percentage')[0].value)
	var priceChangeType = $('#priceChangeType')[0].value


	$.each(priceInputs, function (index, priceInput) {
		// body...
		var price = Number.parseFloat($(priceInput)[0].value)
		var changeValue = price * (percentage/100)
		if(priceChangeType == 'up'){
			$('.priceInput')[index].value = Number.parseFloat(price + changeValue).toFixed(2)
		}
		else{
			$('.priceInput')[index].value = Number.parseFloat(price - changeValue).toFixed(2)
		}
		$($('.priceInput')[index].parentNode.parentNode).addClass('changed')
		$($('.priceInput')[index].parentNode.parentNode).removeClass('notmodified')

	})
	
	saveAllPrices()
	savePricesFlag = false
}

function newListDescriptionChange() {
	// body...
}

function newListDescriptionKeyUp() {
	// body...0
	console.log(this)
}

function priceValueChange(price) {
	// body...
	if(!Number.parseFloat){
		Number.parseFloat = window.parseFloat
	}
	price.value = Number.parseFloat(price.value).toFixed(2)
	$(price.parentNode.parentNode).addClass('changed')

	saveChangedPrices()
}

var descriptionChange = false

function newListButtonClick() {
	// body...

	var descriptionSelect = document.getElementById('descriptionSelect')

	if(descriptionChange){
		$('#descriptionSelect').show()
		$('#priceChangeFactor').show()
		$('#newListDescription').hide()
		descriptionChange = false
		return
	}

	$('#descriptionSelect').hide()
	$('#priceChangeFactor').hide()
	$('#newListDescription').show()

	var priceListTableBody = $('#priceListTable tbody')[0]
	var priceListTableRows = $('#priceListTable tbody tr')[0]
	priceListTableBody.innerHTML = ''
	$('#newListDescription').focus()
}

function newListDescriptionChange(description) {
	// body...
	if(keyup){
		keyup = false
		return
	}

	descriptionChange = true

	var descriptionSelect = $('#descriptionSelect')[0]

	var optionElementReference = new Option(description.value, 0, false, true);

	if(description.value.length == 0){
		if(window.confirm('YOU MUST ENTER A DESCIPTION TO CREATE A NEW LIST'))
		{
		}
		else{
			$('#descriptionSelect').hide()
			$('#priceChangeFactor').hide()
			$('#newListDescription').show()
		}
		return
	}

	$('#descriptionSelect').show()
	$('#priceChangeFactor').show()
	$('#newListDescription').hide()

	$.get('/createnewlist', 
		{
			listDescription:description.value
		}, function creteNewListCallBack(data, status) {
		// body...
		if(!Number.parseFloat){
			Number.parseFloat = window.parseFloat
		}

		// check status
		
		if(data.status == 'ok'){
			optionElementReference.value = data.pricelistid
			pricelistlines = data.pricelistlines
			$.each(pricelistlines, function pricelistlnesCallBack(index, line) {
				// body...
				var priceListTableBody = $('#priceListTable tbody')[0]
				var row = priceListTableBody.insertRow(-1)
				row.innerHTML =
					'<td class="itemColumn">' + line.description + '</td>' +
					'<td class="priceColumnValue"><input type="number" value=' + Number.parseFloat(line.price).toFixed(2) + ' class="priceInput"  onchange="priceValueChange(this)"></td>'
				row.id = line.id
				$(row).addClass('notmodified')
			})

			// set layout
			set_layout()
		}
	})

	descriptionSelect.options.add(optionElementReference)
	var lastindex = descriptionSelect.options.length
	descriptionSelect.selectedIndex = lastindex - 1
	description.value = ''
}

function descriptionSelectChange() {
	// body...
	priceListHeaderId = this.value

	saveChangedPrices()

	getPriceList(priceListHeaderId)
}

function saveChangedPrices(argument) {
	// body...
	var changedPrices = $('.changed')

	var prices = {}

	if(changedPrices.length > 0){
		$.each(changedPrices, function (index, changedPrice) {
			// body...
//			$(changedPrice).removeClass('changed')
			$(changedPrice).removeClass('notmodified')
			var priceInput = $($(changedPrice).children('.priceColumnValue')[0]).children('.priceInput')[0]
			var price = priceInput.value
			var row = priceInput.parentNode.parentNode
			prices[row.id] = price
		})

		$.get('/updateprices', {prices:prices}, function updatePricesCallBack(data, status) {
			// body...
			if(data.status == 'fail'){
				alert("THERE WHERE SOME PRICES THAT COULDN'T BE UPDATED");
			}
		})
	}
}

function saveAllPrices() {
	// body...
	var changedPrices = $('.changed')
	changedPricesChunks = chunkArray(changedPrices, 10)
	var status = 'ok'
	var nChunks = changedPricesChunks.length

	for(var i = 0; i < changedPricesChunks.length; i++){
		var changedPrices = changedPricesChunks[i]

		var prices = {}

		if(changedPrices.length > 0){
			$.each(changedPrices, function (index, changedPrice) {
				// body...
				$(changedPrice).removeClass('changed')
				var priceInput = $($(changedPrice).children('.priceColumnValue')[0]).children('.priceInput')[0]
				var price = priceInput.value
				var row = priceInput.parentNode.parentNode
				prices[row.id] = price
			})

			$.get('/updateprices', {prices:prices}, function updatePricesCallBack(data, status) {
				// body...
				if(data.status == 'fail'){
					status = 'fail'
				}
				nChunks--
				if(nChunks == 0 && status == 'fail'){
					alert("THERE WHERE SOME PRICES THAT COULDN'T BE UPDATED");
				}
			})
		}
	}
}

function getPriceList(priceListHeaderId) {
	// body...

	$.get('/pricelistbyid', {id:priceListHeaderId}, function priceListByIdCallBack(data, status) {
		// body...
		if(data.status == 'ok'){
			pricelistlines = data.pricelist.lines
			var priceListTableBody = $('#priceListTable tbody')[0]
			priceListTableBody.innerHTML = ''
			$.each(pricelistlines, function pricelistlnesCallBack(index, line) {
				// body...
				var row = priceListTableBody.insertRow(-1)
				row.innerHTML =
					'<td class="itemColumn">' + line.description + '</td>' +
					'<td class="priceColumnValue"><input type="number" value=' + Number.parseFloat(line.price).toFixed(2) + ' class="priceInput"  onchange="priceValueChange(this)"></td>'
				row.id = line.id
				$(row).addClass('listTableBodyRow')
				if(line.modified == 0){
					$(row).addClass('notmodified')
				}
			})
		}
		else
		{
			if(data.message == 'LIST NOT FOUND'){
				var priceListTableBody = $('#priceListTable tbody')[0]
				priceListTableBody.innerHTML = ''
			}
		}
	})	
}

function deleteListButtonClick() {
	// body...
	var id = $('#descriptionSelect').children("option:selected").val()

	var selectedIndex = $('#descriptionSelect').children("option:selected").index()
	
	$.get('/findusersbypricelist', {pricelistid:id}, function findusersbypricelistCallBack(data, status) {
		// body...
		if(data.status == 'ok'){
			if(data.users.length > 0){
				if(!confirm('THIS PRICE LIST IS ASIGNED TO SOME USERS. IF YOU DELETE IT THOSE USERS WILL NEED A NEW ASSIGNMENT')){
					return
				}
			}
			
			$.get('/deletelistbyid', {id:id}, function deletelistbyidCallBack(data, status) {
				// body...
				if(data.status == 'ok'){
					$('#deletedMessage').show()
					setTimeout(function(){
						$('#deletedMessage').hide()
					}, 3000)

					$('#descriptionSelect option[value =' + id + ']').remove()
					selectedIndex--
					if(selectedIndex < 0){
						selectedIndex = 0
					}
					$('#descriptionSelect')[0].selectedIndex = selectedIndex
					id = $('#descriptionSelect').children("option:selected").val()
					getPriceList(id)

					set_layout()
				}
			})
		}
		else{
			alert('SOMETHING WENT WRONG, PLEASE TRY AGAIN\nSERVER MESSAGE: ' + data.message)
		}
	})
}

/**
 * Returns an array with arrays of the given size.
 *
 * @param myArray {Array} array to split
 * @param chunk_size {Integer} Size of every group
 */
function chunkArray(myArray, chunk_size){
    var index = 0;
    var arrayLength = myArray.length;
    var tempArray = [];
    
    for (index = 0; index < arrayLength; index += chunk_size) {
        myChunk = myArray.slice(index, index+chunk_size);
        // Do something if you want with the group
        tempArray.push(myChunk);
    }

    return tempArray;
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