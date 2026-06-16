# Implementation Plan

- [x] 1. Fix coupon AJAX application






  - [x] 1.1 Update CouponController AJAX response to include subtotal and totalItems

    - Add `subtotal` via `CartModel::getSubtotal()` and `totalItems` via `CartModel::getTotalItems()` to the success JSON response
    - Add `codigo` from the coupon data to the response
    - _Requirements: 1.1, 1.2, 1.5_
  - [x] 1.2 Fix cart.js AJAX URLs and coupon application UI


    - Add a `<meta name="base-url">` tag in the layout and read it in cart.js for URL construction
    - Replace hardcoded `/cupon/aplicar`, `/carrito/actualizar`, `/carrito/eliminar` with base-relative URLs
    - Update the coupon success handler to show the discount row, update subtotal/discount/total without `location.reload()`
    - Show coupon code and percentage in the discount label
    - Distinguish network errors (catch) from validation errors (422 response) with different messages
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  - [ ]* 1.3 Write property test: Coupon discount calculation consistency
    - **Property 3: Coupon discount calculation consistency**
    - **Validates: Requirements 1.2**
  - [ ]* 1.4 Write property test: Invalid coupons produce specific rejection messages
    - **Property 2: Invalid coupons produce specific rejection messages**
    - **Validates: Requirements 1.3**

- [x] 2. Create database tables and models for email system





  - [x] 2.1 Create SQL migration for `plantillas_correo` and `correos_log` tables


    - Create `database/migrations/create_email_tables.sql` with both table definitions and seed data for all template types
    - Include seed INSERT statements for: welcome, password_reset, order_confirmation, payment_confirmed, order_status, admin_new_user, admin_new_order, admin_payment_received, admin_new_review
    - _Requirements: 8.1, 9.1_
  - [x] 2.2 Implement EmailTemplateModel


    - Create `app/models/EmailTemplateModel.php` with `findBySlug()`, `findAll()`, `update()`, and `render()` methods
    - `render()` replaces `{variable_name}` markers with provided values
    - _Requirements: 8.2, 8.4_
  - [x] 2.3 Implement EmailLogModel


    - Create `app/models/EmailLogModel.php` with `create()`, `findAll()`, `countAll()` methods
    - _Requirements: 9.1, 9.2, 9.3_
  - [ ]* 2.4 Write property test: Template variable replacement completeness
    - **Property 4: Template variable replacement completeness**
    - **Validates: Requirements 8.4**
  - [ ]* 2.5 Write property test: Email log records match send outcomes
    - **Property 5: Email log records match send outcomes**
    - **Validates: Requirements 9.2, 9.3**

- [x] 3. Extend Mailer to log all sends






  - [x] 3.1 Modify Mailer::send() to record in correos_log

    - After each send attempt (success or failure), insert a record into `correos_log` via `EmailLogModel::create()`
    - On success: estado='enviado', error_mensaje=NULL
    - On failure: estado='error', error_mensaje=exception message
    - _Requirements: 9.2, 9.3_

- [x] 4. Create NotificationService






  - [x] 4.1 Implement NotificationService with all notification methods

    - Create `services/NotificationService.php` with static methods for each event type
    - Each method loads the template from DB via `EmailTemplateModel::render()`, then sends via `Mailer`
    - If template not found in DB, fall back to existing PHP template files
    - Methods: `welcomeEmail()`, `orderConfirmation()`, `paymentConfirmed()`, `orderStatusChanged()`, `adminNewUser()`, `adminNewOrder()`, `adminPaymentReceived()`, `adminNewReview()`
    - _Requirements: 2.1, 4.1, 5.1, 6.1, 7.1, 7.2, 7.3, 7.4_

- [x] 5. Integrate notifications into existing controllers







  - [x] 5.1 Add welcome email to AuthController::registro()

    - After successful registration and verification email, also call `NotificationService::welcomeEmail()` for the user and `NotificationService::adminNewUser()` for the admin

    - _Requirements: 2.1, 7.1_
  - [x] 5.2 Add admin notification to CheckoutController::confirm()

    - After order creation, call `NotificationService::adminNewOrder()`
    - _Requirements: 7.2_
  - [x] 5.3 Add payment confirmation notification to Admin\PaymentController


    - When admin approves payment, call `NotificationService::paymentConfirmed()` and `NotificationService::adminPaymentReceived()`
    - _Requirements: 5.1, 7.3_
  - [x] 5.4 Add order status change notification to Admin\OrderController::updateStatus()


    - Call `NotificationService::orderStatusChanged()` after status update
    - _Requirements: 6.1_

  - [x] 5.5 Add review notification to ReviewController::store()

    - After review submission, call `NotificationService::adminNewReview()`
    - _Requirements: 7.4_
  - [ ]* 5.6 Write property test: Order confirmation email contains all required fields
    - **Property 6: Order confirmation email contains all required fields**
    - **Validates: Requirements 4.1**

- [x] 6. Create email templates (PHP views)





  - [x] 6.1 Create welcome email template


    - Create `app/views/emails/welcome.php` with user name, email, login link, store contact info
    - _Requirements: 2.1_

  - [x] 6.2 Create payment confirmation email template

    - Create `app/views/emails/payment_confirmed.php` with order number, amount, date, status
    - _Requirements: 5.1_


  - [x] 6.3 Create order status change email template

    - Create `app/views/emails/order_status_change.php` with order number, new status, date, comments, order link

    - _Requirements: 6.1_

  - [x] 6.4 Create admin notification email templates

    - Create `app/views/emails/admin_notification.php` as a generic admin notification template
    - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [x] 7. Admin panel for email templates and log





  - [x] 7.1 Create Admin\EmailTemplateController


    - Create controller with `index()`, `edit()`, `update()` methods
    - _Requirements: 8.1, 8.2, 8.3_

  - [x] 7.2 Create admin email template views

    - Create `app/views/admin/plantillas/index.php` (list) and `app/views/admin/plantillas/edit.php` (edit form showing available variables)
    - _Requirements: 8.1, 8.2, 8.3_

  - [x] 7.3 Create Admin\EmailLogController

    - Create controller with `index()` method showing paginated log
    - _Requirements: 9.1_

  - [x] 7.4 Create admin email log view

    - Create `app/views/admin/correos/index.php` with table showing recipient, subject, date, status, error message
    - _Requirements: 9.1_


  - [x] 7.5 Register admin routes for templates and log

    - Add routes: GET/POST `/admin/plantillas-correo`, GET/POST `/admin/plantillas-correo/{id}/editar`, GET `/admin/correos-log`
    - Add navigation links in admin sidebar
    - _Requirements: 8.1, 9.1_

- [x] 8. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.
