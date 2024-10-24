#!/bin/bash

# Charge les fixtures pour l'environnement de test
echo "Chargement des fixtures..."
symfony console doctrine:fixtures:load --group=test -n --env=test --purge-with-truncate 2>&1 | tee load_fixtures.log
