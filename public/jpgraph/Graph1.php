<?php

class Custom_Graphs {


    function graph_two() {
            require_once ('src/jpgraph.php');
            require_once ('src/jpgraph_line.php');
            require_once ('src/jpgraph_date.php');
            require_once ('src/jpgraph_plotline.php');



            $futuretxt = new Text();
            $futuretxt->SetFont(FF_ARIAL,FS_NORMAL,10);
            $futuretxt->Set("36 Hour \nFuture Plot");
            $futuretxt->SetParagraphAlign('center');
            $futuretxt->SetPos(0.9,0.15,'right');
            $futuretxt->SetBox('lightblue');
            $futuretxt->SetShadow();

            $historictxt = new Text();
            $historictxt->SetFont(FF_ARIAL,FS_NORMAL,10);
            if( $this->back_ticks <> "" ) $historictxt->Set($this->back_ticks." hour \nHistoric Plot");
            $historictxt->SetParagraphAlign('center');
            $historictxt->SetPos(0.1,0.15,'left');
            $historictxt->SetBox('lightblue');
            $historictxt->SetShadow();

            if( $this->back_ticks <> "" ) $l1 = new PlotLine(VERTICAL, $this->back_ticks-1, 'red:1.0', 4);

        // Create the graph. These two calls are always required
            $width=900;
            $graph = new Graph($width,450,"auto");
            $graph->SetMargin(60,80,50,60);
            $graph->SetMarginColor('white');

            $graph->SetScale('textlin');
            if( isset($this->Scale) ) $graph->SetScale('textlin',$this->Min_Low,$this->Max_High);
//            $graph->SetYScale(0,'lin'); //New

            // Turn the tick marks out from the plot area
            $graph->yaxis->SetTickSide(SIDE_LEFT);

            // Create Temperature plot
            $lineplot = new LinePlot($this->graph_data1); //Array temperature
            $graph->yaxis->SetColor($this->color1);
            $graph->yaxis->title->Set($this->y1_title);
            $graph->yaxis->title->SetColor($this->color1);
            $graph->yaxis->title->SetFont(FF_ARIAL,FS_NORMAL,15);
            $graph->yaxis->SetTitleMargin(40);
            $graph->Add($lineplot);
            $lineplot->SetColor($this->color1);


            if( $this->y2_title <> ""){
                if( isset($this->add_to_yaxis)){
                    $p2 = new LinePlot($this->graph_data2);
                    $graph->Add($p2);
                    $p2->SetColor($this->color2);
                    //$p2->SetLegend('Line 2');
                }
                else{

                    $graph->SetYScale(0,'lin'); //New

                    if( isset($this->Scale) ) $graph->SetYScale(0,'lin',$this->Min_Low,$this->Max_High);
                    // Create the Wind plot
                    $lineplot_wind = new LinePlot($this->graph_data2); //Array Wind

                    $graph->AddY(0,$lineplot_wind);

                    $lineplot_wind->SetColor($this->color2);
                    $graph->ynaxis[0]->SetColor($this->color2);
                    $graph->ynaxis[0]->title->SetFont(FF_ARIAL,FS_NORMAL,16);
                    $graph->ynaxis[0]->title->Set($this->y2_title);
                    $graph->ynaxis[0]->title->SetColor($this->color2);
                    $graph->ynaxis[0]->SetTitleMargin(50);
                }
            }

            if( isset($this->y3_title)){
                $graph->SetYScale(0,'lin'); //New

                $graph->SetYScale(0,'lin',$this->min3,$this->max3);
                // Create the Wind plot
                $lineplot_wind = new LinePlot($this->graph_data3); //Array Wind

                $graph->AddY(0,$lineplot_wind);

                $lineplot_wind->SetColor($this->color3);
                $graph->ynaxis[0]->SetColor($this->color3);
                $graph->ynaxis[0]->title->SetFont(FF_ARIAL,FS_NORMAL,16);
                $graph->ynaxis[0]->title->Set($this->y3_title);
                $graph->ynaxis[0]->title->SetColor($this->color3);
                $graph->ynaxis[0]->SetTitleMargin(50);
            }



            $graph->xaxis->SetTickLabels($this->graph_xdata);
            $graph->xaxis->SetTextLabelInterval($this->interval); //$interval
            $graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,7);
            $graph->xaxis->SetColor('black');
            $graph->xaxis->SetPos('min');
            $graph->xaxis->SetTitleMargin(20);
            $graph->xaxis->title->Set($this->x_title);

            // Add the plot to the graph
            $graph->title->Set($this->title);
            $graph->title->SetFont(FF_ARIAL,FS_NORMAL,20);

            if( $this->back_ticks <> "" ) {
                $graph->AddLine($l1);
                $graph->Add($historictxt);
                $graph->Add($futuretxt);
            }


            // Display the graph
            $graph->Stroke($this->file_name);
            //        $graph->StrokeCSIM();
            //   $graph->StrokeCSIM( basename(__FILE__));
        }



    }

?>