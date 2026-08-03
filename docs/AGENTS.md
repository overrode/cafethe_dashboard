# CafThé development instructions

## Developer

The developer is learning and wants to write most of the code himself.

Do not generate complete classes containing every property and method unless
explicitly requested.

Prefer:

- Step-by-step explanations.
- Small, focused code snippets.
- Explaining which file to create or modify.
- Explaining how components connect.
- Letting the developer implement each step before continuing.

Do not modify files automatically unless explicitly requested.

## Project

CafThé is a PHP sales-management and ecommerce learning project.

Stack:

- PHP 8.3
- Object-oriented PHP
- MVC-style architecture
- MySQL 8
- Docker
- Apache
- phpMyAdmin
- HTML, CSS and JavaScript
- No PHP framework

Routing currently uses query parameters:

`/public/index.php?route=/products`

Do not introduce Apache rewrite rules or a framework unless explicitly requested.

## Architecture

- `../app/Core`: Router, Controller, Database and authentication infrastructure
- `../app/Controllers`: Request handling
- `../app/Models`: Database access and business logic
- `../app/Views`: PHP templates
- `public`: Application entry point and public assets

Main entities:

- Users
- Categories
- Products
- Clients
- Sales
- Sale items

## Working method

Before proposing code:

1. Inspect the existing implementation.
2. Explain what currently happens.
3. Identify the exact files involved.
4. Describe the next small implementation step.
5. Provide only the minimum code needed for that step.

Preserve the current project architecture and naming conventions.

## Development workflow

The developer is learning and wants to write most of the code personally.

The preferred workflow is:

1. ChatGPT is used as the teacher and architect.
2. The developer writes the implementation.
3. Codex reviews the actual repository, identifies problems and helps debug.
4. Codex should not take over complete feature development unless explicitly requested.

When helping:

* Inspect the existing code before making suggestions.
* Explain what the current code does.
* Reference exact file paths, classes and methods.
* Break features into small, sequential steps.
* Provide small, focused snippets only.
* Explain why each change is needed.
* Preserve the existing project architecture and naming conventions.
* Point out bugs, security issues and duplicated logic.
* Do not modify files automatically unless explicitly requested.
* Do not generate complete classes with all properties and methods unless explicitly requested.
* Do not implement an entire feature when the developer is practising it.
* Prefer guidance that lets the developer write the final code.

When reviewing code, use this format:

1. What is correct.
2. What is incorrect or risky.
3. Why it is a problem.
4. Which file and line or method is involved.
5. The smallest change needed to correct it.

The main objective is learning and understanding, not completing the project as quickly as possible.



