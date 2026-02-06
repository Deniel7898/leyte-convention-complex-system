$(function () {
    //add button click
    $(document).on('click', '.add-category', function () {
        $('#loading-spinner').addClass('active');
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
        url = $(this).data('url');
        if (confirm('Are you sure you want to delete this category?')) {
            $('#loading-spinner').addClass('active');
            $.ajax({
                url: url,
                type: 'post',
                data: { '_token': $('meta[name="csrf-token"]').attr('content'), '_method': 'delete' },
                success: function (response) {
                    $('#categories_table tbody').html(response);
                    $('#loading-spinner').removeClass('active'); // hide
                },
                error: function (xhr) {
                    alert('error')
                    console.log(xhr.responseText);
                }, complete: function (obj) {
                    alert('Deleted successfully');
                }
            })
        }
    });

    //form submit
    $(document).on('submit', 'form', function (e) {
        e.preventDefault();
        $('#loading-spinner').addClass('active');
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var data = form.serialize();
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function (response) {
                $('#categories_table tbody').html(response);
                //$('#categories_modal').modal('hide');
                form.find('input[type="text"], textarea').val('');
                $('#loading-spinner').removeClass('active'); // hide
            }
        })
    })
})