<?php
session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $assetBase = '/librarySystem/public/assets';
  $faviconFile = $_SERVER['DOCUMENT_ROOT'] . '/librarySystem/public/assets/img/favicon.png';
  $faviconVersion = file_exists($faviconFile) ? filemtime($faviconFile) : time();
  ?>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register - NiceAdmin Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo $assetBase; ?>/img/favicon.png?v=<?php echo $faviconVersion; ?>" rel="icon" type="image/png">
  <link href="<?php echo $assetBase; ?>/img/favicon.png?v=<?php echo $faviconVersion; ?>" rel="shortcut icon" type="image/png">
  <link href="<?php echo $assetBase; ?>/img/apple-touch-icon.png?v=<?php echo $faviconVersion; ?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link
    href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">



              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form class="row g-3 needs-validation" action="../app/controllers/loginController.php" method="post"
                    enctype="multipart/form-data" autocomplete="off" novalidate>


                    <div class="col-12">
                      <div class="row">
                        <div class="col-12">
                          <label for="yourName" class="form-label">First Name</label>
                          <input type="text" name="first_name" class="form-control" id="yourName" required>
                          <div class="invalid-feedback">Please, enter your first name!</div>
                        </div>
                      </div>
                      <div class="col-12 mt-2">
                        <label for="yourName" class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" id="yourName" required>
                        <div class="invalid-feedback">Please, enter your last name!</div>
                      </div>

                      <div class="col-12 mt-2">
                        <label for="yourEmail" class="form-label">Your Email</label>
                        <input type="email" name="email" class="form-control" id="yourEmail" required>
                        <div class="invalid-feedback">Please enter a valid Email adddress!</div>
                      </div>

                      <div class="col-12 mt-2">
                        <label for="yourUsername" class="form-label">Username</label>
                        <div class="input-group has-validation">
                          <input type="text" name="username" class="form-control" id="yourUsername" required>
                          <div class="invalid-feedback">Please choose a username.</div>
                        </div>
                      </div>

                      <div class="col-12 mt-2">
                        <div class="row">
                          <div class="col-6">
                            <label for="yourPassword" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                            <div class="invalid-feedback">Please enter your password!</div>
                          </div>

                          <div class="col-6">
                            <label for="yourPassword" class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" id="yourPassword"
                              required>
                            <div class="invalid-feedback">Please confirm your password!</div>
                          </div>
                        </div>

                        <div class="col-12 mt-2">
                          <label for="yourPassword" class="form-label">Street</label>
                          <input type="text" name="street" class="form-control" id="yourPassword" required>
                          <div class="invalid-feedback">Please enter your street!</div>
                        </div>

                        <div class="col-12 mt-2">
                          <label for="yourPassword" class="form-label">Barangay</label>
                          <input type="text" name="barangay" class="form-control" id="yourPassword" required>
                          <div class="invalid-feedback">Please enter your barangay!</div>
                        </div>

                        <div class="col-12 mt-2">
                          <label for="yourPassword" class="form-label">City</label>
                          <input type="text" name="city" class="form-control" id="yourPassword" required>
                          <div class="invalid-feedback">Please enter your city!</div>
                        </div>


                        <div class="col-12 mt-2">
                          <div class="form-check">
                            <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms"
                              required>
                            <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms
                                and
                                conditions</a></label>
                            <div class="invalid-feedback">You must agree before submitting.</div>
                          </div>
                        </div>
                        <div class="col-12 mt-2">
                          <button class="btn btn-primary w-100" name="registerButton" type="submit">Create
                            Account</button>
                        </div>
                        <div class="col-12 mt-2">
                          <p class="small mb-0">Already have an account? <a href="login">Log in</a></p>
                        </div>
                  </form>

                </div>
              </div>



            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <?php
  if (isset($_SESSION['message']) && $_SESSION['code'] != '') {
    ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });

      Toast.fire({
        icon: "<?php echo $_SESSION['code']; ?>",
        title: "<?php echo $_SESSION['message']; ?>"
      });
    </script>
    <?php
    unset($_SESSION['message']);
    unset($_SESSION['code']);
  }
  ?>


</body>

</html>