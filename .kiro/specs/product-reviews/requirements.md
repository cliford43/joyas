# Requirements Document

## Introduction

Este documento especifica los requisitos para implementar un sistema de comentarios y calificaciones por estrellas para los productos de la tienda VILUNA. El sistema permite a usuarios autenticados dejar opiniones con valoración obligatoria, requiere aprobación administrativa antes de publicación, y se integra con las secciones configurables de la página principal.

## Glossary

- **Sistema_Viluna**: La aplicación web de joyería Viluna, incluyendo frontend público y panel administrativo.
- **Comentario**: Una opinión textual publicada por un usuario autenticado sobre un producto específico.
- **Calificación**: Una valoración numérica de 1 a 5 estrellas asignada por un usuario a un producto.
- **Reseña**: El conjunto de un comentario más su calificación asociada sobre un producto.
- **Estado_Reseña**: El estado de moderación de una reseña: pendiente, aprobado o rechazado.
- **Usuario_Autenticado**: Un usuario que ha iniciado sesión en el sistema con credenciales válidas.
- **Administrador**: Un usuario con rol "admin" que gestiona el panel administrativo.
- **Calificación_Promedio**: El promedio aritmético de todas las calificaciones aprobadas de un producto.

## Requirements

### Requirement 1

**User Story:** Como usuario autenticado, quiero publicar un comentario con calificación sobre un producto, para compartir mi experiencia con otros compradores.

#### Acceptance Criteria

1. WHEN a Usuario_Autenticado submits a review form on a product page THEN the Sistema_Viluna SHALL create a new Reseña with Estado_Reseña set to "pendiente".
2. WHEN a Usuario_Autenticado submits a review THEN the Sistema_Viluna SHALL require a Calificación value between 1 and 5 inclusive.
3. WHEN a Usuario_Autenticado submits a review THEN the Sistema_Viluna SHALL require a non-empty Comentario text with a minimum of 10 characters and a maximum of 1000 characters.
4. WHEN a visitor (non-authenticated user) attempts to submit a review THEN the Sistema_Viluna SHALL redirect that visitor to the login page.
5. WHEN a Reseña is created THEN the Sistema_Viluna SHALL record the user ID, product ID, comment text, rating, IP address, and creation timestamp.
6. WHEN a Usuario_Autenticado attempts to submit a second review for the same product THEN the Sistema_Viluna SHALL reject the submission and display an informational message.

### Requirement 2

**User Story:** Como administrador, quiero gestionar los comentarios y valoraciones, para mantener la calidad del contenido visible en la tienda.

#### Acceptance Criteria

1. WHEN an Administrador accesses the reviews admin section THEN the Sistema_Viluna SHALL display all Reseñas with filters by product, user, Estado_Reseña, date range, and Calificación.
2. WHEN an Administrador approves a Reseña THEN the Sistema_Viluna SHALL change its Estado_Reseña to "aprobado" and make it publicly visible.
3. WHEN an Administrador rejects a Reseña THEN the Sistema_Viluna SHALL change its Estado_Reseña to "rechazado" and keep it hidden from public view.
4. WHEN an Administrador deletes a Reseña THEN the Sistema_Viluna SHALL permanently remove it from the database.
5. WHEN an Administrador edits a Reseña THEN the Sistema_Viluna SHALL update the comment text while preserving the original author, date, and Calificación.

### Requirement 3

**User Story:** Como visitante, quiero ver las valoraciones y comentarios aprobados en la página del producto, para tomar decisiones de compra informadas.

#### Acceptance Criteria

1. WHEN a visitor views a product page THEN the Sistema_Viluna SHALL display the Calificación_Promedio, total number of approved ratings, and total number of approved comments for that product.
2. WHEN a visitor views a product page THEN the Sistema_Viluna SHALL display the list of approved Reseñas showing comment text, user first name, Calificación in stars, and formatted date.
3. WHILE a Reseña has Estado_Reseña "pendiente" or "rechazado" THEN the Sistema_Viluna SHALL exclude it from all public-facing displays and calculations.
4. WHEN no approved Reseñas exist for a product THEN the Sistema_Viluna SHALL display a message indicating no reviews are available yet.

### Requirement 4

**User Story:** Como administrador, quiero que las secciones de la página principal puedan mostrar productos basados en valoraciones, para destacar productos bien calificados.

#### Acceptance Criteria

1. WHEN an Administrador configures a home section THEN the Sistema_Viluna SHALL offer "Productos mejor valorados" as a content type option.
2. WHEN an Administrador configures a home section THEN the Sistema_Viluna SHALL offer "Productos con más comentarios" as a content type option.
3. WHEN the home section type is "Productos mejor valorados" THEN the Sistema_Viluna SHALL display products ordered by their Calificación_Promedio descending, considering only approved reviews.
4. WHEN the home section type is "Productos con más comentarios" THEN the Sistema_Viluna SHALL display products ordered by their count of approved Reseñas descending.

### Requirement 5

**User Story:** Como visitante, quiero ver una sección de testimonios en la página principal, para generar confianza en la tienda.

#### Acceptance Criteria

1. WHEN an Administrador configures a home section as "Testimonios destacados" THEN the Sistema_Viluna SHALL display approved Reseñas with Calificación of 4 or 5 stars.
2. WHEN displaying testimonials THEN the Sistema_Viluna SHALL show the comment text, user first name, Calificación in stars, and product name with link.
3. WHEN fewer than 3 approved testimonials exist THEN the Sistema_Viluna SHALL hide the testimonials section.

### Requirement 6

**User Story:** Como administrador, quiero que el sistema prevenga spam y contenido malicioso, para proteger la integridad de la tienda.

#### Acceptance Criteria

1. WHEN a Reseña is submitted THEN the Sistema_Viluna SHALL sanitize the comment text to remove HTML tags and script content.
2. WHEN a Usuario_Autenticado submits a Reseña THEN the Sistema_Viluna SHALL record the client IP address for audit purposes.
3. WHEN a Usuario_Autenticado attempts to submit more than 3 reviews across all products within a 24-hour period THEN the Sistema_Viluna SHALL reject the submission with a rate-limit message.

### Requirement 7

**User Story:** Como usuario autenticado, quiero que el formulario de reseña sea intuitivo y accesible, para poder dejar mi opinión fácilmente.

#### Acceptance Criteria

1. WHEN a product page loads for a Usuario_Autenticado THEN the Sistema_Viluna SHALL display the review form below the product details section.
2. WHEN a Usuario_Autenticado interacts with the star rating THEN the Sistema_Viluna SHALL provide visual feedback highlighting stars from 1 up to the selected value.
3. WHEN a review is successfully submitted THEN the Sistema_Viluna SHALL display a confirmation message indicating the review is pending approval.
