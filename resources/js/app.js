// ==========================
// 🚀 app.js (FIXED)
// ==========================
import "./bootstrap";
import * as bootstrap from "bootstrap";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
import axios from "axios";

window.Pusher = Pusher;
window.axios = axios;
window.bootstrap = bootstrap;

// ✅ Axios default headers
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// ==========================
// 🔌 Laravel Echo (Reverb)
// ==========================
window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname, // ✅ IMPORTANT FIX (works for localhost & IP)
    wsPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
});

console.log("✅ Echo initialized...");

// ==========================
// 💡 FIX: Rebind Bootstrap + Events
// ==========================
function reinitializeUI() {
    // 🔽 Dropdowns
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((el) => {
        let instance = bootstrap.Dropdown.getInstance(el);
        if (instance) instance.dispose();
        new bootstrap.Dropdown(el);
    });

    // 🔽 Tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
        let instance = bootstrap.Tooltip.getInstance(el);
        if (instance) instance.dispose();
        new bootstrap.Tooltip(el);
    });

    // 🔥 IMPORTANT: Fix dropdown click manually (fallback)
    document.querySelectorAll(".dropdown-toggle").forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            let dropdown = bootstrap.Dropdown.getOrCreateInstance(this);
            dropdown.toggle();
        });
    });

    console.log("🔁 UI Reinitialized");
}

// ==========================
// 💬 Helper: Show Success Alert (ALL actions)
// ==========================
function showSuccessAlertForAction(action) {
    let message = "";

    switch (action) {
        case "restock":
            message = "✅ Item successfully restocked!";
            break;
        case "distribute":
            message = "✅ Item successfully distributed!";
            break;
        case "return":
            message = "✅ Item successfully returned!";
            break;
        case "service":
            message = "✅ Item sent to service!";
            break;
        case "complete":
            message = "✅ Service completed!";
            break;
        case "add":
            message = "✅ Item successfully added!";
            break;
        case "edit":
            message = "✅ Item successfully updated!";
            break;
        default:
            message = "✅ Action completed successfully!";
    }

    let container = document.querySelector("#alert-container");
    if (!container) {
        container = document.createElement("div");
        container.id = "alert-container";
        container.className = "position-fixed top-0 end-0 p-3";
        container.style.zIndex = 9999;
        document.body.appendChild(container);
    }

    const alert = document.createElement("div");
    alert.className = "alert alert-success alert-dismissible fade show";
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    container.appendChild(alert);

    setTimeout(() => {
        bootstrap.Alert.getOrCreateInstance(alert).close();
    }, 10000);
}

// ==========================
// 📡 LISTEN FOR EVENTS (FINAL)
// ==========================
window.Echo.channel("inventory").listen(".item.action", async (e) => {
    console.log("📡 EVENT RECEIVED:", e);

    const action = e.action;

    // ==========================
    // 🧱 Update SINGLE ITEM CARD (NO DUPLICATE)
    // ==========================
    if (document.querySelector("#items_cards_container")) {
        try {
            const res = await axios.get("/items/cards-partial", {
                params: { item_id: e.item.id },
            });

            const existingCard = document.querySelector(
                `#item-card-${e.item.id}`,
            );

            if (existingCard) {
                existingCard.outerHTML = res.data.item_card_html;
            }

            console.log("✅ Item card updated");
        } catch (err) {
            console.warn(
                "⚠️ Item update failed:",
                err.response?.data || err.message,
            );
        }
    }

    // ==========================
    // 📜 Update HISTORY (NO DOUBLE)
    // ==========================
    if (document.querySelector("#history_container")) {
        try {
            const res = await axios.get("/items/history-partial", {
                params: { item_id: e.item.id },
            });

            $("#history_container").html(res.data.history_table_html);

            console.log("✅ History updated");
        } catch (err) {
            console.warn(
                "⚠️ History update failed:",
                err.response?.data || err.message,
            );
        }
    }

    // ==========================
    // 📊 Inventory Table
    // ==========================
    if (document.querySelector("#inventories_table tbody")) {
        try {
            const res = await axios.get("/inventory/table-partial");
            $("#inventories_table tbody").html(res.data.table_html);
        } catch (err) {
            console.error(
                "❌ Inventory failed:",
                err.response?.data || err.message,
            );
        }
    }

    // ==========================
    // 📈 Home Stats
    // ==========================
    if (document.querySelector("#home-stats-cards")) {
        try {
            const res = await axios.get("/home/stats-partial");
            $("#home-stats-cards").html(res.data.stats_html);
        } catch (err) {
            console.warn("⚠️ Stats failed:", err.response?.data || err.message);
        }
    }

    // ==========================
    // 📜 Activity
    // ==========================
    if (document.querySelector("#activity-container-wrapper")) {
        try {
            const res = await axios.get("/home/activity-partial");
            $("#activity-container-wrapper").html(
                res.data.recent_activity_html,
            );
        } catch (err) {
            console.warn(
                "⚠️ Activity failed:",
                err.response?.data || err.message,
            );
        }
    }

    // ==========================
    // 🔁 Reinitialize UI
    // ==========================
    reinitializeUI();

    // ==========================
    // 💬 Show Success Message
    // ==========================
    showSuccessAlertForAction(action);
});

// ==========================
// 🔄 Fix Vite Hot Reload
// ==========================
if (import.meta.hot) {
    import.meta.hot.accept(() => {
        reinitializeUI();
    });
}
