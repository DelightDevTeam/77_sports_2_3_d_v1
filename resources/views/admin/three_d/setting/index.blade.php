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
      <h5 class="mb-0">3D Setting Dashboards</h5>
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
    <table class="table table-flush" id="permission-search">
     <thead class="thead-light">
      <tr>
       <th>StartDate</th>
       <th>OpenDate</th>
       <th>OpenTime</th>
       <th>ResNumber</th>
       {{-- <th>3DMatch</th> --}}
       <th>CreatePrizeNumber</th>
       <th>Status</th>
       <th>3DMatch Open/Close</th>
      </tr>
     </thead>
     <tbody>
      @foreach($results as $key => $result)
      <tr>
       {{-- <td class="text-sm font-weight-normal">{{ ++$key }}</td> --}}
       <td>{{ $result->match_start_date }}</td>
       <td class="text-sm font-weight-normal">{{ $result->result_date }}</td>
       <td class="text-sm font-weight-normal">{{ $result->result_time }}</td>
        <td class="text-sm font-weight-normal">
            @if(isset($result->result_number))
            {{ $result->result_number }}
            @else
            <p>Pending</p>
            @endif
        </td>
       {{-- <td id="status-{{ $result->id }}">{{ $result->status }}</td> --}}
       <td>
       @if($result->status == 'open')

            <form method="POST" action="{{ route('admin.UpdateResult_number', ['id' => $result->id]) }}">
                @csrf
                @method('PATCH')
                <input type="text" name="result_number" placeholder="Enter result number" required class="form-control">
                <button type="submit" class="btn btn-primary">ထွက်ဂဏန်းထဲ့ရန်</button>
            </form>
        @endif
        </td>
        {{-- <td>
            <button class="toggle-status"
                    data-id="{{ $result->id }}"
                    data-status="{{ $result->status == 'open' ? 'closed' : 'open' }}">
                Open/Close
            </button>
        </td> --}}
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
  <div class="card mt-4">
    <div class="card-header">
        <p class="text-center">
            ပတ်လယ်ထွက်ဂဏန်း
        </p>
        <p class="text-center">
            3D First Prize Number : 
            @if(isset($lasted_prizes->result_number))
            {{ $lasted_prizes->result_number }}
            @else
            <p class="text-center">
                Pending
            </p>
            @endif
        </p>
    </div>
    <div class="card-body">
        
        @php
            use App\Helpers\PermutationDigit;

            // Create an instance of the PermutationDigit class
            $permutationDigit = new PermutationDigit();
            if($lasted_prizes) {
            $prize_num = $lasted_prizes->result_number;
            $permutations = $permutationDigit->PerDigit($prize_num, $prize_num);
            } else {
                $lasted_prizes = null;
            }
            
            @endphp
            <form method="POST" action="{{ route('admin.storePermutations') }}">
            @csrf
            <table class="table table-flush" id="twod-search">
                <thead class="thead-light">
                    <th>ပတ်လယ်ထွက်ဂဏန်းများ</th>
                </thead>
                <tbody>
                    @if($lasted_prizes)
                    <tr>
                        <td colspan="4">
                            @foreach($permutations as $permutation)
                                <span>{{ $permutation }} | </span>
                                <input type="hidden" name="permutations[]" value="{{ $permutation }}">
                            @endforeach
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td colspan="4" class="text-center">No Data Found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">ပတ်လယ်ထွက်ဂဏန်းများသိမ်းပါ</button>
            </form>

            <div class="card-header pb-0">
                <div>
                    <h5 class="mb-0">3D ပတ်လယ်ထွက်ဂဏန်းများ</h5>
                </div>
                <div class="d-lg-flex mt-2">
                    <div class="ms-auto my-auto mt-lg-0">
                        <div class="ms-auto my-auto">
                        <form action="{{ route('admin.PermutationReset') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" type="submit" name="button">ပတ်လယ်ထွက်ဂဏန်းများအကုန်ဖျက်ရန်</button>
                        </form>
                        </div>
                    </div>
                </div>
            </div>

             <div class="table-responsive mt-4">
                <table class="table table-flush" id="twod-search">
                    <thead class="thead-light">
                        <th>#</th>
                        {{-- <th>Lottery ID</th> --}}
                        <th>ပတ်လယ်ထွက်ဂဏန်း</th>
                        <th>Date</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @if($permutation_digits)
                        @foreach($permutation_digits as $permutation_digit)
                        <tr>
                            <td>{{ $permutation_digit->id }}</td>
                            <td>{{ $permutation_digit->digit }}</td>
                            <td>{{ $permutation_digit->created_at }}</td>
                            {{-- delete form --}}
                            <td>
                                <form action="{{ route('admin.deletePermutation', $permutation_digit->id) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="transparent-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                        <i class="fas fa-trash text-danger"></i>
                                    </button>
                                </form>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">No Data Found</td>
                        </tr>
                        @endif


                    </tbody>
                </table>
            </div>
    </div>
  </div>
 </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>3D သွပ်ဂဏန်းထဲ့ရန်</h5>
            </div>
            <form action="{{ route('admin.PrizeStore') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="input-group input-group-outline is-valid my-3">
                        <label class="form-label">အထက်ဂဏန်း</label>
                        <input type="text" class="form-control" name="prize_one">
                    </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="input-group input-group-outline is-valid my-3">
                        <label class="form-label">အောက်ဂဏန်း</label>
                        <input type="text" class="form-control" name="prize_two">
                    </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="input-group input-group-outline is-valid my-3">
                        <button type="submit" class="btn btn-primary">သွပ်ဂဏန်းသိမ်းပါ</button>
                    </div>
                    </div>
                </div>
                
            </form>
        </div>
        <div class="card mt-3">
            <!-- Card header -->
            <div class="card-header pb-0">
                <div>
                    <h5 class="mb-0">3D သွပ်ဂဏန်းများ</h5>
                </div>
                <div class="d-lg-flex mt-2">
                    <div class="ms-auto my-auto mt-lg-0">
                        <div class="ms-auto my-auto">
                            {{-- <a href="#" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Create New</a> --}}
                            <button class="btn btn-outline-primary btn-sm export mb-0 mt-sm-0 mt-1" data-type="csv" type="button" name="button">Export</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-flush" id="twod-search">
                    <thead class="thead-light">
                        <th>#</th>
                        {{-- <th>Lottery ID</th> --}}
                        <th>သွပ်အထက်ဂဏန်း</th>
                        <th>သွပ်အောက်ဂဏန်း</th>
                        <th>Date</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @if($three_digits_prize)
                        <tr>
                            <td>{{ $three_digits_prize->id }}</td>
                            <td>{{ $three_digits_prize->prize_one }}</td>
                            <td>{{ $three_digits_prize->prize_two }}</td>
                            <td>{{ $three_digits_prize->created_at }}</td>
                            <td>
                             <form class="d-inline" action="{{ route('admin.DeletePrize', $three_digits_prize->id) }}" method="POST">
                             @csrf
                             @method('DELETE')
                             <button type="submit" class="transparent-btn" data-bs-toggle="tooltip" data-bs-original-title="Delete Role">
                              <i class="material-icons text-secondary position-relative text-lg">delete</i>
                             </button>
                            </form>
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="4" class="text-center">No Data Found</td>
                        </tr>
                        @endif


                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-6">
         
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
            url: '/admin/lottery-results/' + resultId + '/status', // Your route
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