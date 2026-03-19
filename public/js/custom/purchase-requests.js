// ===============================
// AJAX SUBMIT (SAVE / UPDATE)
// ===============================
$(document).on('submit', '#purchaseRequestForm', function (e) {
    e.preventDefault();

    const form = $(this);
    const url = form.attr('action');
    const method = form.attr('method') || 'POST';
    const submitBtn = form.find('.pr-save-btn');
    const modal = $('#purchase_requests_modal');

    // Add spinner properly here
    submitBtn.prop('disabled', true);
    submitBtn.html(`
        <span class="spinner-border spinner-border-sm me-2"></span>
        Saving...
    `);

    Swal.fire({
        title: 'Saving Purchase Request...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: url,
        type: method,
        data: form.serialize(),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (html) {

            // Directly replace table using returned HTML
            $('#purchase-requests-table-body').html(html);

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Purchase Request saved successfully.',
                timer: 1500,
                showConfirmButton: false
            });

            submitBtn.prop('disabled', false);
            submitBtn.html('Save Purchase Request');

            // Close modal using Bootstrap method
            const bootstrapModal = bootstrap.Modal.getInstance(modal[0]);
            if (bootstrapModal) {
                bootstrapModal.hide();
            } else {
                modal.modal('hide');
            }
        },
        error: function (xhr) {

            submitBtn.prop('disabled', false);
            submitBtn.html('Save Purchase Request');

            if (xhr.status === 422) {
                let errorMessage = 'Please complete required fields.';
                
                // Try to extract specific validation errors from response
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    
                    // Flatten all error messages
                    Object.values(errors).forEach(fieldErrors => {
                        if (Array.isArray(fieldErrors)) {
                            errorMessages.push(...fieldErrors);
                        } else {
                            errorMessages.push(fieldErrors);
                        }
                    });
                    
                    if (errorMessages.length > 0) {
                        errorMessage = errorMessages.join('\n');
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: errorMessage
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong.'
                });
            }
        }
    });
});

// ===============================
// ADD PURCHASE REQUEST (CREATE)
// ===============================
$(document).on('click', '.add-purchase-request', function (e) {
    e.preventDefault();

    const url = $(this).data('url');
    const modal = $('#purchase_requests_modal');

    // Show loading state
    Swal.fire({
        title: 'Loading Form...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: url,
        type: 'GET',
        success: function (html) {
            Swal.close();
            modal.find('.modal-content').html(html);
            modal.modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load form.'
            });
        }
    });
});

// ===============================
// EDIT PURCHASE REQUEST (UPDATE)
// ===============================
$(document).on('click', '.edit-purchase-request', function (e) {
    e.preventDefault();

    const url = $(this).data('url');
    const modal = $('#purchase_requests_modal');

    // Show loading state
    Swal.fire({
        title: 'Loading Form...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: url,
        type: 'GET',
        success: function (html) {
            Swal.close();
            modal.find('.modal-content').html(html);
            modal.modal('show');
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load form.'
            });
        }
    });
});

// ===============================
// DELETE PURCHASE REQUEST
// ===============================
$(document).on('click', '.delete-purchase-request', function (e) {
    e.preventDefault();

    const url = $(this).data('url');
    const id = $(this).data('id');
    const row = $('tr[data-id="' + id + '"]');

    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this purchase request!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state while deleting
            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    row.fadeOut(300, function () {
                        $(this).remove();
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Purchase request has been deleted successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    let errorMessage = 'Failed to delete purchase request.';
                    
                    if (xhr.status === 403) {
                        errorMessage = 'Cannot delete this purchase request. It may have already been processed.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
    });
});
