<?php
require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Feed Control Panel</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.spinner.js"></script>
    <script type="text/javascript" src="js/kendo.all.min.js"></script>
    <link rel="stylesheet" href="css/kendo.common.min.css" />
    <link rel="stylesheet" href="css/kendo.default.min.css" />
    <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<div id="top">
    <div id="links">
        <p><strong>Feed Control Panel</strong></p>

        <p>Testing? <input type="checkbox" name="testing" id="testing" value="true"> <button class="runFeeds" type="button"><span style="color:green;text-align:center;">Run Feeds</span></button></p>

        <p><span style="color:red;">***</span>Before proceeding, SE file must be downloaded manually to 'feeds/download'<span style="color:red;">***</span></p>

        <p>Individual steps: (for special cases)</p>

        <ul>
            <li class="actions"><a href="#" id="getSE" name="getSE">Get SE file</a> (experimental: do not use)</li>

            <li class="actions"><a href="#" id="runSPE" name="runSPE">Run Specialized file</a></li>

            <li class="actions"><a href="#" id="runSE" name="runSE">Run SE file</a></li>

            <li class="actions"><a href="#" id="runRpro" name="runRpro">Run RPro file</a></li>

            <li class="actions"><a href="#" id="runOnline" name="runOnline">Create &amp; upload Online Listings file</a></li>

            <li class="actions"><a href="#" id="runLocal" name="runLocal">Create &amp; upload Local Products file</a></li>

            <li class="actions"><a href="#" id="runPrice" name="runPrice">Create &amp; upload Price-Quantity file</a></li>

            <li class="actions"><a href="#" id="runqBike" name="runqBike">Create &amp; upload qBike file</a> (experimental: do not use) USE THIS-&gt; <a href="http://www.qbike.com/cgi-bin/mikesbikes-data.cgi" target="_new">qBike Upload</a></li>

            <li class="actions"><a href="#" id="runBizList" name="runBizList">Create &amp; upload Business Listings file</a> (Once per month)</li>
        </ul>
    </div>

    <div id="status_container">
            <div id="totals_container">
                <div id="totals"></div>
            </div>
            <div id="messages_container">
                <div id="messages"></div>
            </div>
    </div>
</div>
<div id="bottom" class="k-content">
    <div class="chart-wrapper" style="margin:auto;">
        <div id="chart"></div>
    </div>
    <script>

    var items = <?php echo getItemCountsJSON(360); ?>;

    function createChart() {
        $("#chart").kendoChart({
            theme: $(document).data("kendoSkin") || "default",
            chartArea: {
                width: 1400
            },
            title: {
                text: 'Mike\'s Bikes Feed Statistics'
            },
            legend: {
                position: "bottom"
            },
            seriesDefaults: {
                type: "line",
                labels: {
                    visible: false
                }
            },
            dataSource: {
                data: items,
                group: {
                    field: "item_type",
                    dir: "asc"
                }
            },
            series: [
                     {
                        field: "item_count"
                     }
                    ],
            /*
            categoryAxis: {
                field: "timestamp",
                labels: {
                    rotation: -45
                }
            },
            */
            tooltip: {
                visible: true,
                template: "#= series.name # - #= value #"
            }
        });
    }
    
    $(document).ready(function() {
/*
                setTimeout(function() {
                    // Initialize the chart with a delay to make sure
                    // the initial animation is visible
                    createChart();
            
                    $("#bottom").bind("kendo:skinChange", function(e) {
                        createChart();
                    });
                }, 1000);
*/
        /* Run Feeds */
        $(document).on("click", "button.runFeeds", function(){
            /* check for testing state */
            var testing = $("input[name='testing']").attr("checked") === 'checked' ? true : false;
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            if(!testing){$this.spinner(opts);}
                /* empty the results divs */
                $("div#totals").html('');
                $("div#messages").html('');

                $.ajax({
                    // Process SmartEtailing file //
                    type: "POST",
                    url: "parse_googlebase.php",
                    success: function(response) {

                        $("div#totals").html( $("div#totals").html() + response );

                       // Process Specialized file //
                       $.ajax({
                            type: "POST",
                            url: "parse_specialized.php",
                            success: function(response) {
     
                                $("div#totals").html( $("div#totals").html() + response );

                                // Process Raleigh file //
                                $.ajax({
                                     type: "POST",
                                     url: "parse_raleigh.php",
                                     success: function(response) {
              
                                         $("div#totals").html( $("div#totals").html() + response );
                                         
                                          // Process RPRO's file //
                                          $.ajax({
                                                  type: "POST",
                                                  url: "parse_inv.php",
                                                  success: function(response) {
              
                                                      $("div#totals").html( $("div#totals").html() + response );
if (!testing) {
                                                      // output and upload google-online file //
                                                      $.ajax({
                                                              type: "POST",
                                                              url: "save_online_listings.php",
                                                              success: function(response) {
              
                                                                  $("div#messages").html( $("div#messages").html() + response );
              
                                                                  
                                                                    // output and upload google-local file //
                                                                    $.ajax({
                                                                            type: "POST",
                                                                            url: "save_localproduct_listings.php",
                                                                            success: function(response) {
                
                                                                                $("div#messages").html( $("div#messages").html() + response );
                
                                                                                // output and upload google-inventory file //
                                                                                $.ajax({
                                                                                        type: "POST",
                                                                                        url: "save_price_quantity.php",
                                                                                        success: function(response) {
                
                                                                                            $("div#messages").html( $("div#messages").html() + response );
                
                                                                                            // output and upload qBike file //
                                                                                            $.ajax({
                                                                                                    type: "POST",
                                                                                                    url: "save_qbike_listings.php",
                                                                                                    success: function(response) {
                                                                                                        $this.spinner('remove');
                                                                                                        $("div#messages").html( $("div#messages").html() + response );
                                                                                                        
                                                                                                        // show chart
                                                                                                        createChart();
                                                                                                    }
                                                                                            });
                                                                                        }
                                                                                });
                                                                            }
                                                                    });
                                                              }
                                                      });
}
                                                  }
                                          });
                                     }
                                });
                            }
                       });
                    }
                });
                return false;
        });
        
        
        /* Manual Feed Functions */
        
        $(document).on("click", "a#runSPE", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "parse_specialized.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });

        $(document).on("click", "a#getSE", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $("div#messages").load("SE/SE_test1.html");
            $this.spinner('remove');
            return false;
        });

        $(document).on("click", "a#runSE", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "parse_googlebase.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
        $(document).on("click", "a#runRpro", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "parse_inv.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
        $(document).on("click", "a#runOnline", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "save_online_listings.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
        $(document).on("click", "a#runLocal", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "save_localproduct_listings.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
        $(document).on("click", "a#runPrice", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "save_price_quantity.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
        $(document).on("click", "a#runqBike", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "save_qbike_listings.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
        $(document).on("click", "a#runBizList", function(){
            var $this = $(this);
            var opts = {
                img: 'images/ajax-loader-green.gif',
                height: 15,
                width: 128
            };
            $this.spinner(opts);
            $.ajax({
                    type: "POST",
                    url: "save_business_listings.php",
                    success: function(response) {
                        $this.spinner('remove');
                        $("div#messages").html(response);
                    }
            });
            return false;
        });
    });
    </script>
</div>
</body>
</html>