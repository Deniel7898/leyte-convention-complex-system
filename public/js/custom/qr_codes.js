// $(function () {
//     $(document).on('click', '.view', function () {
//         $('#loading-spinner').addClass('active');
//         $('#view_qrCodes_modal').data('action', 'update');

//         let url = $(this).data('url');
//         $.ajax({
//             url: url,
//             type: 'GET',
//             success: function (response) {
//                 $('#view_qrCodes_modal .modal-content').html(response);
//                 $('#loading-spinner').removeClass('active');
//                 $('#view_qrCodes_modal').modal('show');
//             }
//         })
//     })
// })
$(function () {
    $(function () {
        function performSearch() {
            let query = $('#qrCode-search').val();
            let type = $('#type-filter').val();          // dropdown for type
            let status = $('#status-filter').val(); // dropdown for status
            let category = $('#categories-filter').val(); // dropdown for category

            $.ajax({
                url: window.liveSearchUrl, // e.g., "/items/live-search"
                type: 'GET',
                data: {
                    query: query,
                    type: type,
                    status: status,
                    category: category
                },
                success: function (response) {
                    $('#qrCodes-table-body').html(response);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Trigger search while typing
        $('#qrCode-search').on('keyup', function () {
            performSearch();
        });

        // Trigger search when any dropdown changes
        $('#type-filter, #status-filter, #categories-filter').on('change', performSearch);
    });
})