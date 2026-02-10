$(document).on('click', '.delete-qr', function () {

    let url = $(this).data('url');

    Swal.fire({
        title: "Delete QR Code?",
        text: "This cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete",
        width: '400px'
    }).then((result) => {

        if (result.isConfirmed) {

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                dataType: 'json'
            })
            .done(function (response) {

                $('#qr_table').replaceWith(response.html);

                Swal.fire({
                    icon: "success",
                    title: "Deleted!",
                    text: "QR Code removed successfully.",
                    timer: 1500,
                    showConfirmButton: false
                });

            })
            .fail(function () {
                Swal.fire("Error!", "Something went wrong.", "error");
            });

        }

    });
});
