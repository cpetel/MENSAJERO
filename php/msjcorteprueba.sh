#!/bin/sh
#monta
sudo mount -t cifs //192.168.0.11/CortesEnergia -o username=elastix@cpe.com,password=s1st3*cpe /mnt/ntserver
#cp ./tmp/estado.txt /mnt/ntserver/estado.txt
cp ./tmp/mensaje.txt /mnt/ntserver/pruebas/mensaje.txt
cp ./tmp/mapa.svg /mnt/ntserver/pruebas/mapa.svg
#desmonta
sudo umount /mnt/ntserver
