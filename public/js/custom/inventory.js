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
    attachModalListener('inventories_modal');

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Add Item
    //////////////////////////////////////////////////////////////////////////////////////////////////
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

    //edit button click
    $(document).on('click', '.edit', function () {
        $('#loading-spinner').addClass('active');

        // When opening modal for update
        $('#inventories_modal').data('action', 'update');

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

    //delete button click
    $(document).on('click', '.delete', function () {
        let url = $(this).data('url');

        //Sweet ALert
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

                $.post(url, {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                })
                    .done(function (response) {
                        $('#inventories_table tbody').html(response.html);

                        Swal.fire({
                            title: "Deleted!",
                            text: "The record has been removed.",
                            icon: "success",
                            timer: 1000,
                            showConfirmButton: false,
                            width: '400px',
                            padding: '0.8rem'
                        });
                    })
                    .fail(function (xhr) {
                        Swal.fire("Error!", "Something went wrong.", "error");
                        console.log(xhr.responseText);
                    })
                    .always(function () {
                        $('#loading-spinner').removeClass('active');
                    });
            }
        });
    });

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

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Add Stock Item / Add Item Distribution
    //////////////////////////////////////////////////////////////////////////////////////////////////
    $(document).on('click', '.add-stock, .add-itemDistribution, .add-service', function (e) {
        e.preventDefault();

        let button = $(this).closest('.add-itemDistribution, .add-stock, .add-service');
        let url = button.data('url');
        let itemId = button.data('item-id');
        let type = button.data('type'); // distributed, issued, borrowed

        $('#loading-spinner').addClass('active');

        $('#inventories_modal').data('action', 'update');

        $.get(url, { item_id: itemId, type: type }, function (response) {
            // Insert modal content
            $('#inventories_modal .modal-content').html(response);
            $('#loading-spinner').removeClass('active');
            $('#inventories_modal').modal('show');

            // Wait a moment to ensure DOM exists
            setTimeout(function () {
                const $typeSelect = $('#itemDistribution-type');

                // Set the type in the modal
                if (type) {
                    $typeSelect.val(type).trigger('change');
                }

                // Toggle quantity/unit fields based on type
                if (type === 'issued') {
                    $('#unitsSection').show();      // show unit selection
                    $('#quantityWrapper').hide();   // hide quantity
                } else if (type === 'distributed' || type === 'borrowed') {
                    $('#unitsSection').hide();      // hide unit selection
                    $('#quantityWrapper').show();   // show quantity
                    $('#distributionQuantity').val(1); // default quantity 1
                }

                // Trigger change on item select to populate units if necessary
                $('#itemSelect').trigger('change');
            }, 50);
        });
    });

    //////////////////////////////////////////////////////////////////////////////////////////////////
    // Search Item
    //////////////////////////////////////////////////////////////////////////////////////////////////
    function performSearch() {
        let query = $('#inventory-search').val().trim();
        let type = $('#type-filter').val();
        let status = $('#status-filter').val();
        let category = $('#categories-filter').val();

        $.ajax({
            url: window.liveSearchUrl,
            type: 'GET',
            data: {
                query: query,
                type: type,
                status: status,
                category: category
            },
            success: function (response) {
                // Update table rows
                $('#inventory-table-body').html(response);

                // Hide pagination if any search/filter is applied
                if (query !== '' ||
                    (type && type.toLowerCase() !== 'all type') ||
                    (status && status.toLowerCase() !== 'all status') ||
                    (category && category.toLowerCase() !== 'all')) {
                    $('#inventory-pagination').hide();
                } else {
                    $('#inventory-pagination').show(); // show default pagination when no filter
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // Trigger search while typing
    $('#inventory-search').on('keyup', function () {
        performSearch();
    });

    // Trigger search when any dropdown changes
    $('#type-filter, #status-filter, #categories-filter').on('change', performSearch);

})