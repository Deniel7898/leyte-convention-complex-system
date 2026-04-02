$(function () {

    // OPEN MODAL (ADD)
    $(document).on("click", "#btnAdd", function () {
        $("#loading-spinner").addClass("active");

        setTimeout(() => {
            $("#request_id").val('');
            $("#request_date").val('');
            $("#itemsTable tbody").html('');

            addRow(); // always start with 1 row

            $("#loading-spinner").removeClass("active");
            $("#requestModal").modal("show");

            // 🔥 focus first input
            setTimeout(() => {
                $("#itemsTable tbody input:first").focus();
            }, 200);

        }, 300);
    });

    // EDIT
    $(document).on("click", ".btnEdit", function () {
        $("#loading-spinner").addClass("active");

        let id = $(this).data("id");

        $.ajax({
            url: `/purchase-requests/${id}`,
            type: "GET",
            success: function (response) {

                $("#request_id").val(response.id);

                // ✅ FIX DATE FORMAT HERE
                let formattedDate = response.request_date
                    ? response.request_date.split('T')[0]
                    : '';

                $("#request_date").val(formattedDate);

                $("#itemsTable tbody").html('');

                if (response.items && response.items.length > 0) {
                    response.items.forEach(item => {
                        addRow(item);
                    });
                } else {
                    addRow();
                }

                $("#loading-spinner").removeClass("active");
                $("#requestModal").modal("show");

                // 🔥 focus first input
                setTimeout(() => {
                    $("#itemsTable tbody input:first").focus();
                }, 200);
            },
            error: function () {
                $("#loading-spinner").removeClass("active");
                Swal.fire("Error!", "Failed to load data.", "error");
            }
        });
    });

    // ADD ITEM ROW
    function addRow(item = {}) {
        let row = `
        <tr>
            <td><input type="text" class="item_name form-control" value="${item.item_name ?? ''}"></td>
            <td><input type="number" class="quantity form-control" value="${item.quantity ?? ''}"></td>
            <td><input type="text" class="unit form-control" value="${item.unit ?? ''}"></td>
            <td><input type="text" class="description form-control" value="${item.description ?? ''}"></td>
            <td><button type="button" class="remove btn btn-danger btn-sm">X</button></td>
        </tr>`;
        $("#itemsTable tbody").append(row);
    }

    // ADD ITEM BUTTON (with validation)
    $("#addItem").click(function () {
        let lastRow = $("#itemsTable tbody tr:last");
        let lastValue = lastRow.find(".item_name").val();

        if (lastRow.length && !lastValue) {
            Swal.fire("Warning", "Fill the current item first.", "warning");
            return;
        }

        addRow();
    });

    // REMOVE ITEM
    $(document).on("click", ".remove", function () {
        $(this).closest("tr").remove();
    });

    // SAVE (CREATE + UPDATE)
    $(document).on("click", "#btnSave", function () {

        $("#loading-spinner").addClass("active");
        $("#btnSave").prop("disabled", true); // 🔥 prevent double click

        let id = $("#request_id").val();
        let url = id ? `/purchase-requests/${id}` : `/purchase-requests`;
        let method = id ? "PUT" : "POST";

        let items = [];

        $("#itemsTable tbody tr").each(function () {
            let item_name = $(this).find(".item_name").val();

            if (item_name) {
                items.push({
                    item_name: item_name,
                    quantity: $(this).find(".quantity").val(),
                    unit: $(this).find(".unit").val(),
                    description: $(this).find(".description").val()
                });
            }
        });

        // VALIDATION
        if (!$("#request_date").val()) {
            $("#loading-spinner").removeClass("active");
            $("#btnSave").prop("disabled", false);
            Swal.fire("Error!", "Request date is required.", "error");
            return;
        }

        if (items.length === 0) {
            $("#loading-spinner").removeClass("active");
            $("#btnSave").prop("disabled", false);
            Swal.fire("Error!", "Add at least one item.", "error");
            return;
        }

        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: method,
                request_date: $("#request_date").val(),
                items: items
            },

            success: function () {

                $("#tableContainer")
                    .fadeTo(200, 0.5)
                    .load(location.href + " #tableContainer > *", function () {
                        $(this).fadeTo(200, 1);
                    });

                $("#requestModal").modal("hide");
                $("#loading-spinner").removeClass("active");
                $("#btnSave").prop("disabled", false);

                Swal.fire({
                    icon: "success",
                    title: "Success!",
                    text: "Purchase Request saved successfully!",
                    timer: 1500,
                    showConfirmButton: false
                });
            },

            error: function (xhr) {
                $("#loading-spinner").removeClass("active");
                $("#btnSave").prop("disabled", false);

                let msg = "Something went wrong";

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors)
                        .map(e => e[0])
                        .join("\n");
                }

                Swal.fire("Error!", msg, "error");
            }
        });
    });

    // DELETE
    $(document).on("click", ".btnDelete", function () {
        let id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {

            if (result.isConfirmed) {

                $("#loading-spinner").addClass("active");

                $.ajax({
                    url: `/purchase-requests/${id}`,
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: "DELETE"
                    },

                    success: function () {

                        $("#tableContainer")
                            .fadeTo(200, 0.5)
                            .load(location.href + " #tableContainer > *", function () {
                                $(this).fadeTo(200, 1);
                            });

                        $("#loading-spinner").removeClass("active");

                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: "Purchase Request deleted.",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    },

                    error: function () {
                        $("#loading-spinner").removeClass("active");
                        Swal.fire("Error!", "Delete failed.", "error");
                    }
                });
            }
        });
    });

    // PRINT
    $(document).on("click", ".btnPrint", function () {

        $("#loading-spinner").addClass("active");

        let id = $(this).data("id");

        setTimeout(() => {
            window.open(`/purchase-requests/${id}/print`, '_blank');
            $("#loading-spinner").removeClass("active");
        }, 300);
    });

});