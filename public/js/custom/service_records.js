$(function () {
    //add button click
    $(document).on('click', '.add-serviceRecord', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#serviceRecords_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#serviceRecords_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#serviceRecords_modal').modal('show');
            }
        })
    })

    //edit button click
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#serviceRecords_modal').data('action', 'update');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#serviceRecords_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#serviceRecords_modal').modal('show');
            }
        })
    })

    //delete button click
    $(document).on('click', '.delete', function () {
        let url = $(this).data('url');

        //Sweet ALert
        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete",
            width: '400px',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-spinner').addClass('active');

                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                })
                    .done(function (response) {

                        // Refresh card & table
                        $('#service-card-' + response.record_id).remove();
                        $('#serviceRecords_table tbody').html(response.table_html);

                        Swal.fire({
                            title: "Deleted!",
                            text: "The record has been removed.",
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                            width: '400px',
                            padding: '0.8rem'
                        });
                    })
                    .fail(function (xhr) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.log(xhr.responseText);
                    })
                    .always(function () {
                        $('#loading-spinner').removeClass('active');
                    });
            }
        });
    });

    //form submit
    $(document).on('submit', 'form', function (e) {
        e.preventDefault();
        $('#loading-spinner').addClass('active');

        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var data = new FormData(this);

        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {

                if ($('#serviceRecords_modal').data('action') === 'update') {
                    // Replace existing card
                    $('#service-card-' + response.record_id).replaceWith(response.cards_html);
                } else {
                    // New record: prepend at start
                    $('#cards-row').prepend(response.cards_html);
                }

                $('#serviceRecords_table tbody').html(response.table_html);

                // Close modal only if update
                if ($('#serviceRecords_modal').data('action') === 'update', 'add') {
                    $('#serviceRecords_modal').modal('hide');
                }

                // Reset all fields
                form.find('input[type="text"], input[type="number"], textarea, input[type="date"]').val('');
                form.find('select').prop('selectedIndex', 0);
                form.find('input[type="file"]').val(null);
                $('#picture-preview').attr('src', '').hide();

                $('#loading-spinner').removeClass('active');

                // SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    width: '400px',
                    padding: '0.8rem'
                });
            },
            error: function (xhr) {
                console.log(xhr.responseJSON);
                $('#loading-spinner').removeClass('active');
            }
        });
    });

    // function performSearch() {
    //     let query = $('#item-search').val();
    //     let type = $('#type-filter').val();          // dropdown for type
    //     let status = $('#status-filter').val(); // dropdown for status
    //     let category = $('#categories-filter').val(); // dropdown for category

    //     $.ajax({
    //         url: window.liveSearchUrl, // e.g., "/items/live-search"
    //         type: 'GET',
    //         data: {
    //             query: query,
    //             type: type,
    //             status: status,
    //             category: category
    //         },
    //         success: function (response) {
    //             $('#items-table-body').html(response);
    //         },
    //         error: function (xhr) {
    //             console.error(xhr.responseText);
    //         }
    //     });
    // }

    // // Trigger search while typing
    // $('#item-search').on('keyup', function () {
    //     performSearch();
    // });

    // // Trigger search when any dropdown changes
    // $('#type-filter, #status-filter, #categories-filter').on('change', performSearch);

    // complete button click
    $(document).on('click', '.complete-service', function () {

        let url = $(this).data('url');
        let itemName = $(this).data('item');
        let qrCode = $(this).data('qr');
        let scheduleDate = $(this).data('schedule');
        let person = $(this).data('person');
        let serviceTypeValue = $(this).data('type');

        let serviceType = serviceTypeValue == 0
            ? 'Maintenance'
            : 'Installation';

        Swal.fire({
            title: "Mark as completed?",
            html: `
            <div style="text-align:left">
                <p><strong>Item:</strong> ${itemName}</p>
                <p><strong>Service Type:</strong> ${serviceType}</p>
                <p><strong>QR Code:</strong> ${qrCode}</p>
                <p><strong>Schedule Date:</strong> ${scheduleDate}</p>
                <p><strong>In-Charge:</strong> ${person}</p>
            </div>
        `,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, complete it",
            width: '400px',
        }).then((result) => {

            if (result.isConfirmed) {

                $('#loading-spinner').addClass('active');

                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                    .done(function (response) {

                        // Remove the card (because it is now completed)
                        $('#service-card-' + response.record_id).remove();
                        // Refresh table
                        $('#serviceRecords_table tbody').html(response.table_html);

                        Swal.fire({
                            icon: "success",
                            title: "Completed!",
                            text: response.message,
                            timer: 1200,
                            showConfirmButton: false,
                            width: '400px',
                            padding: '0.8rem'
                        });

                    })
                    .fail(function (xhr) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.log(xhr.responseText);
                    })
                    .always(function () {
                        $('#loading-spinner').removeClass('active');
                    });
            }
        });
    });

    // undo completion click
    $(document).on('click', '.undo-completion', function () {

        let url = $(this).data('url');
        let itemName = $(this).data('item');
        let qrCode = $(this).data('qr');
        let scheduleDate = $(this).data('schedule');
        let person = $(this).data('person');
        let serviceTypeValue = $(this).data('type');

        let serviceType = serviceTypeValue == 0
            ? 'Maintenance'
            : 'Installation';

        Swal.fire({
            title: "Undo completion?",
            html: `
        <div style="text-align:left">
            <p><strong>Item:</strong> ${itemName}</p>
            <p><strong>Service Type:</strong> ${serviceType}</p>
            <p><strong>QR Code:</strong> ${qrCode}</p>
            <p><strong>Schedule Date:</strong> ${scheduleDate}</p>
            <p><strong>In-Charge:</strong> ${person}</p>
        </div>
        `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ffc107",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, undo it",
            width: '400px',
        }).then((result) => {

            if (result.isConfirmed) {

                $('#loading-spinner').addClass('active');

                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                })
                    .done(function (response) {

                        // Add card if undo
                        $('#cards-row').prepend(response.cards_html);
                        // Update table
                        $('#serviceRecords_table tbody').html(response.table_html);

                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: response.message,
                            timer: 1200,
                            showConfirmButton: false,
                            width: '400px',
                            padding: '0.8rem'
                        });

                    })
                    .fail(function (xhr) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.log(xhr.responseText);
                    })
                    .always(function () {
                        $('#loading-spinner').removeClass('active');
                    });
            }
        });
    });
})