<div class="mini-card py-3 mt-3" id="recent-activity-card">
    <h5 class="mb-3">
        <i class="bi bi-clock-history me-2"></i>
        Recent Activity
    </h5>

    <!-- Scrollable container -->
    <div id="activity-container" class="overflow-auto" style="max-height: 300px; transition: max-height 0.3s ease;">
        @foreach($recent_activities as $activity)
            <div class="activity-item mb-2 d-flex justify-content-between align-items-start">
                <div class="activity-dot me-2 
                    @if(in_array($activity->action, ['item created', 'added stock', 'added unit'])) bg-success
                    @elseif(in_array($activity->action, ['distributed', 'issued', 'installation'])) bg-primary
                    @elseif(in_array($activity->action, ['returned'])) bg-info
                    @elseif(in_array($activity->action, ['maintenance', 'borrowed', 'inspection'])) bg-warning
                    @elseif(in_array($activity->action, ['service completed'])) bg-dark
                    @elseif(in_array($activity->action, ['deleted'])) bg-danger
                    @else bg-secondary
                    @endif"
                    style="width:10px; height:10px; border-radius:50%; margin-top:6px;">
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

    <!-- Toggle button -->
    @if(count($recent_activities) > 5)
        <div class="position-relative small clickable fw-500 toggle-activity-btn"
            style="cursor:pointer; color: rgb(43, 45, 87);">
            Show More
        </div>
    @endif
</div>

<script>
    // Function to bind toggle activity button
    function bindToggleActivity() {
        const btn = document.querySelector('.toggle-activity-btn');
        const container = document.getElementById('activity-container');

        if (btn && container) {
            let expanded = false;
            const collapsedHeight = '300px';
            const expandedHeight = container.scrollHeight + 'px';

            btn.onclick = function() {
                if (!expanded) {
                    container.style.maxHeight = expandedHeight;
                    btn.textContent = 'Show Less';
                    expanded = true;
                } else {
                    container.style.maxHeight = collapsedHeight;
                    btn.textContent = 'Show More';
                    expanded = false;
                }
            };
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindToggleActivity();
    });
</script>