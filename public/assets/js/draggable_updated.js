
//----- handle list---- //
new Sortable(handleList, {
    handle: '.list-handle', // handle's class
    animation: 150
});
//----end handle list---- //


//------ nestable list------ //
$(document).ready(function() {
        var nestedSortables = $(".nested-sortable");

        // Loop through each nested sortable element
        for (var i = 0; i < nestedSortables.length; i++) {
            new Sortable(nestedSortables[i], {
                group: 'nested',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onSort: function (e) {
                    var items = e.to.children;
                    var result = [];
                    for (var i = 0; i < items.length; i++) {
                        result.push($(items[i]).data('id'));
                    }

                    $('#standard_order').val(result);
                }
            });
        }
});
//------- end nestable list----- //
