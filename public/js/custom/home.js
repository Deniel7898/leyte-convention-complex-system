$(function () {
    $(document).on("submit", "form", function (e) {
        e.preventDefault();
        $("#loading-spinner").addClass("active");

        var form = $(this);
        var url = form.attr("action");
        var method = form.attr("method");
        var formData = new FormData(this);
        var page = formData.get("page"); // get page value

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (page === "inventory" && response.table_html) {
                    $("#inventories_table tbody").html(response.table_html);
                } else if (page === "items" && response.item_card_html) {
                    $("#item-card-" + response.item_id).replaceWith(
                        response.item_card_html,
                    );
                    $("#history-table-" + response.item_id).html(
                        response.history_table_html,
                    );
                } else if (page === "home") {
                    $("#home-stats-cards").html(response.stats_html);
                    $("#activity-container-wrapper").html(
                        response.recent_activity_html,
                    );
                    $("#home-dashboard-overview").html(response.overview_html);
                }

                form[0].reset();
                form.find(".is-invalid").removeClass("is-invalid");
                form.find(".error").remove();

                var modalEl = form.closest(".modal")[0];
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

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
                console.log(xhr.responseJSON);
                $("#loading-spinner").removeClass("active");
            },
        });
    });

    // ----------------------------
    // Modals
    // ----------------------------
    // Scanner Modal
    const scanModalEl = document.getElementById("scanModal");
    const scanModal = new bootstrap.Modal(scanModalEl);

    // Restock Modal
    const restockModalEl = document.getElementById("restockFormModal");
    const restockModal = new bootstrap.Modal(restockModalEl);

    // Service Modal
    const serviceModalEl = document.getElementById("serviceRecordModal");
    const serviceModal = new bootstrap.Modal(serviceModalEl);

    const completeServiceModalEl = document.getElementById(
        "completeServiceModal",
    );
    const completeServiceModal = new bootstrap.Modal(completeServiceModalEl);

    // Distribution Modal
    const distributionModalEl = document.getElementById(
        "itemDistributionModal",
    );
    const distributionModal = new bootstrap.Modal(distributionModalEl);
    const returnModalEl = document.getElementById("returnModal");
    const returnModal = new bootstrap.Modal(returnModalEl);

    // Scanner & manual input
    const scanMessage = document.getElementById("scanModalMessage");
    const manualQrInput = document.getElementById("manualQrInput");
    const manualSubmit = document.getElementById("manualSubmit");

    // Scanner state
    let scanning = false;
    let scanBuffer = "";
    let scanTimeout;
    let currentListener = null;
    let currentAction = "";

    // ----------------------------
    // Quick action buttons
    // ----------------------------
    document.querySelectorAll(".quick-action-box").forEach((box) => {
        box.addEventListener("click", () => {
            const action = box.dataset.action.toLowerCase();
            if (["restock", "distribute", "service"].includes(action)) {
                startScan(action);
            }
        });
    });

    // ----------------------------
    // Start scanning
    // ----------------------------
    function startScan(actionKey) {
        if (scanning) return;
        scanning = true;
        currentAction = actionKey;

        scanModalEl.querySelector(".modal-title").innerText =
            `Scan Item for ${actionKey.charAt(0).toUpperCase() + actionKey.slice(1)}`;
        scanMessage.innerText = "Waiting for QR scan...";
        manualQrInput.value = "";
        scanModal.show();
        manualQrInput.focus();

        if (currentListener)
            document.removeEventListener("keydown", currentListener);

        currentListener = function (e) {
            if (e.key.length === 1) scanBuffer += e.key;

            if (e.key === "Enter") {
                const code = scanBuffer.trim() || manualQrInput.value.trim();
                if (!code) return;
                scanBuffer = "";
                fetchItem(code, actionKey);
            }

            clearTimeout(scanTimeout);
            scanTimeout = setTimeout(() => (scanBuffer = ""), 100);
        };

        document.addEventListener("keydown", currentListener);

        manualSubmit.onclick = () => {
            const code = manualQrInput.value.trim();
            if (!code) return;
            fetchItem(code, actionKey);
        };
    }

    // ----------------------------
    // Fetch item data
    // ----------------------------
    function fetchItem(code, actionKey) {
        $("#loading-spinner").addClass("active");

        fetch(`/home/qr/${encodeURIComponent(code)}`)
            .then((res) => res.json())
            .then((result) => {
                $("#loading-spinner").removeClass("active");

                if (!result.success) {
                    Swal.fire({
                        icon: "warning",
                        title: "Scan Failed",
                        text: result.message || "Item not found",
                        width: "350px",
                        padding: "0.8rem",
                        timer: 10000, // 10 seconds
                        showConfirmButton: false,
                    });
                    resetScan();
                    return;
                }

                const item = result.data;

                if (actionKey === "distribute") {
                    populateDistributionModal(item);
                } else if (actionKey === "restock") {
                    populateRestockModal(item);
                } else if (actionKey === "service") {
                    populateServiceModal(item);
                }

                scanModal.hide();
                resetScan();
            })
            .catch((err) => {
                console.error(err);
                alert("Error fetching item details");
                scanModal.hide();
                resetScan();
            });
    }

    // ----------------------------
    // Reset scanning
    // ----------------------------
    function resetScan() {
        scanning = false;
        scanBuffer = "";
        if (currentListener) {
            document.removeEventListener("keydown", currentListener);
            currentListener = null;
        }
    }

    scanModalEl.addEventListener("hidden.bs.modal", () => {
        manualQrInput.value = "";
        resetScan();
    });

    // ----------------------------
    // Populate Restock Modal
    // ----------------------------
    function populateRestockModal(item, page = "home") {
        if (item.type !== "consumable") {
            Swal.fire({
                icon: "warning",
                title: "Not Allowed",
                text: "Restock is only allowed for consumable items.",
                width: "350px",
                padding: "0.8rem",
                timer: 10000, // 10 seconds
                showConfirmButton: false,
            });
            return;
        }

        restockModalEl.querySelector('input[name="item_id"]').value =
            item.item_id || "";
        document.getElementById("itemName").value = item.item_name || "";
        document.getElementById("itemCategory").value = item.category || "";
        document.getElementById("itemType").value = item.type || "";
        document.getElementById("itemUnit").value = item.unit || "";
        document.getElementById("itemSupplier").value = item.supplier || "";
        document.getElementById("quantity").value = "";
        document.getElementById("stock-notes").value = "";

        document.getElementById("quantityGroup").style.display = "block";
        document.getElementById("notesGroup").style.display = "block";
        document.getElementById("actionSubmitBtn").innerText = "Restock";

        // store page info on modal for AJAX
        restockModalEl.dataset.page = page;
        const restockModal = new bootstrap.Modal(restockModalEl);

        restockModal.show();
    }

    // ----------------------------
    // Restock Quantity trim to 3 digit
    // ----------------------------
    document.getElementById("quantity").addEventListener("input", function () {
        if (this.value.length > 3) {
            this.value = this.value.slice(0, 3);
        }
    });

    // ----------------------------
    // Populate Item Distribution Modal
    // ----------------------------
    function populateDistributionModal(item, page = "home") {
        const quantityWrapper = document.getElementById(
            "distributionQuantityWrapper",
        );
        const quantityInput = document.getElementById("distributionQuantity");
        const hiddenInventoryId = document.getElementById(
            "distributionInventoryId",
        );
        const scannedQRText = document.getElementById("distributionScannedQR");
        const typeWrapper = document.getElementById("distributionTypeWrapper");
        const typeSelect = document.getElementById("distributionType");
        const distributionItemRemaining = document.getElementById(
            "distributionItemRemaining",
        );
        const distributionScannedQR = document.getElementById(
            "distributionScannedQR",
        );

        // ----------------------------
        // VALIDATION (OUT OF STOCK)
        // ----------------------------
        if (item.type === "consumable") {
            if (!item.remaining || item.remaining <= 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Out of Stock",
                    text: "This item has no remaining stock.",
                    width: "320px",
                    padding: "0.6rem",
                    timer: 10000,
                    showConfirmButton: true,
                });
                return;
            }
        }

        // ----------------------------
        // RESET MODAL
        // ----------------------------
        quantityInput.value = 1;
        hiddenInventoryId.value = "";
        scannedQRText.innerText = "";
        distributionScannedQR.style.display = "none";
        distributionItemRemaining.style.display = "none";

        if (typeSelect) typeSelect.value = "";

        // Set item info
        document.getElementById("distributionItemId").value =
            item.item_id || "";
        document.getElementById("distributionItemName").value =
            item.item_name || "";

        // ----------------------------
        // LOGIC BASED ON ITEM TYPE
        // ----------------------------
        if (item.type === "consumable") {
            if (typeSelect) typeSelect.value = "distributed";

            quantityWrapper.style.display = "block";
            distributionItemRemaining.style.display = "block";
            distributionItemRemaining.innerText = `Available: ${item.remaining || 0}`;

            setupDistributionQuantity(item.remaining);
        } else {
            // Non-consumable
            if (typeWrapper) typeWrapper.style.display = "block";

            quantityInput.value = 1;

            if (item.units && item.units.length > 0) {
                hiddenInventoryId.value = item.units[0].id;

                distributionScannedQR.style.display = "block";
                scannedQRText.innerText = `QR Code: ${item.units[0].qr_code}`;
            }
        }

        // store page info
        restockModalEl.dataset.page = page;

        // Normalize status
        const status = (item.status || "").toLowerCase();

        // ✅ AVAILABLE → show modal
        if (status === "available") {
            distributionModal.show();
            return;
        }

        // ✅ BORROWED / ISSUED → show return modal
        if (["issued", "borrowed"].includes(status)) {
            populateReturnModal(item);
            returnModal.show();
            return;
        }

        // ❌ NOT ALLOWED
        Swal.fire({
            icon: "warning",
            title: "Not Allowed",
            text: `Item is not available. Current status: "${item.status}".`,
            width: "350px",
            padding: "0.8rem",
            iconColor: "#f59e0b",
            timer: 10000,
            showConfirmButton: true,
            customClass: {
                title: "swal-small-title",
                htmlContainer: "swal-small-text",
            },
        });
    }

    // ----------------------------
    // Distribution Quantity Limit
    // ----------------------------
    function setupDistributionQuantity(remaining) {
        const input = document.getElementById("distributionQuantity");
        if (!input) return;

        // set max properly
        input.setAttribute("max", remaining);

        // default value
        input.value = remaining > 0 ? 1 : "";

        // ❗ remove previous listeners (important fix)
        input.oninput = function () {
            let value = Number(this.value);

            if (isNaN(value) || value < 1) {
                this.value = "";
            } else if (value > remaining) {
                this.value = remaining;
            }
        };
    }

    // ----------------------------
    // Populate Return Item Modal
    // ----------------------------
    function populateReturnModal(item) {
        if (!item) return;

        document.getElementById("returnItemName").value = item.item_name || "";
        returnQR.innerText = `QR Code: ${item.units[0].qr_code}`;

        document.getElementById("returnBorrower").value = item.borrower || "";
        document.getElementById("returnDateBorrowed").value =
            item.distribution_date || "";

        const returnDateInput = document.getElementById("returnDate");

        const today = new Date().toISOString().slice(0, 10);

        // ✅ Min = distribution date
        returnDateInput.min = item.distribution_date || "";

        // ✅ Max = today
        returnDateInput.max = today;

        // ✅ Default = valid value
        if (today < item.distribution_date) {
            returnDateInput.value = item.distribution_date;
        } else {
            returnDateInput.value = today;
        }

        document.getElementById("returnDistributionId").value =
            item.distribution_id || "";

        const form = document.getElementById("returnForm");
        form.action = `/item-distributions/${item.distribution_id}/return`;
    }

    // ----------------------------
    // Populate Service Modal
    // ----------------------------
    function populateServiceModal(item, page = "home") {
        if (!item || !item.item_id) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Item not found for service.",
                timer: 10000,
                showConfirmButton: true,
            });
            return;
        }

        // BLOCK CONSUMABLE ITEMS
        if (item.type === "consumable") {
            Swal.fire({
                icon: "warning",
                title: "Not Allowed",
                text: "Consumable items cannot be serviced.",
                timer: 10000,
                showConfirmButton: true,
            });
            return;
        }

        const hiddenInventoryId = document.getElementById("serviceInventoryId");
        const scannedQRText = document.getElementById("serviceScannedQR");

        // Reset
        hiddenInventoryId.value = "";
        if (scannedQRText) scannedQRText.innerText = "";

        // Set item info
        serviceModalEl.querySelector('input[name="item_id"]').value =
            item.item_id || "";
        document.getElementById("serviceItemName").value = item.item_name || "";
        document.getElementById("technician").value = "";
        document.getElementById("serviceDescription").value = "";
        document.getElementById("serviceDate").value = new Date()
            .toISOString()
            .slice(0, 10);

        // SET INVENTORY + QR
        if (item.units && item.units.length > 0) {
            hiddenInventoryId.value = item.units[0].id;

            if (scannedQRText) {
                scannedQRText.innerText = `QR Code: ${item.units[0].qr_code}`;
                scannedQRText.style.display = "block";
            }
        }

        // Reset image preview
        const preview = document.getElementById("servicePicturePreview");
        const placeholder = document.getElementById(
            "servicePicturePlaceholder",
        );
        const fileInput = document.getElementById("servicePicture");

        preview.style.display = "none";
        placeholder.style.display = "block";
        fileInput.value = "";

        // store page info
        restockModalEl.dataset.page = page;

        // Normalize status
        const status = (item.status || "").toLowerCase();

        // AVAILABLE → show service modal
        if (status === "available") {
            serviceModal.show();
            return;
        }

        // ONGOING SERVICE → show complete modal
        if (["installation", "maintenance", "inspection"].includes(status)) {
            populateCompleteServiceModal(item);
            completeServiceModal.show();
            return;
        }

        // not allowed if the item is in distributed or issued
        Swal.fire({
            icon: "warning",
            title: "Not Allowed",
            text: `Item is not available. Current status: "${item.status}".`,
            width: "350px",
            padding: "0.8rem",
            iconColor: "#f59e0b",
            timer: 10000,
            showConfirmButton: true,
            customClass: {
                title: "swal-small-title",
                htmlContainer: "swal-small-text",
            },
        });
    }

    // ----------------------------
    // Populate Complete Service Modal
    // ----------------------------
    function populateCompleteServiceModal(item) {
        if (!item) return;

        document.getElementById("completeItemName").value =
            item.item_name || "";
        document.getElementById("completeQR").value = item.qr_code || "";
        document.getElementById("scheduleDate").value =
            item.schedule_date || "";

        const completedDateInput = document.getElementById("completedDate");

        const today = new Date().toISOString().slice(0, 10);

        // ✅ Set min (schedule date)
        completedDateInput.min = item.schedule_date || "";

        // ✅ Set max (today)
        completedDateInput.max = today;

        // ✅ Default = valid value (between min and max)
        if (today < item.schedule_date) {
            completedDateInput.value = item.schedule_date;
        } else {
            completedDateInput.value = today;
        }

        const form = document.getElementById("completeServiceForm");
        form.action = `/service/${item.service_record_id}/complete-service`;
    }

    // ----------------------------
    // Service Picture Dropzone
    // ----------------------------
    const dropzone = document.getElementById("serviceDropzone");
    const fileInput = document.getElementById("servicePicture");
    const preview = document.getElementById("servicePicturePreview");
    const placeholder = document.getElementById("servicePicturePlaceholder");

    dropzone.addEventListener("click", () => fileInput.click());
    fileInput.addEventListener("change", () => {
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.style.display = "block";
                placeholder.style.display = "none";
            };
            reader.readAsDataURL(fileInput.files[0]);
        } else {
            preview.style.display = "none";
            placeholder.style.display = "block";
        }
    });
});
