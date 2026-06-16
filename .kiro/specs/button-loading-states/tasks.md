# Implementation Plan

- [x] 1. Create the loading.js module





  - [x] 1.1 Implement `startLoading` and `stopLoading` functions with WeakMap state storage


    - Create `public/assets/js/loading.js` with the core loading logic
    - Implement spinner HTML injection, disabled attribute management, and timeout safety net (10s)
    - Use a WeakMap to store original button state (innerHTML, disabled, timeoutId)
    - _Requirements: 1.1, 1.2, 1.3, 2.1, 2.2, 4.1, 4.3_
  - [x] 1.2 Add automatic form submit interception via `data-loading-text` attribute


    - Listen for `submit` events on forms containing buttons with `data-loading-text` or class `btn-loading`
    - Apply loading state to the submit button automatically
    - _Requirements: 2.3, 3.1, 3.3, 3.5, 3.6_
  - [ ]* 1.3 Write property test: Loading state transition
    - **Property 1: Loading state transition**
    - **Validates: Requirements 1.1, 2.1, 2.2, 2.3**
  - [ ]* 1.4 Write property test: Round-trip restoration
    - **Property 2: Round-trip restoration**
    - **Validates: Requirements 1.3, 4.1**
  - [ ]* 1.5 Write property test: Timeout safety net
    - **Property 3: Timeout safety net**
    - **Validates: Requirements 4.3**

- [x] 2. Integrate loading.js into layouts





  - [x] 2.1 Add loading.js script tag to the public layout (`app/views/layouts/layout.php`)


    - Add `<script src="<?= asset('js/loading.js') ?>"></script>` before app.js
    - _Requirements: 3.1, 3.2, 3.3, 3.4_
  - [x] 2.2 Add loading.js script tag to the admin layout (`app/views/layouts/admin_layout.php`)


    - Add `<script src="<?= asset('js/loading.js') ?>"></script>` before admin.js
    - _Requirements: 3.5, 3.6_

- [x] 3. Add loading attributes to public-facing buttons





  - [x] 3.1 Add `data-loading-text` to checkout "Confirmar pedido" button in `app/views/checkout/index.php`


    - Set `data-loading-text="Confirmando..."` on the submit button
    - _Requirements: 3.1_
  - [x] 3.2 Add `data-loading-text` to "Agregar al carrito" button in `app/views/product/show.php`

    - Set `data-loading-text="Agregando..."` on the add-to-cart submit button
    - _Requirements: 3.2_

  - [x] 3.3 Add `data-loading-text` to "Vaciar carrito" button in `app/views/cart/index.php`

    - Set `data-loading-text="Vaciando..."` on the empty cart submit button
    - _Requirements: 3.3_
  - [x] 3.4 Integrate loading into "Aplicar" coupon button in `public/assets/js/cart.js`

    - Call `startLoading`/`stopLoading` around the AJAX request for applying coupons
    - _Requirements: 3.4_

- [x] 4. Add loading attributes to admin buttons





  - [x] 4.1 Add `data-loading-text` to "Actualizar estado" button in `app/views/admin/ordenes/show.php`


    - Set `data-loading-text="Actualizando..."` on the order status submit button
    - _Requirements: 3.5_
  - [x] 4.2 Add `data-loading-text` to product form submit buttons in `app/views/admin/productos/form.php`


    - Set `data-loading-text="Guardando..."` on create/update buttons
    - _Requirements: 3.6_
  - [x] 4.3 Add `data-loading-text` to category form submit buttons in `app/views/admin/categorias/form.php`


    - Set `data-loading-text="Guardando..."` on create/update buttons
    - _Requirements: 3.6_

- [x] 5. Add loading CSS styles





  - [x] 5.1 Add `.btn-loading` styles to `public/assets/css/custom.css`


    - Add opacity reduction and cursor: not-allowed for buttons in loading state
    - Add spinner animation styles
    - _Requirements: 1.2_

- [x] 6. Final Checkpoint - Make sure all tests are passing





  - Ensure all tests pass, ask the user if questions arise.
