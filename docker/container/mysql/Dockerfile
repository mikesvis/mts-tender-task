FROM mysql:8.0

# Set timezone
ARG TIMEZONE
RUN unlink /etc/localtime && ln -s /usr/share/zoneinfo/${TIMEZONE} /etc/localtime
