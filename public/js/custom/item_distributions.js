$(function () {

    //add button click
    $(document).on('click', '.add-itemDistribution', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#itemDistributions_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#itemDistributions_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#itemDistributions_modal').modal('show');
            }
        })
    })

    //edit button click
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#itemDistributions_modal').data('action', 'update');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#itemDistributions_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#itemDistributions_modal').modal('show');
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
                        $('#itemDistributions_table tbody').html(response.html);

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

        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var data = new FormData(this);
        var page = form.find('input[name="page"]').val();

        $('#loading-spinner').addClass('active');

        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                if (page === 'inventory' && response.table_html) {
                    // Update the main inventory table
                    $('#inventories_table tbody').html(response.table_html);
                } else if (page === 'items' && response.item_card_html && response.history_table_html) {
                    // Update the item card and history table
                    $('#item_card_container').html(response.item_card_html);
                    $('#itemHistory_table tbody').html(response.history_table_html);
                }

                $('#itemDistributions_modal').modal('hide');
                form[0].reset();
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
            },
            error: function (xhr) {
                $('#loading-spinner').removeClass('active');

                console.log('Status:', xhr.status);
                console.log('Response:', xhr.responseText); // <-- very important for 500

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    let msg = '';
                    for (let key in errors) {
                        msg += errors[key][0] + '\n';
                    }
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

    $(function () {
        function performSearch() {
            let query = $('#itemDistribution-search').val();
            let type = $('#type-filter').val();          // dropdown for type
            let status = $('#status-filter').val(); // dropdown for status
            let category = $('#categories-filter').val(); // dropdown for category
            let distType = $('#dist-type-filter').val(); // gets "Distributed" or "Borrowed"

            $.ajax({
                url: window.liveSearchUrl, // e.g., "/items/live-search"
                type: 'GET',
                data: {
                    query: query,
                    type: type,
                    status: status,
                    category: category,
                    dist_type: distType,
                },
                success: function (response) {
                    $('#itemDistributions-table-body').html(response);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Trigger search while typing
        $('#itemDistribution-search').on('keyup', function () {
            performSearch();
        });

        // Trigger search when any dropdown changes
        $('#type-filter, #status-filter, #categories-filter, #dist-type-filter').on('change', performSearch);
    });

    $(document).on('click', '.return-item', function (e) {
        e.preventDefault();

        let url = $(this).data('url'); // route to show the return form
        $('#loading-spinner').addClass('active');

        $.get(url, function (response) {
            $('#itemDistributions_modal .modal-content').html(response);
            $('#itemDistributions_modal').modal('show');
            $('#loading-spinner').removeClass('active');
        }).fail(function () {
            $('#loading-spinner').removeClass('active');
            Swal.fire("Error!", "Could not load the return form.", "error");
        });
    });

    // Submit Return Item Form via AJAX
    $(document).on('submit', '#itemDistributions_modal form', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let method = form.attr('method');
        let data = new FormData(this);

        $('#loading-spinner').addClass('active');

        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                // Update table
                $('#itemDistributions_table tbody').html(response.table_html);

                // Close modal
                $('#itemDistributions_modal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Returned!',
                    text: response.message || 'Item returned successfully.',
                    timer: 1500,
                    showConfirmButton: false,
                    width: '400px',
                    padding: '0.8rem'
                });
            },
            error: function (xhr) {
                console.error(xhr.responseJSON || xhr.responseText);
                Swal.fire("Error!", "Could not return the item.", "error");
            },
            complete: function () {
                $('#loading-spinner').removeClass('active');
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
                        $('#itemDistributions_table tbody').html(response.table_html);

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