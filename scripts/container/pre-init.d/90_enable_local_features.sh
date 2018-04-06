#!/usr/bin/env sh
# Local alterations for your instance.
# i.e. drush --root=${DRUPAL_ROOT} --uri=default --yes en thirty_two_project

# Squash update emails.
drush --root=${DRUPAL_ROOT} --uri=default --yes config-set update.settings notification.emails.0 ''

# Import content.
if [ "$DEPLOY_ENV" = "local" ]; then
  drush en --yes cabinetry_core
  drush cache-rebuild
  drush en --yes cabinetry_cabinet_project
fi
