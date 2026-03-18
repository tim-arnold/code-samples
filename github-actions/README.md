# GitHub Actions Workflows

CI/CD, backup, migration, and disaster recovery workflows from two different projects.

## Contents

### [`sftp-deploy/`](sftp-deploy/)

Build and deploy pipeline for a WordPress theme and custom plugin. Compiles SCSS, deploys via SFTP to staging
(automatically) and to production (with manual approval) via GitHub environment protection rules.

### [`complex-actions-suite/`](complex-actions-suite/)

A comprehensive set of workflows for a Cloudflare Workers + D1 application covering:

- **Deployment** — Production and staging pipelines with safety gates, confirmation prompts, and pre-deployment backups
- **Database migrations** — Apply, roll back, and inspect D1 schema changes with dry-run support
- **Scheduled backups** — Daily automated backups of Workers, D1 databases, and frontend builds to GitHub Releases
- **Disaster recovery** — Database restore from backup with dependency-ordered imports, plus production-to-staging sync

### [`github-deploy-cf-sanity.yml`](github-deploy-cf-sanity.yml)

Deploys a Next.js application to Cloudflare Workers on push to `main`, with preview deployments for other branches.