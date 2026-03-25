$(function () {
    //add button click
    $(document).on("click", ".add-serviceRecord", function () {
        $("#loading-spinner").addClass("active");

        // When opening modal for add
        $("#serviceRecords_modal").data("action", "add");

        url = $(this).data("url");
        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                $("#serviceRecords_modal .modal-content").html(response);
                $("#loading-spinner").removeClass("active"); // hide
                $("#serviceRecords_modal").modal("show");
            },
        });
    });

    // open modal (edit / complete)
    $(document).on("click", ".edit, .complete-service", function () {
        $("#loading-spinner").addClass("active");

        let url = $(this).data("url");

        // detect action based on class
        let action = $(this).hasClass("complete-service")
            ? "complete"
            : "update";

        $("#serviceRecords_modal").data("action", action);

        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                $("#serviceRecords_modal .modal-content").html(response);
                $("#loading-spinner").removeClass("active");
                $("#serviceRecords_modal").modal("show");
            },
            error: function () {
                $("#loading-spinner").removeClass("active");
                Swal.fire("Error!", "Unable to load form.", "error");
            },
        });
    });

    // form submit
    $(document).on("submit", "#serviceRecords_modal form", function (e) {
        e.preventDefault();
        $("#loading-spinner").addClass("active");

        let form = $(this);
        let url = form.attr("action");
        let method = form.attr("method");
        let data = new FormData(this);
        let action = $("#serviceRecords_modal").data("action");

        $.ajax({
            url: url,
            type: method,
            data: data,
            processData: false,
            contentType: false,

            success: function (response) {
                // ADD RECORD
                if (action === "add") {
                    $("#cards-row").prepend(response.cards_html);
                }

                // UPDATE RECORD
                else if (action === "update") {
                    $("#service-card-" + response.record_id).replaceWith(
                        response.cards_html,
                    );
                }

                // COMPLETE SERVICE
                else if (action === "complete") {
                    // remove card since service is finished
                    $("#service-card-" + response.record_id).remove();
                }

                // Refresh table always
                $("#serviceRecords_table tbody").html(response.table_html);

                // Close modal
                $("#serviceRecords_modal").modal("hide");

                // Reset form
                form[0].reset();
                form.find('input[type="file"]').val("");
                $("#picture-preview").attr("src", "").hide();

                $("#loading-spinner").removeClass("active");

                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    width: "400px",
                    padding: "0.8rem",
                });
            },
            error: function (xhr) {
                $("#loading-spinner").removeClass("active");
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let msg = "";

                    $.each(xhr.responseJSON.errors, function (key, value) {
                        msg += value[0] + "\n";
                    });
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: msg,
                        width: "400px",
                        padding: "0.8rem",
                    });
                }
            },
        });
    });

    $(function () {
        function performSearch() {
            let query = $("#service-search").val();
            let serviceType = $("#type-filter").val();
            let category = $("#categories-filter").val();

            if (serviceType.toLowerCase().includes("all")) serviceType = "";
            if (category.toLowerCase().includes("all")) category = "";

            $.ajax({
                url: window.liveSearchUrl,
                type: "GET",
                data: {
                    query: query,
                    service_type: serviceType,
                    categories: category,
                },
                success: function (response) {
                    $("#serviceRecords-table-body").html(response);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                },
            });
        }

        $("#service-search").on("keyup", performSearch);
        $("#type-filter, #categories-filter").on("change", performSearch);
    });
});
