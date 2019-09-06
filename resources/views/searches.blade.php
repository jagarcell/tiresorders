@extends('layouts.app')
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Searches Report</title>
        @section('styles')
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" type="text/css" href="public/css/searches.css">
        @endsection

        @section('scripts')
        <script src="//code.jquery.com/jquery-1.12.4.js"></script>
        <script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

        <script type="text/javascript" src="public/js/searches.js"></script>
        @endsection
    </head>
    <body>
    @section('content')
    <div class="mainDiv">
        <div id="searchesList" class="frameWidth">
            <div class="deleteSelectedButtonDiv">
                <input type="button" id="deleteSelectedButton" class="actionButton deleteSelectedButton" value="Delete Selected">
            </div>
            <div class="labelDiv">SEARCHES</div>
            <div class="tableDiv">
                <table id="listsearchesTable" class="listsearchesTable fixed_header">
                    <thead>
                        <tr>
                            <th class="searchTextColumn">Search Text</th>
                            <th class="matchesColumn">Found</th>
                            <th class="nomatchesColumn">Not Found</th>
                            <th class="checkForDeleteColumn"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($searches as $key => $search)
                        <tr id="{{$search->id}}">
                            <td class="searchTextColumn" onclick="rowClick(this)">{{$search->searchtext}}</td>
                            <td class="matchesColumn" onclick="rowClick(this)">{{$search->matchqty}}</td>
                            <td class="nomatchesColumn" onclick="rowClick(this)">{{$search->nomatchqty}}</td>
                            <td class="checkForDeleteColumn"><input type="checkbox" class="checkForDeleteBox"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>


        <div id="searchDetails" class="frameWidth">
            <div id="searchTextDiv">SEARCH TEXT:</div>
            <div class="searchDatesDiv">
                <table id="searchDatesTable" class="searchDatesTable fixed_header orderLinesResponsive">
                    <thead class="orderLinesResponsive">
                        <tr class="orderLinesResponsive">
                            <th class="userColumn orderLinesResponsive">User</th>
                            <th class="dateColumn alignRight orderLinesResponsive">Search Date</th>
                        </tr>
                    </thead>
                    <tbody class="orderLinesResponsive">
                        <!-- LINES ADDED FROM JAVASCRIPT -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="buttonsDiv">
            <input id="gobackButton" type="button" class="actionButton" value="GO BACK">
        </div>

    </div>
    @endsection    
    </body>
</html>
