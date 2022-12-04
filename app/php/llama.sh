cp llama.call llamaahora.call
NOW=`date +%s`

let NOW=$NOW+10
    
        
TOUCH_TMSP=`date -d "1970-01-01 $NOW sec GMT" +%Y%m%d%H%M.%S`
        
            
touch -t $TOUCH_TMSP llamaahora.call
pause
mv llamaahora.call /var/spool/asterisk/outgoing/
