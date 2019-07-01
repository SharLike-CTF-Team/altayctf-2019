#!/usr/bin/env bash
./wait-for.sh postgres:5432
indexer --rotate --all
searchd --nodetach
