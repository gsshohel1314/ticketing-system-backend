# Customer Support Ticketing System - Backend

This is the backend for a Customer Support Ticketing System built with **Laravel**. It provides a REST API for user authentication, ticket management, commenting, and real-time chat functionality using Pusher. The system supports two roles: Admin and Customer, with MySQL as the database.

## Features
- **Authentication**: Token-based (Laravel Sanctum) with registration, login, and logout.
- **Roles**: Admin (view all tickets) and Customer (view own tickets).
- **Tickets**: CRUD operations with fields: Subject, Description, Category, Priority, Attachment, Status (Open, In Progress, Resolved, Closed).
- **Comments**: Both Admins and Customers can comment on tickets.
- **Real-Time Chat**: Linked to tickets, powered by Pusher.

## Prerequisites
- PHP >= 8.2
- Composer
- MySQL
- Node.js & npm (for Pusher setup)
- Pusher account (for real-time chat)
- Git

## Installation
1. **Clone the Repository**:
    ```bash
    HTTPS: git clone https://github.com/gsshohel1314/ticketing-system-backend.git
    SSH: git clone git@github.com:gsshohel1314/ticketing-system-backend.git
    cd ticketing-system-backend
2. **Install Dependencies**:
    ```bash
    composer install
3. **Environment Setup**:
    ```bash
    cp .env.example .env
4. **Update .env**:
    ```bash
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=ticket_system
    DB_USERNAME=your_username
    DB_PASSWORD=your_password

    QUEUE_CONNECTION=sync
    BROADCAST_CONNECTION=pusher
    BROADCAST_DRIVER=pusher
    PUSHER_APP_ID=your_pusher_app_id
    PUSHER_APP_KEY=your_pusher_key
    PUSHER_APP_SECRET=your_pusher_secret
    PUSHER_APP_CLUSTER=your_pusher_cluster
5. **Generate Application Key**:
    ```bash
    php artisan key:generate
6. **Run Migrations**:
    ```bash
    php artisan migrate
7. **Seed Database (Optional)**:
    ```bash
    php artisan db:seed
8. **Start the Server**:
    ```bash
    php artisan serve
    ```

## Real-Time Chat
- **Technology**: Pusher is used for real-time messaging.
- **Channel**: Messages are broadcasted to ticket.{ticket_id} channels.
- **Setup**: Ensure Pusher credentials are set in .env.
- **Event**: MessageSent event triggers real-time updates.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
