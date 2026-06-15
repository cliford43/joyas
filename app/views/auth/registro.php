<section class="py-5" style="min-height:80vh;display:flex;align-items:center;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="text-center mb-4">
          <h1 class="section-title">Crear cuenta</h1>
          <p class="section-subtitle">Únete a VILUNA y descubre nuestra colección exclusiva</p>
        </div>

        <?php if (!empty($errors)): ?>
          <div class="alert-viluna-error mb-3">
            <ul class="mb-0 ps-3">
              <?php foreach ($errors as $e): ?>
                <li><?= e($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('registro') ?>" novalidate>
          <input type="hidden" name="_csrf_token" value="<?= $csrfToken ?>">

          <div class="row g-3">
            <div class="col-6">
              <label class="form-label-viluna" for="nombre">Nombre</label>
              <input type="text" id="nombre" name="nombre" class="form-control form-control-viluna"
                     value="<?= e($old['nombre'] ?? '') ?>" required autocomplete="given-name">
            </div>
            <div class="col-6">
              <label class="form-label-viluna" for="apellido">Apellido</label>
              <input type="text" id="apellido" name="apellido" class="form-control form-control-viluna"
                     value="<?= e($old['apellido'] ?? '') ?>" required autocomplete="family-name">
            </div>
            <div class="col-12">
              <label class="form-label-viluna" for="correo">Correo electrónico</label>
              <input type="email" id="correo" name="correo" class="form-control form-control-viluna"
                     value="<?= e($old['correo'] ?? '') ?>" required autocomplete="email">
            </div>
            <div class="col-12">
              <label class="form-label-viluna" for="password">Contraseña</label>
              <input type="password" id="password" name="password" class="form-control form-control-viluna"
                     required autocomplete="new-password" minlength="8">
              <div class="form-text">Mínimo 8 caracteres.</div>
            </div>
            <div class="col-12">
              <label class="form-label-viluna" for="password2">Confirmar contraseña</label>
              <input type="password" id="password2" name="password2" class="form-control form-control-viluna"
                     required autocomplete="new-password">
            </div>
            <div class="col-12 mt-2">
              <button type="submit" class="btn btn-gold w-100">Crear cuenta</button>
            </div>
          </div>
        </form>

        <!-- Google OAuth -->
        <div class="text-center my-3" style="color:var(--color-gray);font-size:0.85rem;">— o —</div>
        <a href="<?= url('auth/google') ?>" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-2" style="border-radius:0;">
          <svg width="18" height="18" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/><path d="M3.964 10.706A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.706V4.962H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.038l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.962L3.964 7.294C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
          Continuar con Google
        </a>

        <p class="text-center mt-3" style="font-size:0.9rem;">
          ¿Ya tienes cuenta? <a href="<?= url('login') ?>" class="text-gold">Iniciar sesión</a>
        </p>
      </div>
    </div>
  </div>
</section>
