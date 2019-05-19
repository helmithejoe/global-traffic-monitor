# global-traffic-monitor

This project utilize WebSocket mechanism to accomplish real-time traffic monitoring. WebSocket server will run as a proxy between traffic viewer client and the application. Any incoming request that come to the application will be forwarded as a traffic information to the WebSocket server and WebSocket server will immediately push this traffic information to the traffic viewer client.

Traffic viewer client connection to the WebSocket is using a unique authentication system to differentiate from any other connection that may come from application.

## Installation

### WebSocket

Clone this repository:
```
$ git clone https://github.com/helmithejoe/global-traffic-monitor.git
```
Go to project folder:
```
$ cd global-traffic-monitor
```
Open websocket/server.php to change WebSocket server's host and port:
```
//configure server parameters
$server = new WebSocket\Server('0.0.0.0', 8080, false);
```
Run WebSocket server in the background:
```
$ sudo php websocket/server.php &
```

### Application

Import database schema from db.sql:
```
$ mysql -u root -p traffic_db < db.sql
```
Copy configuration file from app/config/config.ini.example:
```
$ cp app/config/config.ini.example app/config/config.ini
```
Edit base_url & database configuration:
```
base_url=http://localhost:8000
```
```
db_str=mysql:host=localhost;port=3306;dbname=traffic
db_username=root
db_password=
```
Edit WebSocket configuration. websocket_host is for connection between application and WebSocket server.
websocket_public_host must be configured with public IP of WebSocket, because it will be used by traffic viewer client.
```
websocket_host=localhost
websocket_public_host=10.10.10.10
websocket_port=8080
```
Run composer install:
```
composer install
```
Run the application server:
```
$ sudo php -S 0.0.0.0:8000
```
Open application with a browser to view the traffic:

[http://localhost:8000/traffic/display](http://localhost:8000/traffic/display)

Open another browser to simulate an incoming traffic, and go to this URL:

[http://localhost:8000/traffic/test](http://localhost:8000/traffic/test)