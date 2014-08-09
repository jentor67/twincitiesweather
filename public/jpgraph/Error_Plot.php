<?php

Class Custom_Error_Plot{

    function error_plot1() {
        require_once ('src/jpgraph.php');
        require_once ('src/jpgraph_line.php');
        require_once ('src/jpgraph_error.php');

//        $errdatay = array(11,9,2,4,19,26,13,19,7,12);
//        $datax = array('Jan','Feb','Mar','Apr','May');


// Create the graph. These two calls are always required
        $graph = new Graph(900,450);
        $graph->SetScale("textlin");

        $graph->img->SetMargin(40,30,20,40);
        $graph->SetShadow();

// Create the error plot
        $errplot=new ErrorLinePlot($this->errdatay);
        $errplot->SetColor("red");
        $errplot->SetWeight(2);
        $errplot->SetCenter();
        $errplot->line->SetWeight(2);
        $errplot->line->SetColor("blue");

// Add the plot to the graph
        $graph->Add($errplot);

        $graph->title->Set($this->title);
        $graph->xaxis->title->Set($this->x_title);
        $graph->yaxis->title->Set($this->y_title);

        $graph->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
        $graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

//        $datax = $gDateLocale->GetShortMonth();
        $graph->xaxis->SetTickLabels($this->datax);
        $graph->xaxis->SetTextLabelInterval($this->interval);

// Display the graph
//        $graph->Stroke();
        $graph->Stroke($this->file_name);

    }


}