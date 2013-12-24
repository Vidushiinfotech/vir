<html>
<head>
<script language="javascript" type="text/javascript" src="jquery.js"></script>
<script language="javascript" type="text/javascript" src="jquery.flot.js"></script>
<script type="text/javascript">
    $(document).ready(function (){
        
    
    
        var obj = $("#graph");
        var data = [[2010,50],[2012,60],[2014,70],[2016,80],[2018,90],[2020,40],[2022,0]];
        var series1 = [{
                color: '#ff0000',
                data: data,
                label: 'Price',
//                lines: { show: true },
                bars: { show: true, 
                    lineWidth: 0.5,
                    fill: false,
                    align: "center",
                    fillColor: '#DDDDDD' },
//                points: { show: true },
//                xaxis: {
//				min: 2010,
//				max: 2016
//			},
//			yaxis: {
//				min: 0,
//				max: 100
//			},
                clickable: true,
                hoverable: true,
//                shadowSize: 10,
                highlightColor: 'green'
            }];
        
        var options = {
//            series: {
//                lines: { show: true },
//                points: { show: true }
//            }
 xaxis: {
				min: 2009,
				max: 2025
			},
			yaxis: {
				min: 0,
				max: 100
			},
        };
        var plot = $.plot(obj, series1,options);

    });

</script>
</head>
<body>

    <div id="graph" style="width:600px;height:300px; font-size: 14px;;"></div>

</body>
</html>