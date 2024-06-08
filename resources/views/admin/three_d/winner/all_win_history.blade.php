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

    .custom-form-group {
        margin-bottom: 20px;
    }

    .custom-form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }

    .custom-form-group input,
    .custom-form-group select {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #e1e1e1;
        border-radius: 5px;
        font-size: 16px;
        color: #333;
    }

    .custom-form-group input:focus,
    .custom-form-group select:focus {
        border-color: #d33a9e;
        box-shadow: 0 0 5px rgba(211, 58, 158, 0.5);
    }

    .submit-btn {
        background-color: #d33a9e;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
    }

    .submit-btn:hover {
        background-color: #b8328b;
    }
</style>
@endsection
@section('content')
<div class="row mt-4">
    
    <div class="col-12">
        <div class="card mt-2">
         <div class="card-header">
          <h5>3D First Prize Winner History</h5>
         </div>
          <div class="table-responsive">
                <table class="table table-flush" id="twod-search">
                    <thead>
                <tr>
                    <th>Name</th>
                    <th>BetDigit</th>
                    <th>Sub Amount</th>
                    <th>Prize Amount</th>
                    <th>Match Start Date</th>
                    <th>Result Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->bet_digit }}</td>
                    <td>{{ $record->sub_amount }}</td>
                    <td>{{ $record->prize_amount }}</td>
                    <td>{{ $record->match_start_date }}</td>
                    <td>{{ $record->res_date }}</td>
                </tr>
                @endforeach
            </tbody>
                </table>
            </div>
            <div class="card">
                <div class="card-header">
                     <h3 class="text-center">Total Prize Amount: {{ $total_prize_amount }}</h3>
                </div>
            </div>
        </div>

        
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    @if(session('SuccessRequest'))
    Swal.fire({
      icon: 'success',
      title: 'Success! Three Digit Lottery Prize Number Created Successfully',
      text: '{{ session('
      SuccessRequest ') }}',
      timer: 3000,
      showConfirmButton: false
    });
    @endif
  });
</script>
<script>
    if (document.getElementById('twod-search')) {
        const dataTableSearch = new simpleDatatables.DataTable("#twod-search", {
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
<script>
    if (document.getElementById('search')) {
        const dataTableSearch = new simpleDatatables.DataTable("#search", {
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
@endsection