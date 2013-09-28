/**
 * Created by datnguyen.cntt@gmail.com.
 * Date: 9/11/13
 */
var asInitVals = new Array();
$(function() {
    var currencySelect = $("#select-currency");
    if(currencySelect.length > 0){
        if(currencySelect.val() == 'none'){
            $("#chart-view").html('<div class="alert alert-info">Please choose currency</div>');
        }
    }
    $(".uploadform").click(function(e){
        e.stopPropagation();
        if($("#import-format").length > 0){
            var fileType = $("#import-format").val();
            if(fileType == '' || fileType == null || fileType == 'Select format'){
                bootbox.alert("You must select format type");
                return true;
            }else{
                $.colorbox({inline:true,href:"#upload-form",width:"60%"});
            }
        }
    });
    /*$("#import-format").colorbox({inline:true, width:"60%"});*/

    var oTable = $('.example').dataTable( {
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ 0 ] }
        ],
        "aaSorting": [[1, 'asc']],
        "oLanguage": {
            "sSearch": "Search all columns:"
        },
        "sPaginationType": "full_numbers"
    } );
    $(".example").each(function(){
        var thead = $(this).find('thead');
        var trLeng = thead.find("tr:first > th").length;
        if(trLeng > 0){
            var htmlAppend = '<tr class="tr-search">' +
                '<th><input type="hidden"></th>';
            for (var i = 1; i < trLeng; i++) {
                htmlAppend = htmlAppend + '<th>' +
                    ' <div class="input-group-sm">' +
                    '<input type="text" class="form-control search_init" />' +
                    '</div>' +
                    '</th>';
            }
            htmlAppend = htmlAppend + '</tr>';
            thead.append(htmlAppend);
        }
    });
    $(".tr-search input").keyup( function () {
        /* Filter on the column (the index) of this element */
        oTable.fnFilter( this.value, $(".tr-search input").index(this) );
    } );

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
     * the footer
     */
    $(".tr-search input").each( function (i) {
        asInitVals[i] = this.value;
    } );

    $(".tr-search input").focus( function () {
        if ( $(this).hasClass("search_init"))
        {
            $(this).removeClass("search_init");
            this.value = "";
        }
    } );

    $(".tr-search input").blur( function (i) {
        if ( this.value == "" )
        {
            $(this).addClass("search_init");
            this.value = asInitVals[$(".tr-search input").index(this)];
        }
    } );
    /**
     * check all item
     */
    $(".check-all").on("click", function () {
        var checked = $(this).is(":checked");
        $(".check-item").prop('checked',checked);
    });
    $(".checkall").on("click", function () {
        var checked = $(this).is(":checked");
        $(".checkitem").prop('checked',checked);
    });
    /**
     * Datepicker
     */
    $('.datepicker').datepicker({
        dateFormat : "dd-mm-yy"
    });

} );
function goToModelDetail()
{
    window.location.assign("/model/detail?id=1");
}
function formSaveAndContinue(id)
{
    var form = $("#"+id);
    if($("#continue").length > 0){
        var continueElement = $("#continue");
        continueElement.val("yes");
    }
    form.submit();
    return true;
}
function formReset(id)
{
    $('#'+id)[0].reset();
    return true;
}
function formSave(id)
{
    if($("#continue").length > 0){
        var continueElement = $("#continue");
        continueElement.val("no");
    }
    $("#"+id).submit();
    return true;
}
function formConfirm(id)
{
    var itemsChecked = $('input[name="ids[]"]:checked');
    if(itemsChecked.length == 0){
        bootbox.alert("You must select items");
        return true;
    }
    bootbox.confirm("Are you sure?", function(result) {
        if(result == true){
            $("#"+id).submit();
        }
        return true;
    });
}
function confirmDelete(url)
{
    bootbox.confirm("Are you sure you want to do this?", function(result) {
        if(result == true){
            window.location.assign(url);
        }
        return true;
    });
}

