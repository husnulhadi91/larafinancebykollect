@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Customer Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
                <div class="card-header">Available Companies</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="">Name</td>
                                <td class="col-6">Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td>{{ $company->name }}</td>
                                <td>
                                        <div class="btn-group join_button" id="{{ $company->id }}_join_button">
                                            <a href="#" onclick="show_form({{ $company->id }})" class="btn btn-default btn-sm"><i class="fa fa-eye"></i> Join</a>
                                        </div>
                                        
                                        <div class="btn-group join_form col-12" id="{{ $company->id }}_join_form" style="display: none;">
                                            <form class="form-horizontal" action="{{ route('apply_finance') }}" method="POST">
                                            <div class="col-md-12">
                                                <input type="hidden" name="company_id" value="{{ $company->id }}">
                                                <input type="text" name="amount" id="amount" placeholder="amount" class="form-control">
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Submit
                                                </button>
                                            </div>
                                    </form>
                                        </div>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-header">Your Financing</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="">Name</td>
                                <td class="">Amount</td>
                                <td class="">Outstanding</td>
                                <td class="">Balance</td>
                                <td class="">invoice</td>
                                <td class="col-6">Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($out as $out)
                            <tr style="background-color: #ffcccb;">
                                <td>{{ $out->name }}</td>
                                <td>{{ $out->amount }}</td>
                                <td>
                                    @if($out->outstanding >0)
                                    {{ $out->outstanding }}
                                @endif
                            </td>
                            <td>{{ $out->balance }}</td>
                            <td><div class="btn-group" id="">
                                            <a href="{{ route('invoice_cust',$out->id) }}" class="btn btn-default btn-sm" target="_blank"><i class="fa fa-eye"></i> View</a>
                                        </div></td>
                                <td>
                                        
                                        
                                        <div class="btn-group join_form col-12" >
                                            <form class="form-horizontal" action="{{ route('pay_finance') }}" method="POST">
                                            <div class="col-md-12">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="finance_id" value="{{ $out->id }}">
                                                <input type="text" name="amount" id="amount" placeholder="amount" class="form-control">
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Pay
                                                </button>
                                            </div>
                                    </form>
                                        </div>

                                </td>
                            </tr>
                        @endforeach
                        @foreach ($okay as $ok)
                            <tr>
                                <td>{{ $ok->name }}</td>
                                <td>{{ $ok->amount }}</td>
                                <td>
                                    @if($ok->outstanding >0)
                                    {{ $ok->outstanding }}
                                @endif
                            </td>
                            <td>{{ $ok->balance }}</td>
                            <td><div class="btn-group" id="">
                                            <a href="{{ route('invoice_cust',$ok->id) }}" class="btn btn-default btn-sm" target="_blank"><i class="fa fa-eye"></i> View</a>
                                        </div></td>
                                <td>
                                        
                                        @if($ok->balance > 0)
                                        <div class="btn-group join_form col-12" >
                                            <form class="form-horizontal" action="{{ route('pay_finance') }}" method="POST">
                                            <div class="col-md-12">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="finance_id" value="{{ $ok->id }}">
                                                <input type="text" name="amount" id="amount" placeholder="amount" class="form-control">
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block">Pay
                                                </button>
                                            </div>

                                    </form>
                                        </div>
                                        @else
                                        <div class="btn-group join_form col-12" >
                                            
                                            Settled!
                                        </div>
                                            @endif

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="card-header">Your Settlement</div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <td class="">Name</td>
                                <td class="">Amount</td>
                                <td class="">Settled</td>
                                <td class="">Invoice</td>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($clears as $clear)
                            <tr>
                                <td>{{ $clear->name }}</td>
                                <td>{{ $clear->amount }}</td>
                                <td>{{ $clear->updated_at }}</td>
                                <td><div class="btn-group" id="">
                                            <a href="{{ route('invoice_cust',$clear->id) }}" class="btn btn-default btn-sm" target="_blank"><i class="fa fa-eye"></i> View</a>
                                        </div></td>
                                <td>
                                
                            </tr>
                        @endforeach
                        
                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function show_form(id) {
        // alert(id);
        document.getElementById(id+"_join_button").style.display='none';
        document.getElementById(id+"_join_form").style.display='';

    }
</script>
@endsection
