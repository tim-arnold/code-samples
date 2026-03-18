# CI/CD Workflows

## build-and-deploy.yml

Single workflow that handles building and deploying the WordPress theme and custom plugin.

### Jobs

**Build** — Runs on all pushes to `main`/`staging` and PRs to `main`. Compiles SCSS and verifies the output CSS exists.

**Deploy to Staging** — Runs after a successful build on the `staging` branch. Deploys the Stout theme and `wfund-global-ctas` plugin to the staging server via SFTP.

**Deploy to Production** — Runs after a successful build on the `main` branch. Requires manual approval via GitHub's environment protection rules before deploying.

### Required GitHub Configuration

#### Secrets

| Secret | Environment | Description |
|--------|------------|-------------|
| `STAGING_SFTP_HOST` | staging | SFTP hostname for staging server |
| `STAGING_SFTP_USER` | staging | SFTP username for staging server |
| `STAGING_SFTP_PASSWORD` | staging | SFTP password for staging server |
| `PRODUCTION_SFTP_HOST` | production | SFTP hostname for production server |
| `PRODUCTION_SFTP_USER` | production | SFTP username for production server |
| `PRODUCTION_SFTP_PASSWORD` | production | SFTP password for production server |

#### Environments

- **staging** — No approval required
- **production** — Requires manual reviewer approval before deploy runs