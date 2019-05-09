$( document ).ready(function() {


    var resizeChart;

    $(window).resize(function(e) {
        clearTimeout(resizeChart);
        resizeChart = setTimeout(function() {
            DrawSparkline();
        }, 300);
    });

    var flot1 = function () {
        var data = [[0, 50], [1, 42], [2, 40], [3, 65], [4, 48], [5, 56], [6, 80]];
        var data2 = [[0, 25], [1, 19], [2, 20], [3, 35], [4, 23], [5, 28], [6, 45]];
        var dataset =  [
            {
                data: data,
                color: "#E0E0E0",
                lines: {
                    show: true,
                    fill: 0.4,
                },
                shadowSize: 0,
            }, {
                data: data,
                color: "#E0E0E0",
                lines: {
                    show: false,
                },
                points: {
                    show: true,
                    fill: true,
                    radius: 4,
                    fillColor: "#fff",
                    lineWidth: 2
                },
                curvedLines: {
                    apply: false,
                },
                shadowSize: 0
            }, {
                data: data2,
                color: "#26A69A",
                lines: {
                    show: true,
                    fill: 0.4,
                },
                shadowSize: 0,
            },{
                data: data2,
                color: "#26A69A",
                lines: {
                    show: false,
                },
                curvedLines: {
                    apply: false,
                },
                points: {
                    show: true,
                    fill: true,
                    radius: 4,
                    fillColor: "#fff",
                    lineWidth: 2
                },
                shadowSize: 0
            }
        ];

        var ticks = [[0, "Mon"], [1, "Tue"], [2, "Wed"], [3, "Thu"], [4, "Fri"], [5, "Sat"], [6, "Sun"]];

        var plot1 = $.plot("#flotchart1", dataset, {
            series: {
                color: "#14D1BD",
                lines: {
                    show: true,
                    fill: 0.2
                },
                shadowSize: 0,
                curvedLines: {
                    apply: true,
                    active: true
                }
            },
            xaxis: {
                ticks: ticks,
            },
            legend: {
                show: false
            },
            grid: {
                color: "#AFAFAF",
                hoverable: true,
                borderWidth: 0,
                backgroundColor: '#FFF'
            },
            tooltip: true,
            tooltipOpts: {
                content: "%yK",
                defaultTheme: false
            }
        });

    };

    flot1();

    var flotchart2 = function () {

        // We use an inline data source in the example, usually data would
        // be fetched from a server

        var data = [],
            totalPoints = 50;

        function getRandomData() {

            if (data.length > 0)
                data = data.slice(1);

            // Do a random walk

            while (data.length < totalPoints) {

                var prev = data.length > 0 ? data[data.length - 1] : 50,
                    y = prev + Math.random() * 10 - 5;

                if (y < 0) {
                    y = 0;
                } else if (y > 75) {
                    y = 75;
                }

                data.push(y);
            }

            // Zip the generated y values with the x values

            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]])
            }

            return res;
        }

        var plot4 = $.plot("#flotchart2", [ getRandomData() ], {
            series: {
                shadowSize: 0   // Drawing is faster without shadows
            },
            yaxis: {
                min: 0,
                max: 75
            },
            xaxis: {
                min: 0,
                max: 50
            },
            colors: ["#26A69A"],
            legend: {
                show: false
            },
            grid: {
                color: "#AFAFAF",
                hoverable: true,
                borderWidth: 0,
                backgroundColor: '#FFF'
            },
            tooltip: true,
            tooltipOpts: {
                content: "Y: %y",
                defaultTheme: false
            }
        });

        function update() {
            plot4.setData([getRandomData()]);

            plot4.draw();
            setTimeout(update, 2000);
        }

        update();

    };

    flotchart2();


    $(document).on("fixedSidebarClick", function() {
        clearTimeout(resizeChart);
        resizeChart = setTimeout(function() {
            DrawSparkline();
        }, 300);
    });
});