# Changelog

All notable changes to `suspicion` will be documented in this file

## 0.2.1 - 2022-03-15

- Suspicion now includes global middleware to block repeat offenders.
  - The threshold, http status code and error message can be configured using the `repeat_offenders` key in `config/suspicion.php`. If you are upgrading from a previous version, this config key must be added manually.
