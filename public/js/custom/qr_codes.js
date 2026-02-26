// $(function () {
//     $(document).on('click', '.view', function () {
//         $('#loading-spinner').addClass('active');
//         $('#view_qrCodes_modal').data('action', 'update');

//         let url = $(this).data('url');
//         $.ajax({
//             url: url,
//             type: 'GET',
//             success: function (response) {
//                 $('#view_qrCodes_modal .modal-content').html(response);
//                 $('#loading-spinner').removeClass('active');
//                 $('#view_qrCodes_modal').modal('show');
//             }
//         })
//     })
// })