#!/bin/bash

ssh -p 65002 u549396201@nl-srv-web977.main-hosting.eu << EOF
  cd /home/u549396201/domains/therapair.com.au/public_html
  chmod +x deploy.sh
  ./deploy.sh
EOF