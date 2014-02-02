#!/bin/bash
cd .ssh 
ssh ec2-user@hueclues.com -i hueclues.pem
sudo su
cd /var/www/html
git pull origin master
