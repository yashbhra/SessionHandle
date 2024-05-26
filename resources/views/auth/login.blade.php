@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <span class="text-danger" id="error"></span>
                    <form method="POST" action="{{ url('check_signin') }}" id="form_login">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#form_login').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '{{ url("check_signin") }}',
                data : {
                    username: $('#email').val(),
                    password: $('#password').val(),
                    _token: $('input[name="_token"]').val()
                },
                success: function(d) {
                    if(d.status == 'error') {
                        $('#error').text(d.message);
                    } else if(d.status == 'success') {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Session Already Exists.... If You Continue Then It Will Be Logout From All Other Devices',
                            icon: 'warning',
                            confirmButtonText: 'Continue'
                        }).then((result) => {
                            if(result.value == true) {
                                console.log('Foce Login');
                                $.ajax({
                                    type: 'POST',
                                    url: '{{ url("force_login") }}',
                                    data : {
                                        username: $('#email').val(),
                                        password: $('#password').val(),
                                        _token: $('input[name="_token"]').val()
                                    },
                                    success: function(data) {
                                        if(data.status == 'redirect') {
                                            window.location.href = '{{ url("") }}'+data.message;
                                        }
                                    }
                                });
                            }
                        });
                    } else if(d.status == 'redirect') {
                        window.location.href = '{{ url("") }}'+d.message;
                    }
                },
                error: function(xhr) {
                    if(xhr.status == 500) {
                        $('#error').text('Please Reload The Page');
                    }
                }
            });
        });
    });
</script>
@endsection
