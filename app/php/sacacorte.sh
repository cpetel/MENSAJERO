#!/bin/sh
#monta
sudo mount -t cifs //192.168.0.11/CortesEnergia -o username=elastix@cpe.com,password=s1st3*cpe /mnt/ntserver
#rm /mnt/ntserver/estado.txt
rm /mnt/ntserver/mensaje.txt
rm /mnt/ntserver/mapa.svg
#desmonta
sudo umount /mnt/ntserver	
