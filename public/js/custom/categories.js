$(function () {

    //add button click
    $(document).on('click', '.add-category', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#categories_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#categories_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#categories_modal').modal('show');
            }
        })
    })

    //edit button click
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#categories_modal').data('action', 'update');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#categories_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#categories_modal').modal('show');
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

                $.post(url + '?page=' + getCurrentPage(), {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                })
                    .done(function (response) {
                        $('#categories_table tbody').html(response);
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

    //form submit (only for modal forms)
    $(document).on('submit', '#categories_modal form', function (e) {
        e.preventDefault();
        $('#loading-spinner').addClass('active');
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method') || 'POST';

        // If HTML form uses method spoofing (<input name="_method">), send as POST so _method is preserved in serialized data
        if (form.find('input[name="_method"]').length) {
            method = 'POST';
        }

        var data = form.serialize();

        $.ajax({
            url: url + '?page=' + getCurrentPage(),
            type: method,
            data: data,
            dataType: 'html'
        })
            .done(function (response) {
                $('#categories_table tbody').html(response);

                // Hide modal only if it's an update
                if ($('#categories_modal').data('action') === 'update') {
                    $('#categories_modal').modal('hide');
                }

                form.find('input[type="text"], textarea').val('');
                $('#loading-spinner').removeClass('active'); // hide

                //Sweet Alert
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Data saved successfully!',
                    showConfirmButton: false,
                    timer: 1500,
                    width: '400px',
                    padding: '0.8rem',
                    timerProgressBar: false
                });
            })
            .fail(function (xhr) {
                $('#loading-spinner').removeClass('active');
                var message = 'Something went wrong.';
                try {
                    var json = JSON.parse(xhr.responseText);
                    if (json.message) message = json.message;
                } catch (e) {
                    // not JSON
                    if (xhr.responseText) message = xhr.responseText;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    width: '400px'
                });
                console.log(xhr.responseText);
            });
    });

    //Get the Current Page for pagination
    function getCurrentPage() {
        return new URLSearchParams(window.location.search).get('page') || 1;
    }
})