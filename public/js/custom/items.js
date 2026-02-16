$(function () {

    //add button click
    $(document).on('click', '.add-item', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#items_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#items_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#items_modal').modal('show');
            }
        })
    })

    //edit button click
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#items_modal').data('action', 'update');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#items_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#items_modal').modal('show');
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
                        $('#items_table tbody').html(response.html);

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
                $('#items_table tbody').html(response.html);

                // Close modal only if update
                if ($('#items_modal').data('action') === 'update') {
                    $('#items_modal').modal('hide');
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

    $(function () {
        function performSearch() {
            let query = $('#item-search').val();
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
                    $('#items-table-body').html(response);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Trigger search while typing
        $('#item-search').on('keyup', function () {
            performSearch();
        });

        // Trigger search when any dropdown changes
        $('#type-filter, #status-filter, #categories-filter').on('change', performSearch);
    });
})