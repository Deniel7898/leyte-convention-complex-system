$(function () {

    // Helper: get current page for pagination
    function getCurrentPage() {
        return new URLSearchParams(window.location.search).get('page') || 1;
    }

    // Open Add Category Modal
    $(document).on('click', '.add-category', function () {
        $('#loading-spinner').addClass('active');
        $('#categories_modal').data('action', 'add');

        let url = $(this).data('url');
        $.get(url, function (response) {
            $('#categories_modal .modal-content').html(response);
            $('#loading-spinner').removeClass('active');
            $('#categories_modal').modal('show');
        });
    });

    // Open Edit Category Modal
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');
        $('#categories_modal').data('action', 'update');

        let url = $(this).data('url');
        $.get(url, function (response) {
            $('#categories_modal .modal-content').html(response);
            $('#loading-spinner').removeClass('active');
            $('#categories_modal').modal('show');
        });
    });

    // Delete Category
    $(document).on('click', '.delete', function () {
        let url = $(this).data('url');

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete",
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-spinner').addClass('active');

                $.post(url + '?page=' + getCurrentPage(), {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                })
                    .done(function (response) {
                        $('#categories_cards').html(response);

                        Swal.fire({
                            title: "Deleted!",
                            text: "Category has been removed.",
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

    // Modal Form Submit (Add/Update)
    $(document).on('submit', '#categories_modal form', function (e) {
        e.preventDefault();
        $('#loading-spinner').addClass('active');

        let form = $(this);
        let url = form.attr('action');
        let method = form.attr('method') || 'POST';
        if (form.find('input[name="_method"]').length) method = 'POST';
        let data = form.serialize();

        $.ajax({
            url: url + '?page=' + getCurrentPage(),
            type: method,
            data: data,
            dataType: 'html'
        })
            .done(function (response) {
                $('#categories_cards').html(response);

                if ($('#categories_modal').data('action') === 'update') {
                    $('#categories_modal').modal('hide');
                }

                form[0].reset();
                $('#loading-spinner').removeClass('active');

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Category saved successfully!',
                    showConfirmButton: false,
                    timer: 1500,
                    width: '400px',
                    padding: '0.8rem'
                });
            })
            .fail(function (xhr) {
                $('#loading-spinner').removeClass('active');
                let message = 'Something went wrong.';
                try {
                    let json = JSON.parse(xhr.responseText);
                    if (json.message) message = json.message;
                } catch (e) {
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

});
