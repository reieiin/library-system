<head>
  <?php $assetBase = '/librarySystem/public/assets'; ?>
  <?php
  $pageTitle = $pageTitle ?? 'Library System';
  $isUserSection = strpos($_SERVER['PHP_SELF'], '/public/user/') !== false;
  $isAdminSection = strpos($_SERVER['PHP_SELF'], '/public/admin/') !== false;
  $faviconFile = $_SERVER['DOCUMENT_ROOT'] . '/librarySystem/public/assets/img/favicon.png';
  $faviconVersion = file_exists($faviconFile) ? filemtime($faviconFile) : time();
  ?>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="<?php echo $assetBase; ?>/img/favicon.png?v=<?php echo $faviconVersion; ?>" rel="icon" type="image/png">
  <link href="<?php echo $assetBase; ?>/img/favicon.png?v=<?php echo $faviconVersion; ?>" rel="shortcut icon" type="image/png">
  <link href="<?php echo $assetBase; ?>/img/apple-touch-icon.png?v=<?php echo $faviconVersion; ?>" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo $assetBase; ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $assetBase; ?>/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $assetBase; ?>/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?php echo $assetBase; ?>/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?php echo $assetBase; ?>/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?php echo $assetBase; ?>/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?php echo $assetBase; ?>/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="<?php echo $assetBase; ?>/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
  <link href="/librarySystem/public/admin/assets/css/admin-components.css?v=<?php echo time(); ?>" rel="stylesheet">
  <?php if ($isUserSection) { ?>
  <link href="<?php echo $assetBase; ?>/css/user.css?v=<?php echo time(); ?>" rel="stylesheet">
  <?php } ?>

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>
<body class="<?php echo $isUserSection ? 'user-area' : ($isAdminSection ? 'admin-area' : ''); ?>">