# currency-api
API Microservice to get recent currency rates from NBP

This microservice gathers currency rates from NBP API - http://api.nbp.pl/ 

It gets currency rates from table A & B provided by NBP.

Endpoints:

/list 
shows all available currencies and their codes.

/{code}
shows the actual average currency rate for specific {code} currency.

/{code}/average
shows the total average currency rate from all dates saved in the database.

The microservice provides the console command to update the database: bin/console app:update-database
It can be added to server's cron to become a scheduled task.
