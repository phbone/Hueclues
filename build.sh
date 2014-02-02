#!/bin/bash
cd .ssh 
ssh ec2-user@54.201.191.133 -i hcprod.pem
sudo su
cd /var/www/html
git pull origin master
