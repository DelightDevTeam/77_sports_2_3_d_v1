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
 <div class="col-12">
  <div class="card">
   <!-- Card header -->
   <div class="card-header pb-0">
    <div class="d-lg-flex">
     <div>
      <h5 class="mb-0">2D Opening Date & Time Dashboards</h5>
      {{-- <p class="text-sm mb-0">
                    A lightweight, extendable, dependency-free javascript HTML table plugin.
                  </p> --}}
     </div>
     <div class="ms-auto my-auto mt-lg-0 mt-4">
      <div class="ms-auto my-auto">
       {{-- <a href="" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Product</a> --}}
       {{-- <button type="button" class="btn btn-outline-primary btn-sm mb-0 py-2" data-bs-toggle="modal"
        data-bs-target="#import">
        +&nbsp; Update Permission
       </button> --}}
      
       <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1 py-2" data-type="csv" type="button"
        name="button">Export</button>
      </div>
     </div>
    </div>
   </div>
   <div class="table-responsive">
    <div class="card">
        <div class="card-header">
            <p class="text-center">
                Morning Session - 12:1 PM
            </p>
        </div>
    </div>
    <table class="table table-flush" id="permission-search">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>Opening Date</th>
            <th>Opening Time</th>
            {{-- <th>Result Number</th> --}}
            {{-- <th>Prize Number</th> --}}
            <th>Status</th>
            <th>Session</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($morningSession))
        <tr>
            <td class="text-sm font-weight-normal">1</td>
            <td class="text-sm font-weight-normal">{{ $morningSession->result_date }}</td>
            <td class="text-sm font-weight-normal">{{ $morningSession->result_time }}</td>
            {{-- <td class="text-sm font-weight-normal">{{ $morningSession->result_number ?? 'Pending' }}</td> --}}
            {{-- <td>
                <form method="POST" action="{{ route('admin.update_result_number', ['id' => $morningSession->id]) }}">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="result_number" placeholder="Enter result number" required class="form-control">
                    <button type="submit" class="btn btn-primary">Create Prize Number</button>
                </form>
            </td> --}}
            <td class="text-sm font-weight-normal">{{ ucfirst($morningSession->status) }}</td>
            <td class="text-sm font-weight-normal">{{ ucfirst($morningSession->session) }}</td>
            <td>
                
            <form id="statusForm" action="{{ route('admin.twodStatusOpenClose', ['id' => $morningSession->id]) }}" method="post">
            @csrf
            @method('PATCH')

            <!-- Switch for toggling status -->
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="statusSwitch-{{ $morningSession->id }}"
                    name="status" value="{{ $morningSession->status == 'open' ? 'closed' : 'open' }}"
                    {{ $morningSession->status == 'open' ? 'checked' : '' }}>

                    <input class="form-check-input" type="checkbox" id="statusSwitch-{{ $morningSession->id }}"
                    name="status" value="{{ $morningSession->status == 'closed' ? 'open' : 'closed' }}"
                    {{ $morningSession->status == 'closed' ? 'checked' : '' }}>

                <label class="form-check-label" for="statusSwitch-{{ $morningSession->id }}">
                    {{ $morningSession->status == 'open' ? 'Open' : 'Closed' }}
                </label>
            </div>

            <div class="form-check form-switch">
                    <input type="hidden" class="form-check-input" type="checkbox" id="statusSwitch-{{ $morningSession->id }}"
                    name="status" value="{{ $morningSession->status == 'closed' ? 'open' : 'closed' }}"
                    {{ $morningSession->status == 'closed' ? 'checked' : '' }}>
            </div>


            <!-- Submit button -->
             <button type="button" class="btn btn-primary mt-2" onclick="confirmStatusUpdate()">
        {{ $morningSession->status == 'open' ? 'Close' : 'Open' }}
    </button>
        </form>
                
            </td>
        </tr>
        @else
        <tr>
            <td colspan="8" class="text-center">No results found for today and current session.</td>
        </tr>
        @endif
    </tbody>
