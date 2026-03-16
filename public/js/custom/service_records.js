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

    // open modal (edit / complete)
    $(document).on('click', '.edit, .complete-service', function () {

        $('#loading-spinner').addClass('active');

        let url = $(this).data('url');

        // detect action based on class
        let action = $(this).hasClass('complete-service') ? 'complete' : 'update';

        $('#serviceRecords_modal').data('action', action);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {

                $('#serviceRecords_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active');
                $('#serviceRecords_modal').modal('show');

            },
            error: function () {
                $('#loading-spinner').removeClass('active');
                Swal.fire("Error!", "Unable to load form.", "error");
            }
        });

    });

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

    // form submit
    $(document).on('submit', '#serviceRecords_modal form', function (e) {

        e.preventDefault();
        $('#loading-spinner').addClass('active');

        let form = $(this);
        let url = form.attr('action');
        let method = form.attr('method');
        let data = new FormData(this);
        let action = $('#serviceRecords_modal').data('action');

        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,

            success: function (response) {

                // ADD RECORD
                if (action === 'add') {

                    $('#cards-row').prepend(response.cards_html);

                }

                // UPDATE RECORD
                else if (action === 'update') {

                    $('#service-card-' + response.record_id)
                        .replaceWith(response.cards_html);

                }

                // COMPLETE SERVICE
                else if (action === 'complete') {

                    // remove card since service is finished
                    $('#service-card-' + response.record_id).remove();

                }

                // Refresh table always
                $('#serviceRecords_table tbody').html(response.table_html);

                // Close modal
                $('#serviceRecords_modal').modal('hide');

                // Reset form
                form[0].reset();
                form.find('input[type="file"]').val('');
                $('#picture-preview').attr('src', '').hide();

                $('#loading-spinner').removeClass('active');

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    width: '400px',
                    padding: '0.8rem'
                });
            }, error: function (xhr) {
                $('#loading-spinner').removeClass('active');
                if (xhr.responseJSON && xhr.responseJSON.errors) {

                    let msg = '';

                    $.each(xhr.responseJSON.errors, function (key, value) {
                        msg += value[0] + '\n';
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: msg,
                        width: '400px',
                        padding: '0.8rem'
                    });
                }
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
})