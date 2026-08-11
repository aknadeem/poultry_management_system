# Laravel 13 Page Compatibility Design

## Goal

Restore all real user-facing pages and AJAX endpoints after the Laravel 8 to 13 upgrade, prevent invalid routes from being exposed, and verify the application with Pest against an isolated in-memory SQLite database.

## Scope

- Fix the `chickreport.purchases` duplicate route-name failure.
- Cover public pages, authenticated navigation pages, linked create/show/edit pages, parameterized reports, and AJAX/DataTables endpoints.
- Remove route registrations for controller actions that do not exist.
- Do not invent unused CRUD actions merely to satisfy generated resource routes.
- Use Pest HTTP tests rather than browser end-to-end tooling.
- Never connect tests to the development MySQL database.

## Route Repair

The parameterized chick purchase report endpoint and the parameterless purchase report page currently share the `chickreport.purchases` name. The data endpoint will receive a distinct name; the page route will retain `chickreport.purchases` because the top navigation already uses it.

All resource registrations will be compared with their controller methods. Each resource will use `only()` or `except()` so Laravel exposes only implemented actions. A route-integrity Pest test will reject duplicate names and missing controller methods.

## Application Page Coverage

Tests will use explicit route catalogs rather than blindly crawling all GET routes:

1. Public authentication pages.
2. Authenticated dashboard and navigation pages.
3. Safe linked create/show/edit pages with minimal fixtures.
4. Date-parameterized report endpoints.
5. AJAX/DataTables endpoints, asserted as JSON.

POST, PUT, PATCH, and DELETE routes will not be invoked by page smoke tests. Debug-only routes, framework endpoints, and intentionally non-page responses will be excluded explicitly.

## DataTables Compatibility

The application contains controllers that call Yajra DataTables, but the package is absent after the upgrade. A Laravel 13-compatible package release will be installed and configured using its documented facade/provider integration. AJAX tests will verify successful JSON responses and expected top-level DataTables fields.

## Test Database Isolation

Pest will use SQLite `:memory:` through both `phpunit.xml` and `.env.testing`. `DATABASE_URL` will be cleared and SQLite foreign keys enabled. `tests/TestCase.php` will fail before running a test unless the effective driver is SQLite and the database is `:memory:`.

`RefreshDatabase` will apply globally to Feature tests. Test fixtures will be minimal and deterministic. Existing migrations and seeders must run successfully under SQLite; known schema mistakes such as `nullabe()` will be corrected.

## Verification

Completion requires:

- no duplicate named routes;
- no routes targeting missing controller methods;
- all intended page responses successful;
- all intended AJAX responses successful JSON;
- migrations and seeders successful on SQLite;
- all Pest tests passing;
- frontend development and production builds passing;
- no new lint or diff-check errors.

## Non-Goals

- Implementing unused controller actions.
- Browser automation or JavaScript interaction tests.
- Testing against the development MySQL database.
- Broad architectural migration to Laravel 13's newest application skeleton.
