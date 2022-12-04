#!/bin/sh
#monta
#//TESTMODEsudo mount -t cifs //192.168.0.11/CortesEnergia -o username=elastix@cpe.com,password=s1st3*cpe /mnt/ntserver
#cp ./tmp/estado.txt /mnt/ntserver/estado.txt
cp ./tmp/mensaje.txt /mnt/ntserver/mensaje.txt
cp ./tmp/mapa.svg /mnt/ntserver/mapa.svg
#desmonta
#//TESTMODEsudo umount /mnt/ntserver	
