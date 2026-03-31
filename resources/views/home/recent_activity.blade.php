<div class="mini-card py-3 mt-3" id="recent-activity-card">
    <h5 class="mb-3">
        <i class="bi bi-clock-history me-2"></i>
        Recent Activity
    </h5>

    <!-- Container -->
    <div id="activity-container" class="overflow-auto" style="max-height: 300px; transition: max-height 0.3s ease;">
        @foreach($recent_activities as $index => $activity)
            <div
                class="activity-item mb-2 d-flex justify-content-between align-items-start {{ $index >= 5 ? 'd-none' : '' }}">

                <div class="activity-dot me-2 
                        @if(in_array($activity->action, ['item created', 'added stock', 'added unit', 'service completed'])) bg-success
                        @elseif(in_array($activity->action, ['distributed', 'issued', 'installation'])) bg-primary
                        @elseif(in_array($activity->action, ['returned'])) bg-info
                        @elseif(in_array($activity->action, ['maintenance', 'borrowed', 'inspection'])) bg-warning
                        @elseif(in_array($activity->action, ['deleted'])) bg-danger
                        @else bg-secondary
                        @endif" style="width:10px; height:10px; border-radius:50%; margin-top:6px;">
                </div>

                <div class="flex-grow-1">
                    <div class="fw-semibold">{{ ucfirst($activity->action ?? '') }}</div>
                    <div class="text-muted small">{{ $activity->notes ?? '-' }}</div>
                </div>

                <div class="activity-time text-muted small ms-2">
                    {{ $activity->created_at->diffForHumans() }}
                </div>
            </div>
        @endforeach
    </div>

    <!-- BUTTONS (LIKE YOUR TABLE) -->
    @if(count($recent_activities) > 5)
        <div class="d-flex justify-content-between align-items-center mt-2 px-2">

            <!-- Show Less -->
            <span id="showLessBtn" class="clickable small fw-500"
                style="display:none; cursor:pointer; color: rgb(43, 45, 87);">
                Show Less
            </span>

            <div>
                <!-- Show More -->
                <span id="showMoreBtn" class="clickable small fw-500"
                    style="cursor:pointer; margin-right:10px; color: rgb(43, 45, 87);">
                    Show More
                </span>

                <!-- Show All -->
                <span id="showAllBtn" class="clickable small fw-500" style="cursor:pointer; color: rgb(43, 45, 87);">
                    Show All
                </span>
            </div>

        </div>
    @endif

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {

        const items = document.querySelectorAll(".activity-item");
        const container = document.getElementById("activity-container");

        if (items.length <= 5) return;

        let visibleCount = 5;
        const step = 10;
        const defaultHeight = 300;

        function updateView() {
            items.forEach((item, index) => {
                item.classList.toggle("d-none", index >= visibleCount);
            });

            // 🔥 Smooth height adjust
            setTimeout(() => {
                container.style.maxHeight = container.scrollHeight + "px";
            }, 50);

            // Buttons logic (same as your table)
            document.getElementById("showMoreBtn").style.display =
                visibleCount >= items.length ? "none" : "inline";

            document.getElementById("showAllBtn").style.display =
                visibleCount >= items.length ? "none" : "inline";

            document.getElementById("showLessBtn").style.display =
                visibleCount > 5 ? "inline" : "none";
        }

        // Show More (+10)
        document.getElementById("showMoreBtn").addEventListener("click", function () {
            visibleCount += step;
            if (visibleCount > items.length) visibleCount = items.length;
            updateView();
        });

        // Show All
        document.getElementById("showAllBtn").addEventListener("click", function () {
            visibleCount = items.length;
            updateView();
        });

        // Show Less (back to 5)
        document.getElementById("showLessBtn").addEventListener("click", function () {
            visibleCount = 5;

            container.style.maxHeight = defaultHeight + "px";

            updateView();
        });

        // Initial state
        container.style.maxHeight = defaultHeight + "px";
        updateView();

    });
</script>