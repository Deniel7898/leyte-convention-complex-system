$(function(){
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
                $('#inventories_table tbody').html(response.table_html);

                // Close modal only if update
                if ($('#inventories_modal').data('action') === 'update') {
                    $('#inventories_modal').modal('hide');
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
})