<!DOCTYPE html>
<html
  lang="en"
  dir="ltr"
  data-layout="vertical">

<head>
  <!-- Required meta tags -->
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Favicon icon-->
  <link
    rel="shortcut icon"
    type="image/png"
    href="{{ asset('assets/images/logos/favicon.png') }}" />

  <!-- Core Css -->
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />

  <title>Spike Bootstrap Admin</title>

  
</head>

<body>
  <!-- Preloader -->
  <div class="preloader">
    <img
      src="{{ asset('assets/images/logos/loader.svg') }}"
      alt="loader"
      class="lds-ripple img-fluid" />
  </div>
  
  <div id="main-wrapper" class="p-0">
    <div
      class="auth-login position-relative overflow-hidden d-flex align-items-center justify-content-center px-3 px-xxl-0 rounded-3 h-100">
      <div class="auth-login-shape position-relative w-100" style="max-width: 900px;">
        <div
          class="auth-login-wrapper card mb-0 container position-relative z-1 h-100 max-h-600"
          data-simplebar>
          <div class="card-body px-4 py-3">
            <div
              class="row align-items-center justify-content-around pt-3 pb-2">
              <div class="col-lg-5 col-xl-4 d-none d-lg-block">
                <div class="text-center text-lg-start">
                  <img
                    src="{{ asset('assets/images/logos/smss-logo.png') }}"
                    alt=""
                    class="img-fluid"
                    style="max-width: 280px;" />
                </div>
              </div>
              <div class="col-lg-7 col-xl-6">
                <h2 class="mb-4 fs-7 fw-bolder">Welcome to Admin</h2>
                <p class="text-dark fs-4 mb-4">Your Admin Dashboard</p>
                <form>
                  <div class="mb-4">
                    <label
                      for="exampleInputEmail1"
                      class="form-label text-dark fw-bold">Username</label>
                    <input
                      type="email"
                      class="form-control py-3"
                      id="exampleInputEmail1"
                      aria-describedby="emailHelp" />
                  </div>
                  <div class="mb-4">
                    <label
                      for="exampleInputPassword1"
                      class="form-label text-dark fw-bold">Password</label>
                    <input
                      type="password"
                      class="form-control py-3"
                      id="exampleInputPassword1" />
                  </div>
                  <div
                    class="d-flex align-items-center justify-content-between mb-4 pb-1">
                    <div class="form-check">
                      <input
                        class="form-check-input primary"
                        type="checkbox"
                        value=""
                        id="flexCheckChecked"
                        checked />
                      <label
                        class="form-check-label text-dark fs-3"
                        for="flexCheckChecked">
                        Remember this Device
                      </label>
                    </div>
                    <a
                      class="text-primary fw-medium fs-3 fw-bold"
                      href="../dark/authentication-forgot-password.html">Forgot Password ?</a>
                  </div>
                  <a
                    href="../dark/index.html"
                    class="btn btn-primary w-100 mb-4 rounded-pill py-3">Sign In</a>
                  <div class="d-flex align-items-center">
                    <p class="fs-3 mb-0 fw-medium">New to Admin?</p>
                    <a
                      class="text-primary fw-bold ms-2 fs-3"
                      href="../dark/authentication-register.html">Create an account</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="dark-transparent sidebartoggler"></div>
      </div>
    </div>
  </div>
  
  <!-- Import Js Files -->
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/theme/app.dark.init.js') }}"></script>
  <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
  <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
  <script src="{{ asset('assets/js/theme/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/theme/feather.min.js') }}"></script>

  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>