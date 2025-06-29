<style>
    #calendar { 
        height: 700px;
    }
    .fc-event {
        font-size: 0.9em;
        padding: 3px;
        cursor: move;
    }
    .modal-body dl {
        display: grid;
        grid-template-columns: max-content auto;
        gap: 0.5rem 1rem;
    }
    .modal-body dt {
        font-weight: bold;
    }
    .modal-body dd {
        margin: 0;
    }
    
    /* Unscheduled events list styles */
    #external-events {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        height: 700px;
        overflow-y: auto;
    }
    .external-event {
        margin: 10px 0;
        cursor: move;
        padding: 8px 12px;
        background-color: #3788d8;
        color: white;
        border-radius: 4px;
        font-size: 0.9em;
        transition: background-color 0.2s;
        position: relative;
    }
    .external-event:hover {
        background-color: #2e6da4;
    }
    .external-event.fc-dragging {
        opacity: 0.5;
    }
    .unscheduled-header {
        font-weight: bold;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
    }
    .event-meta {
        font-size: 0.8em;
        opacity: 0.9;
        margin-top: 4px;
    }
    .delete-event {
        position: absolute;
        right: 5px;
        top: 5px;
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        border-radius: 3px;
        padding: 2px 8px;
        font-size: 0.8em;
        cursor: pointer;
    }
    .delete-event:hover {
        background: rgba(255,0,0,0.5);
    }


   .toast {
        transition: all 0.3s ease;
        backdrop-filter: blur(5px);
        margin-bottom: 0.5rem;
        border: 1px solid;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.15);
        opacity: 0.95;
    }

    .toast:hover {
        opacity: 1;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.2);
    }

    .toast.show {
        opacity: 0.95;
        transform: translateY(0);
    }

    .toast.hide {
        opacity: 0;
        transform: translateY(20px);
    }

    .toast-body {
        padding: 0.75rem;
        display: flex;
        align-items: center;
    }

    /* Special case for warning toast to ensure text is readable */
    .bg-warning.text-dark {
        color: var(--bs-dark) !important;
    }


    /* Modern resource row design - Add this to your styles section */

    /* Modern resource header styling */
    .fc-resource-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white !important;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .fc-datagrid-cell-frame {
        padding: 12px 16px !important;
    }

    .fc-datagrid-cell-cushion {
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    /* Resource row styling with alternating colors */
    .fc-resource-timeline .fc-datagrid-body tr:nth-child(odd) {
        background-color: #f8f9fa;
    }

    .fc-resource-timeline .fc-datagrid-body tr:nth-child(even) {
        background-color: #ffffff;
    }

    .fc-resource-timeline .fc-datagrid-body tr:hover {
        background-color: #e9ecef;
        transition: background-color 0.2s ease;
    }

    /* Modern resource cell styling */
    .fc-datagrid-cell {
        border-right: 1px solid #e0e0e0 !important;
    }

    .fc-resource-timeline .fc-datagrid-cell-main {
        padding-left: 8px;
    }

    /* Add colored indicators for each resource */
    .fc-datagrid-cell-main:before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 24px;
        margin-right: 12px;
        border-radius: 2px;
        vertical-align: middle;
    }

    /* Color indicators based on resource */
    tr[data-resource-id="1"] .fc-datagrid-cell-main:before {
        background-color: #3a87ad;
    }

    tr[data-resource-id="2"] .fc-datagrid-cell-main:before {
        background-color: #5bb75b;
    }

    tr[data-resource-id="3"] .fc-datagrid-cell-main:before {
        background-color: #faa732;
    }

    tr[data-resource-id="4"] .fc-datagrid-cell-main:before {
        background-color: #da4f49;
    }

    /* Modern timeline grid */
    .fc-timeline-slot {
        border-color: #e0e0e0 !important;
    }

    .fc-timeline-slot-frame {
        border-right: 1px dashed #e0e0e0 !important;
    }

    /* Event styling improvements */
    .fc-timeline-event {
        border-radius: 4px;
        border: none !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fc-timeline-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        z-index: 10;
    }

    .fc-timeline-event .fc-event-main {
        padding: 4px 8px;
    }

    /* Resource area width */
    .fc-resource-timeline .fc-datagrid {
        width: 200px !important;
    }

    .fc-resource-timeline .fc-datagrid-header {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    /* Add subtle animations */
    .fc-timeline-event {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Modern scrollbar for resource area */
    .fc-scroller::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .fc-scroller::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .fc-scroller::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .fc-scroller::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
</style>