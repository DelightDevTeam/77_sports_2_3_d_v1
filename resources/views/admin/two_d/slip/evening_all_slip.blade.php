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
  
    
        <h2>All Evening Slips</h2>

        <div class="card mt-1">
            <div class="card-header">
                <p style="color: #f5bd02" class="text-center">Total Evening Amount: {{ $total_amount }}</p>
            </div>
        </div>

        @if ($records->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-flush" id="users-search">
                    <thead>
            <tr>
                <th>User ID</th>
                <th>User Name</th>
                <th>Slip No</th>
                <th>Total Sub Amount</th>
                <th>OpenDate</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
         @foreach ($records as $record)
             <tr>
                 <td>{{ $record->user_id }}</td>
                 <td>{{ $record->user->name }}</td>
                 <td>{{ $record->slip_no }}</td>
                 <td>{{ $record->total_sub_amount }}</td>
                 <td>{{ $record->res_date }}</td>
                 <td>
                     <a href="{{ route('admin.EveningAllSlipShow', ['userId' => $record->user_id, 'slipNo' => $record->slip_no]) }}" class="btn btn-primary">View Details</a>
                 </td>
             </tr>
         @endforeach
     </tbody>

                </table>
            </div>
        @else
            <div class="alert alert-warning">
                No records found for the evening session.
            </div>
        @endif
   
 </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
{{-- <script>
    const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
      searchable: true,
      fixedHeight: true
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
