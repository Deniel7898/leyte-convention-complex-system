$(document).ready(function () {

    /* ===============================
       FADE SESSION SUCCESS (Add Button)
    =============================== */

    let sessionAlert = $('#success-alert');

    if (sessionAlert.length) {

        sessionAlert.hide().fadeIn(400); // fade in first

        setTimeout(function () {
            sessionAlert.fadeOut(500, function () {
                $(this).remove();
            });
        }, 3000);
    }


    /* ===============================
       AUTO FADE SUCCESS MESSAGE (AJAX)
    =============================== */

    function showSuccessMessage(message) {

        $('#success-alert').remove();

        let alertHtml = `
            <div id="success-alert" class="alert-success-custom" style="display:none;">
                ${message}
            </div>
        `;

        $('.card-custom').prepend(alertHtml);

        $('#success-alert').fadeIn(400); // fade in

        setTimeout(function () {
            $('#success-alert').fadeOut(500, function () {
                $(this).remove();
            });
        }, 3000);
    }


    /* ===============================
       DELETE QR
    =============================== */

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

                    if (response.success) {

                        $('#qr_table').html(response.html);

                        showSuccessMessage("QR Code removed successfully.");

                    } else {

                        Swal.fire({
                            icon: "error",
                            title: "Cannot Delete",
                            text: response.message,
                        });

                    }

                })
                .fail(function () {
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: "Something went wrong.",
                    });
                });

            }

        });

    });

});
