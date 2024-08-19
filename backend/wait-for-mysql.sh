# Wait for MySQL to be ready
 until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "show databases;" > /dev/null 2>&1; do
   echo "Waiting for MySQL..."
   sleep 2
 done

 echo "MySQL is up - executing command"
 exec "$@"

