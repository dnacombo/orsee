<?php
// part of orsee. see orsee.org
ob_start();

include ("nonoutputheader.php");

if (isset($_REQUEST['subpool_id']) && $_REQUEST['subpool_id']) 
                $subpool_id=$_REQUEST['subpool_id']; else $subpool_id='';

//Include the code
include("../tagsets/class.phplot.php");
$stat=array();

$plot_defaults=array(
		'xsize'=>400,
		'ysize'=>200,
		'data_type'=>'text-data',
		'graphtype'=>'bars',
		'file_format'=>'png',
		'border_type'=>'full',
		'legend'=>array('none'),
		'legend_x'=>1,
		'legend_y'=>1,
		'title'=>'nothing',
		'xtitle'=>'none',
		'ytitle'=>'none',
		'reverse_data'=>true,
		'background_color'=>$color['stats_graph_background']
		);
        if(isset($_REQUEST['stype']) && substr($_REQUEST['stype'],0,6)=="pform:") {
		$stat=stats__array_pform_field(substr($_REQUEST['stype'],6));
        } elseif (isset($_REQUEST['stype']) && function_exists('stats__array_'.$_REQUEST['stype'])) {
            $fname='stats__array_'.$_REQUEST['stype'];
            $stat=$fname();
        } else {
            $stat['data']=array(array('no data',0));
        }


if (count($stat['data'])==0) $stat['data']=array(array(NULL,NULL));
elseif (count($stat['data'][0])==1) $stat['data'][0][]=0;

foreach ($plot_defaults as $key=>$value) 
	if (!isset($stat[$key])) $stat[$key]=$value;

//Define the object
$graph = new PHPlot($stat['xsize'],$stat['ysize']);
$graph->SetDataType($stat['data_type']);
$graph->SetFileFormat($stat['file_format']);
$graph->SetPlotType($stat['graphtype']);


$graph->SetDefaultTTFont('../tagsets/fonts/FreeSerif.ttf');

$graph->SetPlotBorderType('none'); // plotleft, plotright, both, full, none
$graph->SetBackgroundColor($stat['background_color']);
if($stat['graphtype']=='bars') {
	$graph->SetShading(0);
	$graph->SetPlotAreaWorld(NULL, 0);
}

if (count($stat['legend'])>0) {
	foreach ($stat['legend'] as $key=>$val) {
		if (strlen($val)>23) $stat['legend'][$key]=substr($val,0,20).'...';
		}
	$graph->SetLegend($stat['legend']);
	}

if ($stat['legend_x'] && $stat['legend_y']) $graph->SetLegendPixels($stat['legend_x'],$stat['legend_y']);
if ($stat['graphtype']=='pie') {
			$graph->SetPlotAreaPixels(150,0,$stat['xsize'],$stat['ysize']);
			$graph->SetLegendPixels(1,30);
			}

$graph->SetTitle($stat['title']);

if ($stat['xtitle']) $graph->SetXTitle($stat['xtitle'],'plotdown'); // plotup, plotdown, both, none
if ($stat['ytitle']) $graph->SetYTitle($stat['ytitle'], 'plotleft');// plotleft, plotright, both, plotin, none

// Please remember that angles other than 90 are taken as 0 when working fith fixed fonts.
if(isset($stat['x_label_angle'])) {
	$graph->SetXLabelAngle($stat['x_label_angle']);
} else {
	$graph->SetXLabelAngle(0);
}
$graph->SetYLabelAngle(0);

if (!(isset($stat['tick_increment_y']) && $stat['tick_increment_y']))
	$stat['tick_increment_y']=stats__get_y_increment($stat['data']);
$graph->SetYTickIncrement($stat['tick_increment_y']);
$graph->SetXTickLabelPos('plotdown'); // plotup, plotdown, both, xaxis, none
$graph->SetYTickLabelPos('both'); // plotleft, plotright, both, yaxis, none
$graph->SetXTickPos('plotdown'); // plotup, plotdown, both, xaxis, none
$graph->SetYTickPos('both'); // plotleft, plotright, both, yaxis, none

//Set some data

if ($stat['reverse_data'])
	$data=array_reverse($stat['data']);
   else $data=$stat['data'];
$graph->SetDataValues($data);

$graph->SetXTickLabelPos('none');
$graph->SetXTickPos('none');

//echo '<pre>';
//var_dump($stat);
//echo '</pre>';
//Draw it
if (!isset($_REQUEST['debug'])) $graph->DrawGraph();
?>
