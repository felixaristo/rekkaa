@include('auth.includes.header')
    <style>
        body {
            background-color: #fff;
            height: auto;
        }
        body::before, body::after {
            content: none;
        }
    </style>
    <div class="container">
        <!-- <div class="col-sm-6 container-p-y"> -->
        <div class="col-sm-12 container-p-y">
            @include('auth.register.register-template')
        </div>
    </div>
@include('auth.includes.footer')