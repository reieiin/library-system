<?php $page = basename($_SERVER['PHP_SELF']); ?>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'index.php') ? '' : 'collapsed'; ?>" href="index">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->



    <li class="nav-heading">User Management</li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'users.php') ? '' : 'collapsed'; ?>" href="users">
        <i class="bi bi-person"></i>
        <span>Users</span>
      </a>
    </li>

    <li class="nav-heading">Library Management</li>

    <a class="nav-link <?php echo ($page == 'categories.php') ? '' : 'collapsed'; ?>" href="categories">
      <i class="bi bi-list-ul"></i>
      <span>Categories</span>
    </a>

    <a class="nav-link <?php echo ($page == 'authors.php') ? '' : 'collapsed'; ?>" href="authors">
      <i class="bi bi-person-vcard"></i>
      <span>Authors</span>
    </a>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'books.php') ? '' : 'collapsed'; ?>" href="books">
        <i class="bi bi-book"></i>
        <span>Books</span>
      </a>

    </li>

    <li class="nav-heading">Transactions</li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'borrow-records.php') ? '' : 'collapsed'; ?>" href="borrow-records">
        <i class="bi bi-receipt-cutoff"></i>
        <span>Borrow Records</span>
      </a>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'reservations.php') ? '' : 'collapsed'; ?>" href="reservations">
        <i class="bi bi-calendar-check"></i>
        <span>Reservations</span>
      </a>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'fines.php') ? '' : 'collapsed'; ?>" href="fines">
        <i class="bi bi-cash-coin"></i>
        <span>Fines</span>
      </a>
    </li>

    <li class="nav-heading">Reports</li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'activity-logs.php') ? '' : 'collapsed'; ?>" href="activity-logs">
        <i class="bi bi-list-check"></i>
        <span>Activity Logs</span>
      </a>
    </li>



  </ul>

</aside><!-- End Sidebar-->

<main id="main" class="main">