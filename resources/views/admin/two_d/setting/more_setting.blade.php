@extends('layouts.admin_app')
@section('styles')
 {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> --}}

<style>
.transparent-btn {
 background: none;
 border: none;
 padding: 0;
 outline: none;
 cursor: pointer;
 box-shadow: none;
 appearance: none;
 /* For some browsers */
}
/* Bootstrap modals have a high z-index by default */
.modal {
  z-index: 1050; /* Default for Bootstrap 5 modals */
}

</style>
@endsection
@section('content')

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <p class="text-center">
                        2D More Setting
                    </p>
                </div>
            </div>
            . <div class="table-responsive">
    <table class="table table-flush" id="permission-search">
     <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Result Number</th>
            <th>Session</th>
            <th>Status</th>
            <th>Open/Close</th>
        </tr>
    </thead>
    <tbody>
        @php 
        $status = '';
        @endphp
        @if(isset($results))
        @foreach ($results as $result)
            <tr>
                <td>{{ $result->result_date }}</td>
                <td>{{ $result->result_time }}</td>
                <td>{{ $result->result_number ?? 'N/A' }}</td>
                <td>{{ ucfirst($result->session) }}</td>
                <td>{{ ucfirst($result->status) }}</td>
                <td>
                {{-- <button class="toggle-status"
                        data-id="{{ $result->id }}"
                        data-status="{{ $result->status === 'open' ? 'closed' : 'open' }}">
                    Open/Close
                </button> --}}

                <form action="{{ route('admin.twodStatusOpenCloseEvening', ['id' => $result->id]) }}" method="post">
                @csrf
                @method('PATCH')
                
                <!-- Switch for toggling status -->
                <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="statusSwitchEvening-{{ $result->id }}"
            name="status" value="{{ $result->status == 'open' ? 'closed' : 'open' }}"
            {{ $result->status == 'open' ? 'checked' : '' }}>

             <input class="form-check-input" type="checkbox" id="statusSwitchEvening-{{ $result->id }}"
            name="status" value="{{ $result->status == 'closed' ? 'open' : 'closed' }}"
            {{ $result->status == 'closed' ? 'checked' : '' }}>

        <label class="form-check-label" for="statusSwitchEvening-{{ $result->id }}">
            {{ $result->status == 'open' ? 'Open' : 'Closed' }}
        </label>
    </div>

    <div class="form-check form-switch">
             <input type="hidden" class="form-check-input" type="checkbox" id="statusSwitchEvening-{{ $result->id }}"
            name="status" value="{{ $result->status == 'closed' ? 'open' : 'closed' }}"
            {{ $result->status == 'closed' ? 'checked' : '' }}>
    </div>
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary mt-2" onclick="confirmStatusUpdateEvening()">{{ $result->status == 'open' ? 'closed' : 'open' }}"
                        {{ $result->status == 'open' ? 'checked' : '' }}</button>
            </form>
                
            </td>
            </tr>
            @php 
            $status = $result->id;
            @endphp
        @endforeach
        @else
        <p>no data found</p>
        @endif
    </tbody>
</table>

   </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

<script>
    function confirmStatusUpdateEvening() {
        // Show SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to change the status. Proceed?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            // If confirmed, submit the form
            if (result.isConfirmed) {
                // Set the value of 'status' to 'closed' if the checkbox is unchecked
                document.getElementById('statusSwitchEvening-{{ $status }}').value = 
                    document.getElementById('statusSwitchEvening-{{ $status }}').checked ? 'open' : 'closed';
                document.getElementById('statusFormEvening').submit();
            }
        });
    }

    // Function to show success Sweet Alert after form submission
    function showSuccessAlert() {
        Swal.fire({
            title: 'Success!',
            text: 'Status updated successfully.',
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }

     // Call the showSuccessAlert function when the page loads
    window.onload = function() {
        // Check if the success message is present in the session
        let successMessage = "{{ session('SuccessRequestEvening') }}";
        if (successMessage) {
            showSuccessAlert();
        }
    };
        
</script>

{{-- <script>
$(document).ready(function() {
    // Include CSRF token in AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.toggle-status').on('click', function() {
        const resultId = $(this).data('id'); // The ID of the result
        const newStatus = $(this).data('status'); // The new status to set

        // Ask for confirmation before changing the status
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to change the status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/two-2-results/' + resultId + '/status', // Your route
                    method: 'PATCH',
                    data: {
                        status: newStatus,
                    },
                    success: function(response) {
                        // Display success message with SweetAlert
                        Swal.fire('Updated!', response.message, 'success');
                        // Optional: Update the status on the page
                        $('#status-' + resultId).text(newStatus);
                        // Auto-reload the page after a brief delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500); // 1500 milliseconds = 1.5 seconds
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        Swal.fire('Error', 'Failed to update status.', 'error');
                    }
                });
            }
        });
    });
});
</script> --}}

{{-- <script>
$(document).ready(function() {
    // Include CSRF token in AJAX headers
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.toggle-status-evening').on('click', function() {
        const resultId = $(this).data('id'); // The ID of the result
        const newStatus = $(this).data('status'); // The new status to set

        // Ask for confirmation before changing the status
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to change the status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/two-2-results/' + resultId + '/status', // Your route
                    method: 'PATCH',
                    data: {
                        status: newStatus,
                    },
                    success: function(response) {
                        // Display success message with SweetAlert
                        Swal.fire('Updated!', response.message, 'success');
                        // Optional: Update the status on the page
                        $('#status-' + resultId).text(newStatus);
                        // Auto-reload the page after a brief delay
                        setTimeout(function() {
                            location.reload();
                        }, 1500); // 1500 milliseconds = 1.5 seconds
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        Swal.fire('Error', 'Failed to update status.', 'error');
                    }
                });
            }
        });
    });
});
</script> --}}



<script>
if (document.getElementById('permission-search')) {
 const dataTableSearch = new simpleDatatables.DataTable("#permission-search", {
  searchable: true,
  fixedHeight: false,
  perPage: 7
 });

 document.querySelectorAll(".export").forEach(function(el) {
  el.addEventListener("click", function(e) {
   var type = el.dataset.type;

   var data = {
    type: type,
    filename: "material-" + type,
   };

   if (type === "csv") {
    data.columnDelimiter = "|";
   }

   dataTableSearch.export(data);
  });
 });
};
</script>
<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
 return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

@endsection