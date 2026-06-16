# Implementation Plan

- [x] 1. Create database migration and ReviewModel






  - [x] 1.1 Create SQL migration for `resenas` table

    - Create `database/migrations/create_resenas_table.sql` with the table definition, indexes, and constraints from the design
    - Execute migration against the database
    - _Requirements: 1.5, 1.6_
  - [x] 1.2 Implement ReviewModel with core CRUD methods

    - Create `app/models/ReviewModel.php` with: `create()`, `findById()`, `userHasReview()`, `countRecentByUser()`, `getApprovedByProduct()`, `getProductStats()`
    - Implement input validation method `validate()` for rating (1-5) and comment (10-1000 chars)
    - Implement sanitization in `create()` using `strip_tags()` and `htmlspecialchars()`
    - _Requirements: 1.1, 1.2, 1.3, 1.5, 1.6, 6.1, 6.2, 6.3_
  - [ ]* 1.3 Write property test: Review creation defaults to pending state
    - **Property 1: Review creation defaults to pending state**
    - **Validates: Requirements 1.1**
  - [ ]* 1.4 Write property test: Input validation rejects invalid reviews
    - **Property 2: Input validation rejects invalid reviews**
    - **Validates: Requirements 1.2, 1.3**
  - [ ]* 1.5 Write property test: Sanitization removes HTML and script content
    - **Property 8: Sanitization removes HTML and script content**
    - **Validates: Requirements 6.1**

- [x] 2. Implement admin review management






  - [x] 2.1 Implement admin ReviewModel methods

    - Add `findFiltered()`, `countFiltered()`, `approve()`, `reject()`, `delete()`, `updateComment()` methods to ReviewModel
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_
  - [x] 2.2 Create Admin\ReviewController


    - Create `app/controllers/Admin/ReviewController.php` with `index()`, `approve()`, `reject()`, `edit()`, `update()`, `delete()` actions
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 2.3 Create admin reviews list view

    - Create `app/views/admin/resenas/index.php` with filters (product, user, status, date, rating) and action buttons
    - _Requirements: 2.1_
  - [x] 2.4 Create admin review edit view


    - Create `app/views/admin/resenas/edit.php` with form to edit comment text only
    - _Requirements: 2.5_
  - [x] 2.5 Register admin routes


    - Add routes for `/admin/resenas`, `/admin/resenas/{id}/aprobar`, `/admin/resenas/{id}/rechazar`, `/admin/resenas/{id}/editar`, `/admin/resenas/{id}/eliminar` in `routes/web.php`
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_
  - [ ]* 2.6 Write property test: State transitions preserve review data
    - **Property 5: State transitions preserve review data**
    - **Validates: Requirements 2.2, 2.3**
  - [ ]* 2.7 Write property test: Edit preserves immutable fields
    - **Property 6: Edit preserves immutable fields**
    - **Validates: Requirements 2.5**

- [x] 3. Implement public review submission





  - [x] 3.1 Create ReviewController for public submission


    - Create `app/controllers/ReviewController.php` with `store()` method
    - Validate auth, duplicate check, rate limit (3/24h), input validation, sanitization
    - Store review with estado=pendiente, record IP
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 6.1, 6.2, 6.3_

  - [x] 3.2 Register public review route

    - Add POST route `/producto/{slug}/resena` in `routes/web.php` with auth and csrf middleware
    - _Requirements: 1.1, 1.4_
  - [ ]* 3.3 Write property test: Duplicate review rejection
    - **Property 4: Duplicate review rejection**
    - **Validates: Requirements 1.6**

- [x] 4. Integrate reviews into product page





  - [x] 4.1 Update ProductController to pass review data


    - Modify `show()` in `app/controllers/ProductController.php` to load review stats and approved reviews list
    - Pass `$reviewStats`, `$resenas`, `$userHasReview`, `$canReview` to the view
    - _Requirements: 3.1, 3.2, 3.3, 3.4_
  - [x] 4.2 Add review display section to product view


    - Update `app/views/product/show.php` to show: average rating with stars, total ratings count, list of approved reviews (name, stars, date, comment)
    - Show "No hay reseñas aún" when empty
    - _Requirements: 3.1, 3.2, 3.3, 3.4_
  - [x] 4.3 Add review submission form to product view


    - Add star rating selector (interactive JS) and textarea to `app/views/product/show.php`
    - Show form only for authenticated users who haven't reviewed yet
    - Show confirmation message after submission
    - _Requirements: 7.1, 7.2, 7.3_
  - [x] 4.4 Create star rating JavaScript component


    - Add star rating interaction logic in `public/assets/js/app.js` or a new `reviews.js`
    - Highlight stars on hover/click, set hidden input value
    - _Requirements: 7.2_
  - [ ]* 4.5 Write property test: Aggregations use only approved reviews
    - **Property 7: Aggregations use only approved reviews**
    - **Validates: Requirements 3.1, 3.3**

- [x] 5. Integrate with home sections







  - [x] 5.1 Add new home section types for reviews
    - Add `top_rated`, `most_reviewed`, `testimonials` to `ConfigController::HOME_SECTION_TYPES`
    - Implement `getTopRatedProducts()`, `getMostReviewedProducts()`, `getTestimonials()` in ReviewModel
    - Update `ProductModel::getBySection()` to handle new types

    - _Requirements: 4.1, 4.2, 4.3, 4.4, 5.1_
  - [x] 5.2 Create testimonials partial view

    - Create a partial for rendering testimonials (comment, user name, stars, product link)
    - Update home view to render testimonials section type
    - _Requirements: 5.1, 5.2, 5.3_
  - [ ]* 5.3 Write property test: Testimonials filter by rating threshold
    - **Property 9: Testimonials filter by rating threshold**
    - **Validates: Requirements 5.1**

- [x] 6. Add admin navigation link







  - [x] 6.1 Add "Reseñas" link to admin sidebar/navigation





    - Update admin layout to include navigation to `/admin/resenas`
    - _Requirements: 2.1_

- [x] 7. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.
