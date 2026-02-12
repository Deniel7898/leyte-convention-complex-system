$(function () {
     //add button click
    $(document).on('click', '.add-inventory', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#inventories_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#inventories_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#inventories_modal').modal('show');
            }
        })
    })
})