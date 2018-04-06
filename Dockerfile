FROM unblibraries/drupal:alpine-nginx-php7-8.x-composer
MAINTAINER UNB Libraries <libsupport@unb.ca>

LABEL name="cabinetry.jacobsanford.com"
LABEL vcs-ref=""
LABEL vcs-url="https://github.com/jacobsanford/cabinetry.jacobsanford.com"

# Universal environment variables.
ENV DEPLOY_ENV prod
ENV DRUPAL_DEPLOY_CONFIGURATION TRUE
ENV DRUPAL_SITE_ID cabinetry
ENV DRUPAL_SITE_URI cabinetry.jacobsanford.com
ENV DRUPAL_SITE_UUID NULL
ENV DRUPAL_CONFIGURATION_EXPORT_SKIP devel

# Add scripts, remove delete upstream drupal build.
COPY ./scripts/container /scripts
RUN /scripts/DeployUpstreamContainerScripts.sh && \
  /scripts/deleteUpstreamTree.sh

# Add Mail Sending
RUN apk --update add postfix php7-redis && \
  rm -f /var/cache/apk/* && \
  touch /var/log/nginx/access.log && touch /var/log/nginx/error.log

# Tests.
COPY ./tests ${DRUPAL_TESTING_ROOT}

# Add package conf.
COPY ./package-conf /package-conf
RUN mv /package-conf/postfix/main.cf /etc/postfix/main.cf && \
  mv /package-conf/nginx/app.conf /etc/nginx/conf.d/app.conf && \
  mv /package-conf/php/app-php.ini /etc/php7/conf.d/zz_app.ini && \
  mv /package-conf/php/app-php-fpm.conf /etc/php7/php-fpm.d/zz_app.conf && \
  rm -rf /package-conf

# Deploy the generalized profile and makefile into our specific one.
COPY build/ ${TMP_DRUPAL_BUILD_DIR}
ENV DRUPAL_BUILD_TMPROOT ${TMP_DRUPAL_BUILD_DIR}/webroot
RUN /scripts/deployGeneralizedProfile.sh

# Build the drupal tree.
ARG COMPOSER_DEPLOY_DEV=no-dev
RUN /scripts/buildDrupalTree.sh ${COMPOSER_DEPLOY_DEV} && \
  /scripts/installDevTools.sh ${COMPOSER_DEPLOY_DEV} && \
  /scripts/clearComposerCache.sh && \
  # Copy config from standard install profile for current version of Drupal.
  rm -rf ${DRUPAL_BUILD_TMPROOT}/profiles/${DRUPAL_SITE_ID}/config && \
  cp -r ${DRUPAL_BUILD_TMPROOT}/core/profiles/standard/config ${DRUPAL_BUILD_TMPROOT}/profiles/${DRUPAL_SITE_ID}/ && \
  ls /tmp/drupal_build/webroot/profiles/${DRUPAL_SITE_ID}/config/install


# Copy configuration.
COPY ./config-yml ${TMP_DRUPAL_BUILD_DIR}/config-yml

# Custom modules not tracked in github.
COPY ./custom/modules ${TMP_DRUPAL_BUILD_DIR}/custom_modules
COPY ./custom/themes ${TMP_DRUPAL_BUILD_DIR}/custom_themes
