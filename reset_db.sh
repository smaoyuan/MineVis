#!/bin/bash

./symfony doctrine:drop-db
./symfony doctrine:build-db
./symfony doctrine:insert-sql
./symfony doctrine:data-load
