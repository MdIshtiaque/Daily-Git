
# GitHub Activity Tracker Web App - Roadmap

## Overview
This web app will display daily GitHub activities, including commits, pushes, and code changes. It will be built using **Laravel** (Backend), **Vue.js with Inertia.js** (Frontend), and **MySQL** (Database). The app will integrate with GitHub's API to fetch the relevant data.

## Features
- View daily GitHub commits and pushes
- Display code changes (diffs between commits)
- Filter by repository and date range
- Responsive UI

## Tech Stack
- **Backend**: Laravel
- **Frontend**: Vue.js with Inertia.js
- **Database**: MySQL
- **API**: GitHub REST API
- **Authentication**: GitHub OAuth (Optional)

## Phase 1: Planning & Initial Setup
1. Define the project scope and features.
2. Set up a new Laravel project with Vue Inertia.
3. Set up MySQL database.
4. Configure environment variables (GitHub API credentials).

## Phase 2: Backend Development (Laravel)
1. **API Integration**:
    - Set up routes and controllers for handling GitHub API requests.
    - Use GitHub Personal Access Token for authentication.
    - Fetch GitHub user activities (commits, pushes) using Events and Commits API.
2. **Database**:
    - Create a migration to store GitHub activity logs (optional).
    - Implement models and relationships if required.
3. **Data Processing**:
    - Process and parse API responses to extract necessary commit and push data.

## Phase 3: Frontend Development (Vue Inertia)
1. Set up Vue components using Inertia.js.
2. Create views for displaying GitHub activities (commits, pushes).
3. Implement a code diff view to display changes between commits.
4. Add filters (repository, date range) to the frontend.
5. Style the app using TailwindCSS or a similar framework.

## Phase 4: Security & Authentication
1. Implement GitHub OAuth to allow users to log in and view their activities.
2. Secure API routes with authentication and rate-limiting.
3. Ensure all communication uses HTTPS.

## Phase 5: Deployment & Hosting
1. Set up deployment pipelines for Laravel (Heroku, DigitalOcean, or similar).
2. Configure database for production (MySQL).
3. Set up domain and SSL certificate (optional).

## Phase 6: Optimization & Enhancements
1. Cache API responses to minimize GitHub API calls.
2. Implement error handling for failed API calls or rate limit hits.
3. Add daily/weekly notifications for users to track their commits (optional).

## Phase 7: Testing & Launch
1. Test API integrations and frontend components.
2. Perform cross-browser and device testing.
3. Launch the app on the chosen platform.
