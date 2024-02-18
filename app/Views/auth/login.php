<?= $this->extend('layout/page') ?>

<?= $this->section('content') ?>

<div class="container h-100 d-flex align-items-center justify-content-center">
  <main class="form-signin w-50">

    <div class="row">
      <div class="py-5 my-5">

        <?= form_open('auth/login'); ?>

        <div class="text-center">
          <a href="<?= base_url() ?>"><img class="mb-4" src="<?= base_url('img/bootstrap-logo.svg') ?>" alt="" width="72" height="57"></a>
          <h1 class="h3 mb-5 fw-normal">Please Sign In</h1>
        </div>

        <? if (isset($message)) : ?>
          <!-- alert message -->
          <div class="alert alert-<?= isset($message['type']) ? $message['type'] : 'warning' ?> alert-dismissible alert-message pb-0 fade show" role="alert">
            <?= isset($message['text']) ? $message['text'] : $message ?>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
          </div>
        <? endif ?>

        <div class="form-floating my-3">
          <?= form_input($identity) ?>
          <label for="identity">Email address</label>
        </div>
        
        <div class="form-floating">
          <?= form_input($password) ?>
          <label for="password">Password</label>
        </div>

        <div class="form-check mb-4 py-2">
          <input class="form-check-input" name="remember" type="checkbox" value="1" id="remember">
          <label class="form-check-label" for="remember">Remember Me</label>
        </div>

        <button class="w-100 btn btn-lg btn-primary" type="submit">Sign In</button>
        <?= form_close(); ?>

        <p class="py-1 mt-2 text-center">or</p>
        <a href="<?= $googleBtn ?>" class="w-100 btn btn-lg btn-danger">Google</a>

        <footer class="text-center">
          <p class="mt-5 mb-3 text-muted">© <?= date("Y") ?></p>
        </footer>


      </div>
    </div>

  </main>
</div>

<?= $this->endSection() ?>