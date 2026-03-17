$(function () {
    //////////////////////////////////////////////////////////////////
    // Safe modal helper + dynamic listener
    //////////////////////////////////////////////////////////////////

    // Function to set the hidden page input
    function setCurrentSegment() {
        const pageInput = document.getElementById('currentPageInput');
        if (pageInput) {
            const segments = window.location.pathname.replace(/^\/|\/$/g, '').split('/');
            pageInput.value = segments[0] || 'inventory';
        }
    }

    // Function to safely attach listener to modal open
    function attachModalListener(modalId) {
        const modalEl = document.getElementById(modalId);
        if (modalEl && !modalEl.dataset.listenerAttached) {
            modalEl.addEventListener('show.bs.modal', setCurrentSegment);
            modalEl.dataset.listenerAttached = "true";
        }
    }

    // Helper to open modal safely
    function showModal(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;

        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
    }

    // Attach listener
    attachModalListener('items_modal');

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Form Submit & Edit Item
    //////////////////////////////////////////////////////////////////////////////////////////////////
    $(document).on('click', '.edit, .edit-non-consumable, .complete-service, .show-return', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#items_modal').data('action', 'update');

        let url = $(this).data('url');

        $.get(url, function (response) {
            // Inject the response HTML into the modal
            $('#items_modal .modal-content').html(response);
            $('#loading-spinner').removeClass('active'); // hide spinner

            // Show modal safely
            showModal('items_modal');
        }).fail(function () {
            $('#loading-spinner').removeClass('active');
            Swal.fire("Error!", "Could not load the form.", "error");
        });
    });

    // Form submit
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

                // Update item card
                if (response.item_card_html) {
                    $('#items_cards_container').html(response.item_card_html);
                }

                // Update history table
                if (response.history_table_html) {
                    $('#history_container').html(response.history_table_html);
                }

                // Update non-consumable items table
                if (response.non_consumable_table_html) {
                    $('#items-table-body').html(response.non_consumable_table_html);
                }

                // Update inventory table if returned
                if (response.table_html) {
                    $('#inventories_table tbody').html(response.table_html);
                }

                // Close modal only if update
                if ($('#items_modal').data('action') === 'update') {
                    $('#items_modal').modal('hide');
                }

                $('#loading-spinner').removeClass('active');

                // Close modal
                form[0].reset();
                $('#picture-preview').attr('src', '').hide();

                // Show success alert
                if (response.message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            }, error: function (xhr) {

                console.log(xhr.responseJSON);
                $('#loading-spinner').removeClass('active');

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong'
                });
            }
        });

    });

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Add Stock / Item Distribution / Service / Units / Complete Service
    //////////////////////////////////////////////////////////////////////////////////////////////////
    $(document).on('click', '.add-stock, .add-itemDistribution, .add-service, .add-unit', function (e) {
        e.preventDefault();

        let button = $(this);
        let url = button.data('url');
        let itemId = button.data('item-id');
        let type = button.data('type'); // distributed, issued, borrowed

        $('#loading-spinner').addClass('active');

        // Determine which modal to use
        let modalSelector = button.hasClass('complete-service') ? '#serviceRecords_modal' : '#items_modal';

        // Set modal action
        $(modalSelector).data('action', button.hasClass('complete-service') ? 'complete' : 'update');

        // Prepare data to send
        let data = {};
        if (itemId) data.item_id = itemId;
        if (type && modalSelector === '#items_modal') data.type = type;

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function (response) {
                $(modalSelector + ' .modal-content').html(response);
                $('#loading-spinner').removeClass('active');
                $(modalSelector).modal('show');

                // Trigger type and item select handlers to properly show/hide fields
                if (modalSelector === '#items_modal') {
                    setTimeout(function () {
                        if (type) {
                            $('#itemDistribution-type').val(type).trigger('change');
                        }
                        $('#itemSelect').trigger('change');
                    }, 50);
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#loading-spinner').removeClass('active');
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Unable to load modal content.',
                    width: '400px',
                    padding: '0.8rem'
                });
            }
        });
    });

    //delete button click
    $(document).on('click', '.delete-item', function () {
        let url = $(this).data('url');

        Swal.fire({
            title: "Are you sure?",
            text: "This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete",
            width: '400px',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-spinner').addClass('active');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        Swal.fire({
                            title: "Deleted!",
                            text: response.message,
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                            width: '400px',
                            padding: '0.8rem'
                        }).then(() => {
                            // Redirect to inventory page
                            window.location.href = response.redirect;
                        });
                    },
                    error: function (xhr) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $('#loading-spinner').removeClass('active');
                    }
                });
            }
        });
    });

    //delete button click
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
            width: '400px',
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading-spinner').addClass('active');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        // Update item card
                        if (response.item_card_html) {
                            $('#items_cards_container').html(response.item_card_html);
                        }

                        // Update history table
                        if (response.history_table_html) {
                            $('#history_container').html(response.history_table_html);
                        }

                        // Update non-consumable items table
                        if (response.non_consumable_table_html) {
                            $('#items-table-body').html(response.non_consumable_table_html);
                        }

                        // Update inventory table if returned
                        if (response.table_html) {
                            $('#inventories_table tbody').html(response.table_html);
                        }

                        Swal.fire({
                            title: "Deleted!",
                            text: response.message,
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                            width: '400px',
                            padding: '0.8rem'
                        });
                    },
                    error: function (xhr) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $('#loading-spinner').removeClass('active');
                    }
                });
            }
        });
    });

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Clickable Image Lightbox
    //////////////////////////////////////////////////////////////////////////////////////////////////
    document.addEventListener('DOMContentLoaded', () => {
        const clickableImgs = document.querySelectorAll('.clickable-image');
        const lightbox = document.getElementById('universalLightbox');
        const lightboxImg = document.getElementById('universalLightboxImg');
        const closeBtn = document.getElementById('universalLightboxClose');

        clickableImgs.forEach(img => {
            img.addEventListener('click', () => {
                lightboxImg.src = img.dataset.full;
                lightbox.style.display = 'flex';
            });
        });

        const closeLightbox = () => {
            lightbox.style.display = 'none';
            lightboxImg.src = '';
        };

        closeBtn.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', e => {
            if (e.target === lightbox) closeLightbox();
        });
    });
})