$(document).ready(function searchesReady() {
	// body...
	$('#searchesList').show()
	$('#searchDetails').hide()
	$('#gobackButton').hide()

	$('#gobackButton').click(gobackButtonClick)
	$('#deleteSelectedButton').click(deleteSelectedButtonClick)
})

function deleteSelectedButtonClick() {
	// body...
	var searchIds = $('.checkForDeleteBox:checked')
	var searchIdsArray = []
	$.each(searchIds, function(index, searchId){
		searchIdsArray.push(searchId.parentNode.parentNode.id)
	})

	$.get('/deletesearches', {searchIds:searchIdsArray}, function deleteSearchesCallBack(data, status) {
		// body...
		$.each(data.deletedids, function(index, deletedid){
		 	var row = document.getElementById(deletedid);
		    row.parentNode.removeChild(row);
		})
	})
}

function gobackButtonClick() {
	// body...
	var searchDatesTableRows = $('#searchDatesTable tbody tr')
	searchDatesTableRows.remove()

	$('#searchesList').show()
	$('#searchDetails').hide()
	$('#gobackButton').hide()
}

function rowClick(row) {
	// body...
	searchDetails(row.parentNode.id)
}

function searchDetails(searchId) {
	// body...
	$.get('/searchdetails', {searchid:searchId}, function(data, status){
		if(data.status == 'ok'){

			$('#searchesList').hide()
			$('#searchDetails').show()
			$('#gobackButton').show()


			$("#searchTextDiv")[0].innerHTML = "SEARCH TEXT: " +  '<label style="font-style: italic; display: inline;">' + data.searchtext + '</label>'

			var searchDatesTableBody = $('#searchDatesTable tbody')[0]
			var searchDatesTableRows = $('#searchDatesTable tbody tr')
			searchDatesTableRows.remove()

			for (var i = 0; i < data.searches.length; i++) {
				var searchDatesTableRow = searchDatesTableBody.insertRow(-1)
				searchDatesTableRow.classList.add('orderLinesResponsive')

				search = data.searches[i]
				searchDatesTableRow.id = search.searchid
				searchDatesTableRow.innerHTML =
					'<td class="userColumn  orderLinesResponsive">' +  search.user + '</td>' +
					'<td class="dateColumn  orderLinesResponsive">' + search.searchdate + '</td>'
			}
		}
		else{
			console.log(data.message)
		}
	})
}