$(document).ready(function() {
    
    /* Automagic */
    $("button.runFeeds").click(function(){
        /* check for testing state */
        var testing = $("input[name='testing']").attr("checked") === 'checked' ? true : false;
        
        var $this = $(this);
        var opts = {
            img: 'images/ajax-loader-green.gif',
            height: 15,
            width: 128
        };
        if(!testing){$this.spinner(opts);}

        /* empty the messages div */
        $("div#messages").html('');
        
        if (!testing) {
            $.ajax({
                   type: "POST",
                   url: "parse_googlebase.php",
                   success: function(response) {
                       
                       $("div#messages").html( $("div#messages").html() + response );
                           // Process RPRO's file //
                           $.ajax({
                                   type: "POST",
                                   url: "parse_inv.php",
                                   success: function(response) {
                                       
                                       $("div#messages").html( $("div#messages").html() + response );
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

                                                                                       // finally, show the chart
                                                                                       showChart(180);
                                                                                   }
                                                                           });
                                                                       }
                                                               });
                                                           }
                                                   });
                                               }
                                       });
                                   }
                           });
                   }
            });
            return false;
        }
    });
});

    /* Chart */
    function showChart (days) {
        var options = {
            lines: { show: true },
            points: { show: true },
            xaxis: { mode: "time" }
        };
        var counts = [];
        var testdata = [[1351032830000,5099],[1351114910000,5101],[1351191732000,5122],[1351280147000,14525],[1351532069000,5139]];
        var placeholder = $("#chart");
        $.plot(placeholder, testdata, options );
        function onDataReceived(series) {
            //counts = counts.concat(series);
            //alert(series.toString);
            $.plot(placeholder, testdata, options );
            alert(JSON.stringify(series, null, 4));
            
        }
        
        function getData(itemtype,days){
            $.ajax({
                url: "getItemCounts.php?itemtype="+itemtype+"&days="+days,
                method: 'GET',
                dataType: 'json',
                success: onDataReceived
            });
        }
        getData('SE',days);
        //getData('RPRO',days);
        //getData('GTIN',days);
        //getData('MPN',days);
        //getData('total_matches',days);
    }
    
    /*
       Manual
               */
    $("a#getSE").live(function(){
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

    $("a#runSE").live(function(){
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
    $("a#runRpro").live(function(){
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
    $("a#runOnline").live(function(){
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
    $("a#runLocal").live(function(){
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
    $("a#runPrice").live(function(){
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
    $("a#runqBike").live(function(){
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
    $("a#runBizList").live(function(){
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