<html>
    <head>
        <script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>
        <script type='text/javascript' src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
        <script type='text/javascript'>
            google.load('visualization', '1', {'packages': ['geochart']});
            google.setOnLoadCallback(drawVisualization);

            function drawVisualization() {
                
                /*
                var data = new google.visualization.DataTable();

                data.addColumn('number', 'Lat');                                
                data.addColumn('number', 'Long');
                data.addColumn('number', 'Value'); 
                data.addColumn({type:'string', role:'tooltip'});                        

                data.addRows([[41.151636,-8.569336,0,'tooltip']]);
                data.addRows([[ 39.059575,-98.789062,0,'tooltip']]);
                
                console.log(data);
                */
                
                chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
                var options = {
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
                
                var data = new google.visualization.DataTable();
                
                data.addColumn('number', 'Lat');                                
                data.addColumn('number', 'Long');
                data.addColumn('number', 'Value'); 
                data.addColumn({type:'string', role:'tooltip'});
                
                $.ajax({
                    url: "http://localhost/global-traffic-visualization/test.json",
                    dataType: "json"
                }).done(function(response) {
                    $.each(response, function() {
                        data.addRows([[parseFloat(this.latitude), parseFloat(this.longitude), 0, this.ip]]);
                    });
                    chart.draw(data, options);
                });
            }
        </script>
    </head>
    <body>
        <div id="chart_div" style="width: 900px; height: 500px;"></div>
    </body>
</html>