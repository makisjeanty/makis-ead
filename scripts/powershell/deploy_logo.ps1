ssh root@195.26.252.210 "echo `"import binascii; open('/home/ETUDE-RAPIDE/web/etuderapide.com/public_html/public/images/brand/logo.png', 'wb').write(binascii.unhexlify(open('/tmp/logo.hex').read().strip()))`" > /tmp/install_logo.py"
ssh root@195.26.252.210 "python3 /tmp/install_logo.py"
ssh root@195.26.252.210 "chown -R ETUDE-RAPIDE:ETUDE-RAPIDE /home/ETUDE-RAPIDE/web/etuderapide.com/public_html/public/images/brand"
Write-Host "LOGO INSTALADO VIA SCRIPT PYTHON"
