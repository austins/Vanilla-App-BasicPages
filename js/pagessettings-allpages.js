jQuery(document).ready(function($) {
    if ($.ui && $.ui.nestedSortable) {
        $('ol.Sortable').nestedSortable({
            disableNesting: 'NoNesting',
            errorClass: 'SortableError',
            forcePlaceholderSize: true,
            handle: 'div',
            items: 'li',
            maxLevels: 1,
            axis: 'y',
            opacity: .6,
            placeholder: 'Placeholder',
            tolerance: 'pointer',
            toleranceElement: '> div',
            update: function() {
                $.post(
                    gdn.url('/pagessettings/sortpages.json'),
                    {
                        'TreeArray': $('ol.Sortable').nestedSortable('toArray', {startDepthCount: 0}),
                        'TransientKey': gdn.definition('TransientKey')
                    },
                    function(response) {
                        if (!response || !response.Result) {
                            alert("Error: didn't save order properly.");
                        }
                    }
                );
            }
        });
    }
});
