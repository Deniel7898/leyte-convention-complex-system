$(function () {

    //add button click
    $(document).on('click', '.add-itemDistribution', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for add
        $('#itemDistributions_modal').data('action', 'add');

        url = $(this).data('url');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#itemDistributions_modal .modal-content').html(response);
                $('#loading-spinner').removeClass('active'); // hide
                $('#itemDistributions_modal').modal('show');
            }
        })
    })
})