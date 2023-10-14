# t_chat

`t_chat` is a Webman-based PHP chat project with a Vue frontend.

## Structure
- `app/`: backend controllers, models, and middleware
- `common/`: shared helpers and utilities
- `config/`: Webman and application configuration
- `front/`: Vue 2 frontend
- `public/`: static assets and uploaded media
- `runtime/`: runtime files and logs

## Stack
- PHP / Webman
- Vue 2
- Element UI
- Axios

## Notes
- Install backend dependencies with Composer.
- Install frontend dependencies in `front/` with npm or pnpm.
- Copy `.env.example` to `.env` and update local database and Redis settings.
- Runtime logs and `node_modules` are ignored from version control.
