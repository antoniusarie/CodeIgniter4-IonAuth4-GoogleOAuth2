<!DOCTYPE html>
<html class="h-100" lang="en">

<head>

    <!-- meta/css/js -->
    <?= $this->include('layout/meta') ?>
    <?= $this->include('layout/css') ?>
    <?= $this->include('layout/js') ?>

</head>

<body class="d-flex flex-column h-100">    
    
    <!-- content -->
    <?= $this->renderSection('content') ?>
            
    <!-- plugins -->
    <?= $this->include('layout/plugins') ?>

</body>

</html>