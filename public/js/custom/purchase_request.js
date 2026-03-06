$(document).ready(function () {

    /* ===============================
       SUCCESS MESSAGE FADE IN/OUT
    =============================== */
    let alertBox = $('#success-alert');

    if (alertBox.length) {
        alertBox
            .fadeIn(400)
            .delay(2000)
            .fadeOut(400);
    }


    /* ===============================
       DELETE PURCHASE REQUEST
    =============================== */
    $(document).on('click', '.delete-pr', function () {

        let url = $(this).data('url');
        let row = $(this).closest('tr');

        Swal.fire({
            title: "Delete Purchase Request?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
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
                .done(function () {

                    row.fadeOut(300, function () {
                        $(this).remove();
                    });

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "Purchase Request removed successfully.",
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

});