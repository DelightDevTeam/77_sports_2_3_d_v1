@extends('layouts.admin_app')
@section('styles')
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
</style>
@endsection
@section('content')

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <p class="text-center">
                        3D More Setting
                    </p>
                </div>
            </div>
            . <div class="table-responsive">
    <table class="table table-flush" id="permission-search">
     <thead>
        <tr>
            <th>MatchStartDate</th>
            <th>Date</th>
            <th>Time</th>
            <th>Result Number</th>
            <th>Status</th>
            <th>Open/Close</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($results as $result)
            <tr>
                <td>{{ $result->match_start_date }}</td>
                <td>{{ $result->result_date }}</td>
                <td>{{ $result->result_time }}</td>
                <td>{{ $result->result_number ?? 'N/A' }}</td>
                <td>{{ ucfirst($result->status) }}</td>
                <td>
               <form action="{{ route('admin.ThreedOpenClose', ['id' => $result->id]) }}" method="post">
    @csrf
    @method('PATCH')
    
    <!-- Switch for toggling status -->
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="statusSwitch-{{ $result->id }}"
            name="status" value="{{ $result->status == 'open' ? 'closed' : 'open' }}"
            {{ $result->status == 'open' ? 'checked' : '' }}>

        <label class="form-check-label" for="statusSwitch-{{ $result->id }}">
            {{ $result->status == 'open' ? 'Open' : 'Closed' }}
        </label>
    </div>
    
    <!-- Submit button -->
    <button type="submit" class="btn btn-primary mt-2">{{ $result->status == 'open' ? 'closed' : 'open' }}"
            {{ $result->status == 'open' ? 'checked' : '' }}</button>
</form>
                
            </td>
            </tr>
        @endforeach
    </tbody>
</table>

   </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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

        $.ajax({
            url: '/admin/two-2-results/' + resultId + '/status', // Your route
            method: 'PATCH',
            data: {
                status: newStatus,
            },
            success: function(response) {
                alert(response.message);
                // Optional: Update the status on the page
                $('#status-' + resultId).text(newStatus);
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to update status.');
            }
        });
    });
});
</script>


<script>
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

        $.ajax({
            url: '/admin/two-2-results/' + resultId + '/status', // Your route
            method: 'PATCH',
            data: {
                status: newStatus,
            },
            success: function(response) {
                alert(response.message);
                // Optional: Update the status on the page
                $('#status-' + resultId).text(newStatus);
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Failed to update status.');
            }
        });
    });
});
</script>



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