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

    <li class="nav-heading">Library</li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'browse-books.php') ? '' : 'collapsed'; ?>" href="browse-books">
        <i class="bi bi-eyeglasses"></i>
        <span>Browse Books</span>
      </a>
    </li>

    <li class="nav-heading">My Activity</li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'my-borrowed-books.php') ? '' : 'collapsed'; ?>" href="my-borrowed-books">
        <i class="bi bi-book-half"></i>
        <span>My Borrowed Books</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'my-reservations.php') ? '' : 'collapsed'; ?>" href="my-reservations">
        <i class="bi bi-calendar-check"></i>
        <span>My Reservations</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo ($page == 'my-fines.php') ? '' : 'collapsed'; ?>" href="my-fines">
        <i class="bi bi-cash-coin"></i>
        <span>My Fines</span>
      </a>
    </li>



  </ul>

</aside><!-- End Sidebar-->

<main id="main" class="main">