</table>

   </div>
  </div>
 </div>
</div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <p class="text-center">
                        Evening Session - 4:30 PM
                    </p>
                </div>
            </div>
            . <div class="table-responsive">
    <table class="table table-flush" id="permission-search">
    <thead class="thead-light">
        <tr>
            <th>#</th>
            <th>Opening Date</th>
            <th>Opening Time</th>
            {{-- <th>Result Number</th>
            <th>Prize Number</th> --}}
            <th>Status</th>
            <th>Session</th>
            <th>Update</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($eveningSession))
        <tr>
            <td class="text-sm font-weight-normal">1</td>
            <td class="text-sm font-weight-normal">{{ $eveningSession->result_date }}</td>
            <td class="text-sm font-weight-normal">{{ $eveningSession->result_time }}</td>
            {{-- <td class="text-sm font-weight-normal">{{ $eveningSession->result_number ?? 'Pending' }}</td> --}}
            {{-- <td>
                <form id="statusFormEvening" method="POST" action="{{ route('admin.update_result_number', ['id' => $eveningSession->id]) }}">
                    @csrf
                    @method('PATCH')
                    <input type="text" name="result_number" placeholder="Enter result number" required class="form-control">
                    <button type="submit" class="btn btn-primary">Create Prize Number</button>
                </form>
            </td> --}}
            <td class="text-sm font-weight-normal">{{ ucfirst($eveningSession->status) }}</td>
            <td class="text-sm font-weight-normal">{{ ucfirst($eveningSession->session) }}</td>
            <td>
              
                <form action="{{ route('admin.twodStatusOpenCloseEvening', ['id' => $eveningSession->id]) }}" method="post">
                @csrf
                @method('PATCH')
                
                <!-- Switch for toggling status -->
                <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="statusSwitchEvening-{{ $eveningSession->id }}"
            name="status" value="{{ $eveningSession->status == 'open' ? 'closed' : 'open' }}"
            {{ $eveningSession->status == 'open' ? 'checked' : '' }}>

             <input class="form-check-input" type="checkbox" id="statusSwitchEvening-{{ $eveningSession->id }}"
            name="status" value="{{ $eveningSession->status == 'closed' ? 'open' : 'closed' }}"
            {{ $eveningSession->status == 'closed' ? 'checked' : '' }}>

        <label class="form-check-label" for="statusSwitchEvening-{{ $eveningSession->id }}">
            {{ $eveningSession->status == 'open' ? 'Open' : 'Closed' }}
        </label>
    </div>

    <div class="form-check form-switch">
             <input type="hidden" class="form-check-input" type="checkbox" id="statusSwitchEvening-{{ $eveningSession->id }}"
            name="status" value="{{ $eveningSession->status == 'closed' ? 'open' : 'closed' }}"
            {{ $eveningSession->status == 'closed' ? 'checked' : '' }}>
    </div>
                <!-- Submit button -->
                <button type="submit" class="btn btn-primary mt-2" onclick="confirmStatusUpdateEvening()">{{ $eveningSession->status == 'open' ? 'closed' : 'open' }}"
                        {{ $eveningSession->status == 'open' ? 'checked' : '' }}</button>
            </form>
            </td>

        </tr>
        @else
        <tr>
            <td colspan="8" class="text-center">No results found for today and current session.</td>
        </tr>
        @endif
    </tbody>
</table>

   </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    <script>
    function confirmStatusUpdate() {
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
                document.getElementById('statusSwitch-{{ $morningSession->id }}').value = 
                    document.getElementById('statusSwitch-{{ $morningSession->id }}').checked ? 'open' : 'closed';
                document.getElementById('statusForm').submit();
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
        let successMessage = "{{ session('SuccessRequest') }}";
        if (successMessage) {
            showSuccessAlert();
        }
    };
        
</script>

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
                document.getElementById('statusSwitchEvening-{{ $eveningSession->id }}').value = 
                    document.getElementById('statusSwitchEvening-{{ $eveningSession->id }}').checked ? 'open' : 'closed';
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