function exportProducts()
{
    var format = $("#export-format").val();
    if(format == 'none'){
        bootbox.alert("You must select format data");
        return false;
    }
    var ids = new Array();
    $(".check-item").each(function(){
        if($(this).is(":checked")){
            ids.push($(this).val());
        }
    });
    var params = new Object();
    params.id = ids;
    var urlParams = $.param(params);
    var url = '/product/export/format/'+format+'?'+urlParams;
    window.location.assign(url);
    return true;
}
function exportRecyclers()
{
    var format = $("#export-format").val();
    if(format == 'none'){
        bootbox.alert("You must select format data");
        return false;
    }
    var ids = new Array();
    $(".check-item").each(function(){
        if($(this).is(":checked")){
            ids.push($(this).val());
        }
    });
    var params = new Object();
    params.id = ids;
    var urlParams = $.param(params);
    var url = '/recycler/export/format/'+format+'?'+urlParams;
    window.location.assign(url);
    return true;
}
function exportExchange(currency,startTime,endTime)
{
    var format = $("#export-format").val();
    if(format == 'none'){
        bootbox.alert("You must select format data");
        return false;
    }
    var url = '/exchange/export/format/'+format+'/currency/'+currency+'/start/'+startTime+'/end/'+endTime;
    window.location.assign(url);
    return true;
}
function importRecyclerModels()
{
    $.colorbox.close();
}
function saveImportRecord(url){
    $.get( url, function( data ) {
        $("#show-msg").html('<div class="alert alert-success"><span>'+data+'</span></div>');
    });

    return true;
}
function loadExchangeData()
{
    var selector = $("#select-currency");
    var currency = selector.val();
    if(currency == 'none'){
        bootbox.alert("You must select currency");
        return true;
    }
    $("#chart-view").html('<div class="form-group" style="text-align: center;"><img src="/images/loading.gif" width="48px" style="width: 48px;"></div>');
    var startTime = $("#start-time").val();
    var endTime = $("#end-time").val();
    var urlTableLoad = '/exchange/load-table/currency/'+currency+'/'+ 'start/'+startTime+'/end/'+endTime;
    var urlChartLoad = '/exchange/load-chart/currency/'+currency+'/'+ 'start/'+startTime+'/end/'+endTime;
    $.get( urlTableLoad, function( data ) {
        $("#table-view").html(data);
    });
    $.get( urlChartLoad, function( data ) {
        $("#chart-view").html(data);
    });
    return true;
}
function exportPriceCompare(tdm)
{
    var format = $("#export-format").val();
    if(format == 'none'){
        bootbox.alert("You must select format data");
        return false;
    }
    var ids = new Array();
    if(!$(".check-item:checked").length){
        $(".check-item").each(function(){
            ids.push($(this).val());
        });
    }else{
        $(".check-item").each(function(){
            if($(this).is(":checked")){
                ids.push($(this).val());
            }
        });
    }
    var params = new Object();
    params.id = ids;
    var urlParams = $.param(params);
    var url = '/product/export-price-compare/tdm/'+tdm+'/format/'+format+'?'+urlParams;
    window.location.assign(url);
    return true;
}
function submitHistoricalModelPrice()
{
    var startTime = $("#start-time").val();
    if(!startTime || !startTime.length){
        bootbox.alert("You must select start time");
        return false;
    }
    var endTime = $("#end-time").val();
    if(!endTime || !endTime.length){
        bootbox.confirm("Are you sure you do not want to set end time?", function(result) {
            return true;
        });
    }
    var startTimeSplit = startTime.split("-");
    var endTimeSplit = endTime.split("-");
    var startParse  = new Date(startTimeSplit[2],startTimeSplit[1],startTimeSplit[0]);
    var endParse  = new Date(endTimeSplit[2],endTimeSplit[1],endTimeSplit[0]);
    if(startParse.getTime() > endParse.getTime()){
        bootbox.alert("You must select end time greater than start time");
        return false;
    }
}