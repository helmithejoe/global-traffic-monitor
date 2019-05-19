<html>
    <head>
        <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
        <script type='text/javascript' src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
        
    </head>
    <body>
        <form>
            <input name="method" type="radio" value="realtime" checked="checked"/>Real-Time
            <input name="method" type="radio" value="historical"/>Last
            <select id="hour" name="hour">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select> hour(s).
        </form>
        <div id="chart_div" style="width: 900px; height: 500px;"></div>
        <script type='text/javascript'>
            google.load('visualization', '1', {'packages': ['geochart']});
            google.setOnLoadCallback(drawVisualization);
            
            function clearChartData() {
                if(typeof data != "undefined") {
                    data = new google.visualization.DataTable();
                    data.addColumn('number', 'Lat');                                
                    data.addColumn('number', 'Long');
                    data.addColumn('number', 'Value'); 
                    data.addColumn({type:'string', role:'tooltip'});
                    chart.draw(data, options);
                }
            }
            
            function timestampToDatetime(timestamp) {
                // Create a new JavaScript Date object based on the timestamp
                // multiplied by 1000 so that the argument is in milliseconds, not seconds.
                var date = new Date(timestamp * 1000);
                // Hours part from the timestamp
                var hours = "0" + date.getHours();
                // Minutes part from the timestamp
                var minutes = "0" + date.getMinutes();
                // Seconds part from the timestamp
                var seconds = "0" + date.getSeconds();

                // Will display time in 10:30:23 format
                var formattedTime = hours.substr(-2) + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
                return formattedTime;
            }
            
            /**
            * ajax function to load historical traffic
            */
            function ajaxLoad(lasthour) {
                //disable disappearing location marker
                //because historical data must always appear
                clearInterval(interval);
                //re-init chart
                clearChartData();
                $.ajax({
                    url: "{{ @base_url }}{{ @xhour_action_url }}?lasthour=" + lasthour,
                    dataType: "json"
                }).done(function(response) {
                    $.each(response, function() {
                        var datetime = timestampToDatetime(parseInt(this.request_time));
                        var info = this.ip + ' - ' + this.request_method + ' ' + this.request_uri + 
                            ' [' + datetime + ']';
                        data.addRows([[parseFloat(this.latitude), parseFloat(this.longitude), 0, info]]);
                    });
                    chart.draw(data, options);
                });
            }
            
            /**
            * websocket init
            */
            function wsInit() {
                serverUrl = 'ws://{{ @websocket_public_host }}:{{ @websocket_port }}{{ @websocket_application }}';
                if(window.MozWebSocket) {
                    socket = new MozWebSocket(serverUrl);
                } else if(window.WebSocket) {
                    socket = new WebSocket(serverUrl);
                }
            }
            
            /**
            * websocket function to load real-time traffic
            */
            function wsLoad() {
                //re-init chart
                clearChartData();
                //real-time data must disappear as soon as the request ends
                //however we still need sometime for our eyes to see
                //so we setup duration of time when location markers will disappear (milliseconds)
                timeout = {{ @traffic_marker_timeout }};
                
                socket.binaryType = 'blob';
                socket.onopen = function(msg) {
                    console.log('connected');
                    var payload;
                    payload = new Object();
                    payload.action = '{{ @websocket_login_action }}';
                    payload.data = '{{ @websocket_admin_token }}';
                    socket.send(JSON.stringify(payload));
                };
                socket.onmessage = function(msg) {
                    var response;
                    response = JSON.parse(msg.data);
                    
                    var datetime = timestampToDatetime(parseInt(response.request_time));
                    var info = response.ip + ' - ' + response.request_method + ' ' + response.request_uri + 
                        ' [' + datetime + ']';
                    data.addRows([[parseFloat(response.latitude), parseFloat(response.longitude), 0, info]]);
                    chart.draw(data, options);
                    
                    //refresh timeout so that timeout of all data will always be consistent 
                    timeout = {{ @traffic_marker_timeout }};
                };
                socket.onclose = function() {
                    console.log('closed');
                };
                
                //implement interval for disappearing markers
                interval = setInterval(function(){
                    clearChartData();
                }, timeout);
            }
            
            //init websocket once
            wsInit();
            
            //choosing between realtiime or x hour
            $('input[name=method]').click(function() {
                if($(this).val() == 'realtime') {
                    //prevent double connection
                    if(socket.readyState !== socket.OPEN){
                        wsInit();
                    }
                    wsLoad();
                } else {
                    var lasthour = $('#hour').val();
                    ajaxLoad(lasthour);
                }
            });
            
            //trigger real-time option
            $('input[name=method]').filter(function(){return this.value=='realtime'}).click();
            
            //when user change hour selection
            $('#hour').change(function(){
                var lasthour = $(this).val();
                $('input[name=method]').filter(function(){return this.value=='historical'}).prop('checked="checked"');;
                $('input[name=method]').filter(function(){return this.value=='historical'}).click();
            });

            function drawVisualization() {
                chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
                options = {
                    colorAxis: {
                        minValue: 0,
                        maxValue: 0,
                        colors: ['#6699CC']
                    },
                    legend: 'none',
                    backgroundColor: {
                        fill: 'transparent',
                        stroke: '#FFF',
                        strokeWidth: 0
                    },    
                    datalessRegionColor: '#f5f5f5',
                    displayMode: 'markers',
                    enableRegionInteractivity: 'true',
                    resolution: 'countries',
                    sizeAxis: {
                        minValue: 1,
                        maxValue: 1,
                        minSize: 5,
                        maxSize: 5
                    },
                    region: 'world',
                    keepAspectRatio: true,
                    width: 900,
                    height: 500,
                    tooltip: {
                        textStyle: {
                            color: '#444444'
                        }
                    }    
                };

                //data initialization
                
                data = new google.visualization.DataTable();
                data.addColumn('number', 'Lat');                                
                data.addColumn('number', 'Long');
                data.addColumn('number', 'Value'); 
                data.addColumn({type:'string', role:'tooltip'});
                chart.draw(data, options);
            }
            
            
    
        </script>
    </body>
</html>