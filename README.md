# Cafeteria Management System

A modern cafeteria management system built with Vue.js and PHP, using Nx as a monorepo manager.

## Project Structure

This project is organized as a monorepo using Nx, containing two main applications:

- `apps/client`: Vue.js frontend application
- `apps/server`: PHP backend server

## Getting Started

### Prerequisites

- Node.js
- PHP
- npm or yarn

### Installation

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd cafeteria-vue-php
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

### Development

1. Start the PHP server:
   ```bash
   npx nx serve cafeteria-server
   ```

2. In a separate terminal, start the Vue client:
   ```bash
   npx nx serve cafeteria-client
   ```

The applications will be available at:
- Frontend: http://localhost:5173
- Backend: http://localhost:8000

## License

MIT

