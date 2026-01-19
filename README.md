# Resolve API - Help Desk Support System

Resolve API is a high-performance, headless Help Desk backend built with Laravel 12. It provides a robust foundation for managing support tickets, categorizing issues, and facilitating secure communication between customers and support agents.



## Key Features

-   **Headless Architecture:** Designed specifically as an API-first application using Laravel Sanctum for secure, token-based authentication.
-   **Ticket Management:** Full CRUD operations for support tickets, including status tracking (Open, Pending, Resolved) and priority levels.
-   **Smart Categorization:** Relational database structure linking tickets to reusable categories with SEO-friendly slugs.
-   **Tiered Communication:** A nested comment system supporting "Internal Notes" visible only to staff/agents.
-   **Security First:** Strict resource protection using Laravel Policies—users can only access their own data.
-   **Performance Optimized:** Implements Eager Loading to solve N+1 query issues and API Resources for clean JSON transformations.

---

## Technical Stack

-   **Framework:** Laravel 12
-   **Authentication:** Laravel Sanctum (Breeze API Stack)
-   **Database:** SQLite (Default for portability)
-   **Testing:** Pest PHP (Feature & Unit testing)
-   **Architecture:** RESTful API with JSON Resources

---

## API Endpoints

### Authentication (Breeze)
- `POST /register` - Create a new account
- `POST /login` - Receive an authentication token

### Tickets
- `GET /api/tickets` - List authenticated user's tickets (Paginated)
- `POST /api/tickets` - Create a new support request
- `GET /api/tickets/{id}` - View specific ticket details (Policy protected)
- `PATCH /api/tickets/{id}` - Update ticket title or description

### Comments
- `POST /api/tickets/{id}/comments` - Add a comment to a ticket

---

## Testing

This project follows a Test-Driven Development (TDD) approach. The test suite covers authentication, resource authorization, and role-based access control.

To run the tests:
```bash
php artisan test
