/* eslint new-cap: [2, {"capIsNewExceptions": ["DataTable"]}] */

$(document).ready(function() {
    jQuery.extend(jQuery.fn.dataTableExt.oSort,
        {
        'percent-pre': function (a) {
            var x = (a == '-') ? 0 : a.replace(/ %/, '');
            return parseFloat(x);
        },
        'percent-asc': function (a, b) {
            return ((a < b) ? -1 : ((a > b) ? 1 : 0));
        },
        'percent-desc': function (a, b) {
            return ((a < b) ? 1 : ((a > b) ? -1 : 0));
        }
        }
    );

    $('#tier_details').DataTable({
        info: false,
        paging: false,
        searching: false,
        aoColumns: [
            null,
            { sType: "percent" },
            null,
            null,
            null,
        ]
    });

});
