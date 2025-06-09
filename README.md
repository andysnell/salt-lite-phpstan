# Custom PHPStan Rules for the SaltLite Framework and Applications

https://phpstan.org/developing-extensions/extension-types
https://phpstan.org/developing-extensions/rules
https://phpstan.org/developing-extensions/testing

## Local Development and Tooling

See the project [composer.json] file for the environment and dependency requirements.
This project has a standardized [Dockerfile] defined development image, and we recommended that it be used for all
development and testing. For convenience, the project includes a `docker-compose.yml` file and a `Makefile` to simplify/standardize the
development process and tooling.

#### Installation and Environment Setup

> Note: This guide assumes your host environment is Unix-like (Linux, macOS, WSL2), and that Docker is already installed.

Fork and clone this repository locally, navigate to the project root, and run the
(i.e. `cd /path/to/repository`) and then execute the following command to build
the Docker image, create build files, and install Composer dependencies:

```bash
make
```

While Composer is configured as a script-runner inside the container, `make`
is used externally from the host environment to run most of the common scripts.

```bash
# Run all tests and coding standards checks required to pass before a pull request can be accepted
make ci

# Run PHPStan to statically analyze the entire codebase
make phpstan

# Run the PHP syntax linter
make lint

# Run the PHP_CodeSniffer code standards linter
make phpcs

# Attempt to auto-fix coding standards issues found by phpcs
make phpbf

# Run the Prettier standards formatter to check supported files
make prettier-check

# Run the Prettier standards formatter to fix supported files
make prettier-write

# Run Rector with project configuration, without making changes
make rector-dry-run

# Run Rector with the project configuration and apply automated fixes
make rector

# Run PHPUnit tests
make phpunit
```
