@extends('layouts.pda.app')

@section('content')
    <div class="pdabg">
        <img src="{{ asset('img/bg.jpg') }}" alt="">
        <a href="{{ url('logout?redirect=/pda/login') }}"><div class="quit"><img src="{{ asset('img/quit.png') }}" alt=""></div></a>
        <h5>BIS</h5>
        <div class="pdalogin">
            <div class="fenlei">
                <h3>{{ __("auth.AppointmentManagement") }}</h3>
                <div class="tubiao">
                    <div class="col-1">
                        <a href="{{ url('pda/appointment') }}"><img src="{{ asset('img/yy.png') }}" alt="">
                            <p>{{ __('auth.EndAppointment') }}</p></a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="fenlei">
                <h3>{{ __('auth.ReturnCabinetManagement') }}</h3>
                <div class="tubiao">
                    <div class="col-1">
                        <a href="{{ url('pda/unloading') }}"><img src="{{ asset('img/yy1.png') }}" alt="">
                            <p>{{ __('auth.Unloading') }}</p></a>
                    </div>
                    <div class="col-1">
                        <a href="{{ url('pda/cabinet') }}"><img src="{{ asset('img/yy2.png') }}" alt="">
                            <p>{{ __('auth.ReturningCounter') }}</p></a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

