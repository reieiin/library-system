<?php
$authUser = $_SESSION['authUser'] ?? [];
$displayName = trim($authUser['fullname'] ?? '');
$displayName = $displayName !== '' ? $displayName : 'Guest';

$sharedAssetImgBase = '/librarySystem/public/assets/img';
$sharedAssetBase = '/librarySystem/public/assets';
$cacheBuster = time();
$profileImageSrc = $sharedAssetImgBase . '/profile1.jpg?v=' . $cacheBuster;
$logoImageSrc = $sharedAssetImgBase . '/logo.png?v=' . $cacheBuster;

$roleId = $_SESSION['role_id'] ?? null;
$profileRole = ((int)$roleId === 1) ? 'Administrator' : 'Library Member';
$logoutAction = '../../app/controllers/userController.php';
?>

  <!-- ======= Topbar ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center">
      <a href="index" class="logo d-flex align-items-center" style="width: auto;">
        <img src="<?php echo htmlspecialchars($logoImageSrc); ?>" alt="Library System Logo">
        <span class="d-none d-lg-block">Library System</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn" style="padding-left: 17px;"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="<?php echo htmlspecialchars($profileImageSrc); ?>" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($displayName); ?></span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo htmlspecialchars($displayName); ?></h6>
              <span><?php echo htmlspecialchars($profileRole); ?></span>
            </li>
            <li>
              <form action="<?php echo htmlspecialchars($logoutAction); ?>" method="POST" class="m-0">
                <button type="submit" name="logoutButton" class="dropdown-item d-flex align-items-center">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </button>
              </form>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->