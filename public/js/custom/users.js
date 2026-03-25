$(function () {
    //add button click
    $(document).on('click', '.add-user', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#users_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#users_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#users_modal').modal('show');
            }
        })
    })

    //edit button click
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#users_modal').data('action', 'update');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#users_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#users_modal').modal('show');
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
                        $('#users_table tbody').html(response.html);

                        Swal.fire({
                            title: "Deleted!",
                            text: "The user has been removed.",
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
                $('#users_table tbody').html(response.html);

                // Close modal only if update
                if ($('#users_modal').data('action') === 'update' || $('#users_modal').data('action') === 'add') {
                    $('#users_modal').modal('hide');
                }

                // Reset all fields
                form.find('input[type="text"], input[type="number"], textarea, input[type="date"], input[type="email"], input[type="password"]').val('');
                form.find('select').prop('selectedIndex', 0);
                form.find('input[type="file"]').val(null);

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

                $('#loading-spinner').removeClass('active');

                console.log(xhr.responseText); // ✅ ADD THIS (VERY IMPORTANT)

                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessage = '';

                    $.each(errors, function (key, value) {
                        errorMessage += value[0] + '<br>';
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: errorMessage
                    });
                } else {
                    // ✅ HANDLE 500 ERROR HERE
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Something went wrong. Check console/logs.'
                    });
                }
            }
        });
    });

});
