<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="">
    <head>
        <meta charset="utf-8" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

        <title>{{ config('app.name') }} || {{ __('LOGIN') }}</title>

        <meta name="description" content="Blue Orange Web Application | Staff-India" />

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap" rel="stylesheet" />

        <!-- Icons -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

        <!-- Core CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
        <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

        <!-- Vendors CSS -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
        <!-- Vendor -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />

        <!-- Page CSS -->
        <!-- Page -->
        <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

        <!-- Helpers -->
        <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
        <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
        <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
        <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
        <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
        <script src="{{ asset('assets/js/config.js') }}"></script>
    </head>

    <body>
        <!-- Content -->

        <div class="authentication-wrapper authentication-cover authentication-bg">
            <div class="authentication-inner row">
                <!-- /Left Text -->
                <div class="d-none d-lg-flex col-lg-7 p-0">
                    <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                        <img
                            src="{{ asset('assets/img/illustrations/auth-login-illustration-light.png') }}"
                            alt="auth-login-cover"
                            class="img-fluid my-5 auth-illustration"
                            data-app-light-img="illustrations/auth-login-illustration-light.png"
                            data-app-dark-img="illustrations/auth-login-illustration-dark.png"
                        />

                        <img
                            src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                            alt="auth-login-cover"
                            class="platform-bg"
                            data-app-light-img="illustrations/bg-shape-image-light.png"
                            data-app-dark-img="illustrations/bg-shape-image-dark.png"
                        />
                    </div>
                </div>
                <!-- /Left Text -->

                <!-- Login -->
                <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
                    <div class="w-px-400 mx-auto">
                        <!-- Logo -->
                        <div class="app-brand mb-4">
                            <a href="{{ route('login') }}" class="app-brand-link gap-2">
                                <img src="{{ asset('Logo/logo_black_01.png') }}" alt="{{ config('app.name') }}" width="30%">
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h3 class="mb-3 text-left"><b>Sign-In</b> to {{ config('app.name') }}</h3>

                        <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <sup class="text-danger">*</sup></label>
                                <input type="email" value="{{ old('email') }}" name="email" required autocomplete="off" autofocus tabindex="0" class="form-control @error('email') is-invalid @enderror" placeholder="{{ __('Login Email') }}" />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    {{-- @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">
                                            <small>Forgot Password?</small>
                                        </a>
                                    @endif --}}
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" required autocomplete="off"  tabindex="0" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="*****************" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-dark d-grid w-100">Sign in</button>
                        </form>

                        @if (app()->environment('local') && config('app.debug'))
                            @php
                                $demoPassword = \Database\Seeders\Demo\CyprusPortalDemoSeeder::DEMO_PASSWORD;
                            @endphp
                            <button
                                type="button"
                                class="btn btn-outline-secondary btn-sm w-100 mt-3"
                                data-bs-toggle="modal"
                                data-bs-target="#localDevCredentialsModal"
                            >
                                Show Credentials
                            </button>

                            <div
                                class="modal fade"
                                id="localDevCredentialsModal"
                                tabindex="-1"
                                aria-labelledby="localDevCredentialsModalLabel"
                                aria-hidden="true"
                            >
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="localDevCredentialsModalLabel">Seeded login credentials</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted small mb-3">
                                                Shown only when <code>APP_ENV</code> is <code>local</code> and <code>APP_DEBUG</code> is <code>true</code>.
                                                Run <code>php artisan db:seed</code> (or the Cyprus demo seeder) so these accounts exist.
                                            </p>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Role</th>
                                                            <th scope="col">Email</th>
                                                            <th scope="col">Password</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Developer</td>
                                                            <td><code class="user-select-all">developer@mail.com</code></td>
                                                            <td><code class="user-select-all">{{ $demoPassword }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Super Admin</td>
                                                            <td><code class="user-select-all">superadmin@mail.com</code></td>
                                                            <td><code class="user-select-all">{{ $demoPassword }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Institute representative</td>
                                                            <td><code class="user-select-all">institute.rep@ucy.ac.cy</code></td>
                                                            <td><code class="user-select-all">{{ $demoPassword }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Student</td>
                                                            <td><code class="user-select-all">demo.student@ucy.ac.cy</code></td>
                                                            <td><code class="user-select-all">{{ $demoPassword }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Landlord</td>
                                                            <td><code class="user-select-all">demo.landlord@stunest.test</code></td>
                                                            <td><code class="user-select-all">{{ $demoPassword }}</code></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Agent</td>
                                                            <td><code class="user-select-all">demo.agent@stunest.test</code></td>
                                                            <td><code class="user-select-all">{{ $demoPassword }}</code></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- <p class="text-center">
                            <span>New on our platform?</span>
                            <a href="{{ route('register') }}">
                                <span>Create an account</span>
                            </a>
                        </p> --}}
                    </div>
                </div>
                <!-- /Login -->
            </div>
        </div>

        <!-- / Content -->

        <!-- Core JS -->
        <!-- build:js assets/vendor/js/core.js -->

        <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
        <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

        <!-- endbuild -->

        <!-- Vendors JS -->
        <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>

        <!-- Main JS -->
        <script src="{{ asset('assets/js/main.js') }}"></script>

        <!-- Page JS -->
        <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

        @include('sweetalert::alert')
    </body>
</html>
