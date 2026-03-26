$(function () {

    //edit button click
    $(document).on('click', '.edit, .return-item', function (e) {
        e.preventDefault();

        let url = $(this).data('url');
        // detect action based on class
        let action = $(this).hasClass('return-item') ? 'return' : 'update';
        $('#itemDistributions_modal').data('action', action);
        $('#loading-spinner').addClass('active');

        $.get(url)
            .done(function (response) {
                $('#itemDistributions_modal .modal-content').html(response);
                $('#itemDistributions_modal').modal('show');
            })
            .fail(function () {
                Swal.fire("Error!", "Could not load the form.", "error");
            })
            .always(function () {
                $('#loading-spinner').removeClass('active');
            });
    });

    // Consolidated form submit handler
    $(document).on('submit', '#itemDistributions_modal form', function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let method = form.attr('method');
        let data = new FormData(this);

        let action = $('#itemDistributions_modal').data('action'); // add / update / return / complete

        $('#loading-spinner').addClass('active');

        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {

                // ADD RECORD
                if (action === 'add') {
                    $('#cards-row').prepend(response.cards_html);
                }

                // UPDATE RECORD (flexible)
                else if (action === 'update') {
                    let cardId = '#distribution-card-' + response.distribution_id;

                    if (response.remove_card) {
                        // Remove card if backend indicates it should be removed
                        $(cardId).remove();
                    }
                    else if ($(cardId).length) {
                        // Replace existing card if it exists
                        $(cardId).replaceWith(response.cards_html);
                    }
                    else if (response.cards_html) {
                        // Prepend if card doesn't exist yet
                        $('#cards-row').prepend(response.cards_html);
                    }
                }

                // COMPLETE SERVICE
                else if (action === 'return') {
                    // remove card since service is finished
                    $('#distribution-card-' + response.distribution_id).remove();
                }

                // Always refresh the table if HTML is returned
                if (response.table_html) {
                    $('#itemDistributions_table tbody').html(response.table_html);
                }

                // Close modal
                $('#itemDistributions_modal').modal('hide');

                // Reset form and file inputs
                form[0].reset();
                form.find('input[type="file"]').val('');
                form.find('.preview-img').attr('src', '').hide(); // generic for all preview images

                $('#loading-spinner').removeClass('active');

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message || 'Action completed successfully!',
                    showConfirmButton: false,
                    timer: 1500,
                    width: '400px',
                    padding: '0.8rem'
                });
            },
            error: function (xhr) {
                let msg = 'Something went wrong.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).map(e => e[0]).join('\n');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: msg,
                    width: '400px',
                    padding: '0.8rem'
                });
                console.error(xhr.responseJSON || xhr.responseText);
            },
            complete: function () {
                $('#loading-spinner').removeClass('active');
            }
        });
    });

    $(function () {
        function performSearch() {
            let query = $('#itemDistribution-search').val();
            let type = $('#type-filter').val();          // dropdown for type
            let status = $('#status-filter').val(); // dropdown for status
            let category = $('#categories-filter').val(); // dropdown for category
            let distType = $('#dist-type-filter').val(); // gets "Distributed" or "Borrowed"

            $.ajax({
                url: window.liveSearchUrl, // e.g., "/items/live-search"
                type: 'GET',
                data: {
                    query: query,
                    type: type,
                    status: status,
                    category: category,
                    dist_type: distType,
                },
                success: function (response) {
                    $('#itemDistributions-table-body').html(response);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Trigger search while typing
        $('#itemDistribution-search').on('keyup', function () {
            performSearch();
        });

        // Trigger search when any dropdown changes
        $('#type-filter, #status-filter, #categories-filter, #dist-type-filter').on('change', performSearch);
    });
})