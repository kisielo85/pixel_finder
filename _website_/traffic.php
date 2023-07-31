<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<div style="width:100%; height:100%">
  <canvas id="myChart" style="max-width:100%; max-height: 100%;"></canvas>
</div>

<?php
  error_reporting(0);
  include 'config.php';
  $data=file_get_contents("http://$ip/traffic");
  error_reporting(1);

  $data=rtrim($data,";");
  $data=explode(";",$data);
  $dates="";
  $tr17="";
  $tr22="";
  $tr23="";
  foreach (array_slice($data,-100) as $d){
    $a=explode(",",$d);
    $dates.='"'.$a[0].'",';
    $tr17.=$a[1].',';
    $tr22.=$a[2].',';
    $tr23.=$a[3].',';
  }
?>

<script>
  const xValues = [<?php echo $dates?>];

  new Chart("myChart", {
    type: "line",
    data: {
      labels: xValues,
      datasets: [{ 
        data: [<?php echo $tr17?>],
        borderColor: "red",
        fill: false
      }, { 
        data: [<?php echo $tr22?>],
        borderColor: "green",
        fill: false
      }, { 
        data: [<?php echo $tr23?>],
        borderColor: "blue",
        fill: false
      }]
    },
    options: {
      legend: {display: false}
    }
  });
</script>