   <nav class="navbar navbar-expand-lg sticky-top bg-light border-bottom w-100">
       <div class="container-fluid">
           <a class="navbar-brand ms-2" href="#">
               <img src="<?= base_url('img/bootstrap-logo.svg') ?>" alt="logo" height="37">
               <span class="ms-2">Sticky Brand</span>
           </a>
           <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown">
               <span class="navbar-toggler-icon"></span>
           </button>
           <? if ($isLogin || $isLoggedIn) : ?>
               <div class="collapse navbar-collapse" id="navbarNavDropdown">
                   <ul class="navbar-nav ms-auto nav-justified">
                       <? if ($isAdmin) : ?>
                           <!-- admin menu here -->
                       <? endif ?>
                       <li class="nav-item"><a class="nav-link active" href="<?= site_url('home') ?>"><i class="fad fa-home fa-fw me-1"></i>Home</a></li>
                       <li class="nav-item dropdown mx-4">
                           <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"><b><?= $username ?></b></a>
                           <ul class="dropdown-menu">
                               <li><a class="dropdown-item" href="<?= site_url('logout') ?>" class="btn btn-danger"><i class="fad fa-sign-out fa-fw me-1"></i> Log Out</a></li>
                           </ul>
                       </li>
                   </ul>
               </div>
           <? endif ?>
       </div>
   </nav>


   <? if (isset($message)) : ?>
       <div class="row px-5 mx-5">
           <div class="col-md-12">
               <!-- alert messages -->
               <div class="alert alert-<?= isset($message['type']) ? $message['type'] : 'warning' ?> alert-dismissible alert-message pb-3 fade show" role="alert">
                   <?= isset($message['text']) ? $message['text'] : $message ?>
                   <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
               </div>
           </div>
       </div>
   <? endif ?>