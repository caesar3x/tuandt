/**
 * Created by datnguyen.cntt@gmail.com.
 * Date: 9/11/13
 */
var asInitVals = new Array();

$(function() {
    var oTable = $('#example').dataTable( {
        "oLanguage": {
            "sSearch": "Search all columns:"
        },
        "sPaginationType": "full_numbers"
    } );

    $("tfoot input").keyup( function () {
        /* Filter on the column (the index) of this element */
        oTable.fnFilter( this.value, $("tfoot input").index(this) );
    } );

    /*
     * Support functions to provide a little bit of 'user friendlyness' to the textboxes in
     * the footer
     */
    $("tfoot input").each( function (i) {
        asInitVals[i] = this.value;
    } );

    $("tfoot input").focus( function () {
        if ( this.className == "search_init" )
        {
            this.className = "";
            this.value = "";
        }
    } );

    $("tfoot input").blur( function (i) {
        if ( this.value == "" )
        {
            this.className = "search_init";
            this.value = asInitVals[$("tfoot input").index(this)];
        }
    } );
    /**
     * check all item
     */
    $(".check-all").on("click", function () {
        var checked = $(this).is(":checked");
        $(".check-item").prop('checked',checked);
    });
} );