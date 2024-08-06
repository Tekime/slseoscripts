<p>
Copy the code below and paste it anywhere within the &lt;body&gt;&lt;/body&gt; tags
to display a live, running JavaScript clock to visitors. 
</p>

<textarea class="sl-tool-code">
<span id="miniclock"><noscript>Enable JS to see clock</noscript></span>
<script language="JavaScript" type="text/javascript">
<!-- JavaScript Clock Provided by {site_name} - {site_url} -->
<!--
function runMiniClock()
{
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    minutes=((minutes < 10) ? "0" : "") + minutes;
    ampm = (hours >= 12) ? "PM" : "AM";
    hours=(hours > 12) ? hours-12 : hours;
    hours=(hours == 0) ? 12 : hours;
    var clock = hours + ":" + minutes + " " + ampm;
    if(clock != document.getElementById('miniclock').innerHTML) document.getElementById('miniclock').innerHTML = clock;
    timer = setTimeout("runMiniClock()",1000);
}
runMiniClock();
//-->
</script>
</textarea>

<script language="JavaScript" type="text/javascript">
<!-- JavaScript Clock Provided by {site_name} - {site_url} -->
<!--
function runMiniClock()
{
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    minutes=((minutes < 10) ? "0" : "") + minutes;
    ampm = (hours >= 12) ? "PM" : "AM";
    hours=(hours > 12) ? hours-12 : hours;
    hours=(hours == 0) ? 12 : hours;
    var clock = hours + ":" + minutes + " " + ampm;
    if(clock != document.getElementById('miniclock').innerHTML) document.getElementById('miniclock').innerHTML = clock;
    timer = setTimeout("runMiniClock()",1000);
}
runMiniClock();
//-->
</script>