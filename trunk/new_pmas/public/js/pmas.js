/**
 * Created by datnguyen.cntt@gmail.com.
 * Date: 9/11/13
 */
var asInitVals = new Array();
$(function() {
    var trLeng = $("#example thead").find("tr:first th").length;
    var oTable = $('#example').dataTable( {
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ 0 ] }
        ],
        "aaSorting": [[1, 'asc']],
        "oLanguage": {
            "sSearch": "Search all columns:"
        },
        "sPaginationType": "full_numbers"
    } );
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
        $('#example thead').append(htmlAppend);
    }

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
    /**
     * Datepicker
     */
    $('.datepicker').datepicker();
    /**
     * highcharts
     */
    if($('.chart-demo').length > 0){
        $('.chart-demo').highcharts({
            chart: {
                type: 'spline'
            },
            title: {
                text: 'DEMO CHART'
            },
            subtitle: {
                text: 'This is demo chart.'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: 'DEMO CHART'
                },
                min: 0,
                minorGridLineWidth: 0,
                gridLineWidth: 0,
                alternateGridColor: null,
                plotBands: [{ // Light air
                    from: 0.3,
                    to: 1.5,
                    color: 'rgba(68, 170, 213, 0.1)',
                    label: {
                        text: 'Level 0',
                        style: {
                            color: '#606060'
                        }
                    }
                }, { // Light breeze
                    from: 1.5,
                    to: 3.3,
                    color: 'rgba(0, 0, 0, 0)',
                    label: {
                        text: 'Level 1',
                        style: {
                            color: '#606060'
                        }
                    }
                }, { // Gentle breeze
                    from: 3.3,
                    to: 5.5,
                    color: 'rgba(68, 170, 213, 0.1)',
                    label: {
                        text: 'Level 2',
                        style: {
                            color: '#606060'
                        }
                    }
                }, { // Moderate breeze
                    from: 5.5,
                    to: 8,
                    color: 'rgba(0, 0, 0, 0)',
                    label: {
                        text: 'Level 3',
                        style: {
                            color: '#606060'
                        }
                    }
                }, { // Fresh breeze
                    from: 8,
                    to: 11,
                    color: 'rgba(68, 170, 213, 0.1)',
                    label: {
                        text: 'Level 4',
                        style: {
                            color: '#606060'
                        }
                    }
                }, { // Strong breeze
                    from: 11,
                    to: 14,
                    color: 'rgba(0, 0, 0, 0)',
                    label: {
                        text: 'Level 5',
                        style: {
                            color: '#606060'
                        }
                    }
                }, { // High wind
                    from: 14,
                    to: 15,
                    color: 'rgba(68, 170, 213, 0.1)',
                    label: {
                        text: 'Level 6',
                        style: {
                            color: '#606060'
                        }
                    }
                }]
            },
            tooltip: {
                valueSuffix: ' m/s'
            },
            plotOptions: {
                spline: {
                    lineWidth: 4,
                    states: {
                        hover: {
                            lineWidth: 5
                        }
                    },
                    marker: {
                        enabled: false
                    },
                    pointInterval: 3600000, // one hour
                    pointStart: Date.UTC(2009, 9, 6, 0, 0, 0)
                }
            },
            series: [{
                name: 'DEMO',
                data: [4.3, 5.1, 4.3, 5.2, 5.4, 4.7, 3.5, 4.1, 5.6, 7.4, 6.9, 7.1,
                    7.9, 7.9, 7.5, 6.7, 7.7, 7.7, 7.4, 7.0, 7.1, 5.8, 5.9, 7.4,
                    8.2, 8.5, 9.4, 8.1, 10.9, 10.4, 10.9, 12.4, 12.1, 9.5, 7.5,
                    7.1, 7.5, 8.1, 6.8, 3.4, 2.1, 1.9, 2.8, 2.9, 1.3, 4.4, 4.2,
                    3.0, 3.0]

            }, {
                name: 'CHART',
                data: [0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.1, 0.0, 0.3, 0.0,
                    0.0, 0.4, 0.0, 0.1, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0,
                    0.0, 0.6, 1.2, 1.7, 0.7, 2.9, 4.1, 2.6, 3.7, 3.9, 1.7, 2.3,
                    3.0, 3.3, 4.8, 5.0, 4.8, 5.0, 3.2, 2.0, 0.9, 0.4, 0.3, 0.5, 0.4]
            }]
            ,
            navigation: {
                menuItemStyle: {
                    fontSize: '10px'
                }
            }
        });
    }
} );
function goToModelDetail()
{
    window.location.assign("/model/detail?id=1");
}
function formSaveAndContinue(id)
{
    var form = $("#"+id);
    var continueElement = $("#continue");
    continueElement.val("yes");
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
    var continueElement = $("#continue");
    continueElement.val("no");
    $("#"+id).submit();
    return true;
}
function formConfirm(id)
{
    bootbox.confirm("Are you sure?", function(result) {
        if(result == true){
            $("#"+id).submit();
        }
        return true;
    });
}