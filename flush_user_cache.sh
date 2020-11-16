#!/usr/bin/env bash

redis-cli -s /var/run/redis/redis.sock del "user_id:$1"