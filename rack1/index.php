<?php
session_start();
include('../connection.php');
include('../database_functions.php');
include('../algorithms.php');

$userid = $_SESSION['userid'];
include('../global_tools.php');
include('../global_objects.php');
?>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
    <head>

        <link rel="stylesheet" title="Standard" href="styles.css" type="text/css" media="screen" />
        <link ref="stylesheet" href='../css/global.css' type='text/css'/>
        <script type="text/javascript" src="contentflow_src.js" load="HANGING"></script>
        <link rel="stylesheet" href="jQueryUI.css" />
        <script type="text/javascript" src="js/jquery-1.9.1.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>



        <script>
            $(function() {
                //var DragEventInfo = new Object();
                var editMode = false;
                $(".item").draggable({
                    appendTo: "body",
                    helper: "clone",
                    disabled: true,
                    revert: true,
                    start: function(event, ui) {
                        DragEventInfo.object = event.target;
                        DragEventInfo.helper = ui.helper;
                        // $(DragEventInfo.object).toggle(); // hide the original item
                        $(this).toggle();
                        event.stopPropagation();
                        $(ui.helper.children()[1]).toggle(); // hide the caption
                        $(ui.helper.children()[0]).height($(ui.helper).height()); // set the height of the image

                        //                                // use the mouseover to detect the index of the element
                        //                                $(".item").mouseover(function(event){
                        //                                    if(event.target.itemIndex){
                        //                                DragEventInfo.targetIndex = event.target.itemIndex;
                        //                                if(DragEventInfo.targetIndex)                        
                        //                                    alert(DragEventInfo.targetIndex);
                        //                                else alert("Index is null");
                        //                                $( "#IndexLabel" ).html(""  + DragEventInfo.targetIndex);
                        //                                }
                        //                                else alert("no .itemIndex property");
                        //                                });

                    },
                    stop: function() {
                        $(this).toggle();
                    }
                });

                //Set up the jQuery drag stuff
                $(".item").mousedown(function(event) {
                    if (editMode) {
                        event.stopPropagation();
                    }
                });

                $("#EditButton").click(function(event) {

                    editMode = !editMode;
                    if (editMode) {
                        $(".item").draggable("enable");
                        $(this).html("View");
                    }
                    else {
                        $(this).html("Edit");
                        $(".item").draggable("disable");
                    }
                });

                $(".ContentFlow").droppable({
                    accept: ".item",
                    drop: function(event) {
                        // this prevents it from sending out a double click event (which it does for some odd reason)
                        event.preventDefault();
                        //alert(DragEventInfo.object.parentNode.parentNode.id);

                        // First make sure the element is not being dropped on its original container
                        if ($(this).attr("id") != DragEventInfo.object.parentNode.parentNode.id) {

                            // Loop over all currently existing flows	   
                            for (var i = 0; i < globalFlowElement.Flows.length; i++) {
                                // when you find the one being dropped on and as long as it is not the same
                                if (globalFlowElement.Flows[i].Container.id == $(this).attr("id")) {
                                    // append the dragged element to it
                                    globalFlowElement.Flows[i]._addItem(DragEventInfo.object, 0);
                                    // move to hte added item
                                    globalFlowElement.Flows[i].moveToIndex(0);
                                    // cancel revert
                                    $(DragEventInfo.helper).toggle();


                                }
                                else if (globalFlowElement.Flows[i].Container.id == DragEventInfo.object.parentNode.parentNode.id) {
                                    // remove the item
                                    globalFlowElement.Flows[i].rmItem(DragEventInfo.object.itemIndex);
                                }
                            }
                        }
                    }
                    //	  	out: function(event){
                    //	  		
                    //	  	},
                    //	  	over: function(event, ui){
                    //	  		if(!DragEventInfo.currentDropTarget){
                    //	  		// Loop over all currently existing flows
                    //			  for (var i = 0; i < globalFlowElement.Flows.length; i++){
                    //			  	  // when you find the one being dropped on and as long as it is not the same
                    //			      if(globalFlowElement.Flows[i].Container.id == this.id) {
                    //			      	// set that as the current drop target
                    //			        DragEventInfo.currentDropTarget = this;
                    //			      }
                    //			  }
                    //	  		}
                    //	  		// swap the position of the element being dragged and current target index
                    //	  		var tmp = DragEventInfo.currentDropTarget.items[DragEventInfo.targetIndex];
                    //	  		DragEventInfo.currentDropTarget.items[DragEventInfo.targetIndex] = DragEventInfo.object;
                    //	  		DragEventInfo.currentDropTarget.items[DragEventInfo.object.itemIndex] = tmp;
                    //	  		
                    //	  		// reposition items to apply the changes
                    //	  		DragEventInfo.currentDropTarget._positionItems();
                    //	  	}
                });
                // function allowDrop(ev){
                // // simply allow the drop action
                // ev.preventDefault();
                // // add an empty div at
                // }
                // 	  

            });




        </script>
    </head>

    <body>

        <!-- Edit Button -->
        <button id="EditButton" style='width:100px;height:50px;font-size:35px;top:20px;position:absolute;z-index:3;'>
            Edit
        </button>

        <!--  ===== FLOW ===== --->
        <div id="contentFlow" class="ContentFlow" >
            <!--  should be place before flow so that contained images will be loaded first -->
            <div class="loadIndicator"><div class="indicator"></div></div>
            <div class="flow">
                <?php
                $i = 0;
                $itemQuery = mysql_query("SELECT * FROM item WHERE userid='".$userid."'");
                while ($item = mysql_fetch_array($itemQuery)) {
                    
                    $itemObject = returnItem($item['itemid']);
                    echo "<div id='item$i' class='item' >
                    <img class='content' src='" . $itemObject->image_link . "' />
                    <div class='caption'>".$itemObject->description."</div>
                </div>";
                    $i++;
                }
                ?>

            </div>
            <div class="globalCaption"></div>
            <div class="scrollbar">
                <div class="slider">
                    <div class="position"></div>
                </div>
            </div>
        </div>
        <!--  ===== FLOW ===== --->
        <div id="contentFlow1" class="ContentFlow" >
            <!--  should be place before flow so that contained images will be loaded first -->
            <div class="loadIndicator"><div class="indicator"></div></div>
            <div class="flow">


            </div>
            <div class="globalCaption"></div>
            <div class="scrollbar">
                <div class="slider">
                    <div class="position"></div>
                </div>
            </div>
        </div>
    </body>
</html>
