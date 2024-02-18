<?= $this->extend('layout/page') ?>

<?= $this->section('content') ?>

<!-- header -->
<?= $this->include('layout/header') ?>

<div class="container h-100 d-flex align-items-center justify-content-center">
    <main>
        <div class="row px-5">
            <div class="bg-light rounded-3 py-4">
                <div class="container-fluid py-2 px-5">

                    <div class="text-center mb-2">
                        <h3 class="display-6 fw-bold mb-4"><?= $username ?></h3>
                        <img src="<?= empty($profile_img) ? base_url("img/bootstrap-logo.svg") : $profile_img ?>" height="120">
                    </div>
                    <div class="col-md-12 py-4 fs-5">
                        <div class="row">
                            <div class="input-group py-1">
                                <span class="input-group-text col-md-2 fw-bold">User ID</span>
                                <?= form_input($userId) ?>
                            </div>
                            <div class="input-group py-1">
                                <span class="input-group-text col-md-2 fw-bold">Google ID</span>
                                <?= form_input($googleId) ?>
                            </div>
                            <div class="input-group py-1">
                                <span class="input-group-text col-md-2 fw-bold">First Name</span>
                                <?= form_input($firstName) ?>
                            </div>
                            <div class="input-group py-1">
                                <span class="input-group-text col-md-2 fw-bold">Last Name</span>
                                <?= form_input($lastName) ?>
                            </div>
                            <div class="input-group py-1">
                                <span class="input-group-text col-md-2 fw-bold">Email</span>
                                <?= form_input($email) ?>
                            </div>
                            <div class="input-group py-1">
                                <span class="input-group-text col-md-2 fw-bold">Phone</span>
                                <?= form_input($phone) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- footer -->
<?= $this->include('layout/footer') ?>

<?= $this->endSection() ?>