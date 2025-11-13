@extends('layouts.app_back')

@section('content')
<div class="container-fluid">
    <div class="nk-content-inner">
        <div class="nk-content-body">
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Sample Status Heatmap</h3>
                        <div class="nk-block-des text-soft">
                            <p>Last 30 days samples colored by status</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nk-block">
                <!-- Status Legend -->
                <div class="card mb-4">
                    <div class="card-inner">
                        <h6 class="title">Status Legend</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="legend-item d-flex align-items-center">
                                <span class="legend-color bg-success me-2" style="width: 16px; height: 16px; border-radius: 4px;"></span>
                                <small class="text-dark">REPORTED</small>
                            </div>
                            <div class="legend-item d-flex align-items-center">
                                <span class="legend-color bg-info me-2" style="width: 16px; height: 16px; border-radius: 4px;"></span>
                                <small class="text-dark">IN_PROGRESS</small>
                            </div>
                            <div class="legend-item d-flex align-items-center">
                                <span class="legend-color bg-warning me-2" style="width: 16px; height: 16px; border-radius: 4px;"></span>
                                <small class="text-dark">PENDING</small>
                            </div>
                            <div class="legend-item d-flex align-items-center">
                                <span class="legend-color bg-danger me-2" style="width: 16px; height: 16px; border-radius: 4px;"></span>
                                <small class="text-dark">REJECTED</small>
                            </div>
                            <div class="legend-item d-flex align-items-center">
                                <span class="legend-color bg-secondary me-2" style="width: 16px; height: 16px; border-radius: 4px;"></span>
                                <small class="text-dark">ALLOTED</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Heatmap -->
                <div class="card">
                    <div class="card-inner">
                        <div class="sample-heatmap-container">
                            @foreach($samples as $sample)
                                @php
                                    $statusClass = 'status-default';
                                    $status = $sample->tr04_progress ?? '';
                                    if($status === 'REPORTED') {
                                        $statusClass = 'status-reported';
                                    } elseif($status === 'IN_PROGRESS') {
                                        $statusClass = 'status-in-progress';
                                    } elseif($status === 'PENDING') {
                                        $statusClass = 'status-pending';
                                    } elseif($status === 'VERIFIED') {
                                        $statusClass = 'status-verified';
                                    } elseif($status === 'REGISTERED') {
                                        $statusClass = 'status-registered';
                                    } elseif($status === 'ALLOTED') {
                                        $statusClass = 'status-alloted';
                                    }
                                @endphp
                                <div class="sample-box {{ $statusClass }}" 
                                     data-bs-toggle="tooltip" 
                                     data-bs-placement="top"
                                     title="Ref: {{ $sample->tr04_reference_id }}
Status: {{ $status ?: 'No Status' }}
Customer: {{ $sample->customer->m07_name ?? 'N/A' }}
Created: {{ $sample->created_at->format('M d, Y') }}">
                                    <div class="sample-ref-id">
                                        {{ $sample->tr04_reference_id }}
                                    </div>
                                    <div class="sample-status">
                                        {{ $status ?: 'N/A' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($samples->count() === 0)
                            <div class="text-center py-5">
                                <div class="text-muted">
                                    <em class="icon ni ni-info-fill fs-2"></em>
                                    <p class="mt-2">No samples found in the last 30 days</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sample-heatmap-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    padding: 20px;
}

.sample-box {
    border-radius: 8px;
    padding: 5px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
    text-align: center;
    min-height: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.sample-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.sample-ref-id {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
    line-height: 1.2;
}

.sample-status {
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    opacity: 0.8;
}

/* Status Colors */
.status-reported {
    background-color: #d3d6d4;
    border-color: #b6b9b7;
    color: #5f9e6d;
}

.status-in-progress {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.status-pending {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}
.status-verified {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}

.status-registered {
    background-color: #eec1fc;
    border-color: #e2bafc;
    color: #854cee;
}

.status-alloted {
    background-color: #e2e3e5;
    border-color: #d6d8db;
    color: #383d41;
}

.status-default {
    background-color: #f8f9fa;
    border-color: #e5e9f2;
    color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
    .sample-heatmap-container {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        padding: 15px;
    }
    
    .sample-box {
        padding: 12px;
        min-height: 70px;
    }
    
    .sample-ref-id {
        font-size: 12px;
    }
    
    .sample-status {
        font-size: 10px;
    }
}

@media (max-width: 576px) {
    .sample-heatmap-container {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection