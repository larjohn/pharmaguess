/**
 * Created by larjo on 14/5/2016.
 */


var data = {
    currentDrug : null,
    credit : 100,
    discoveredDrugs : [],
    missedDrugs: [],
    playedDrugs : [],
    selectedNode : null,
    tree : {}
};



$(document).ready(function () {

    var template = $.templates("#theTmpl");

    template.link("#result", data);

    $("body").arrive("#retrieve-btn", function () {
        $(this).click(function () {
            $.observable(data.selectedNode).setProperty("locked", false);
            $.observable(data).setProperty("credit", (data.credit - data.selectedNode.cost).toFixed(1));
            if(data.selectedNode.root){
                $.each(data.selectedNode._children, function (index, value) {
                    $.observable(value).setProperty("locked", false);
                });
                $.each(data.selectedNode.children, function (index, value) {
                    $.observable(value).setProperty("locked", false);
                });
                $.observable(data.playedDrugs).insert(data.currentDrug.id);
                $.observable(data.missedDrugs).insert(data.currentDrug);
                if(data.credit<0)
                    gameOver("Game OVER", "You 're finished");
                else{
                    successContinueGame("OK!", "You spoiled it! The drug is "+data.currentDrug.value+". Do you want to keep trying with the next drug?")
                }

            }
            data.tree.update(data.tree.root);

        });
    });

    $("body").arrive("#guess-btn", function () {
        $(this).click(function () {

            bootbox.dialog({
                message: "Drug:<div id='guess-list-container' ></div>",
                title: "What is the drug?",
                buttons: {
                    main: {
                        label: "Guess",
                        className: "btn-primary",
                        callback: function() {
                            if($('#guess-list').val()==data.currentDrug.id){
                                $.observable(data.selectedNode).setProperty("locked", false);
                                $.observable(data).setProperty("credit", Math.min(100,data.credit + data.selectedNode.cost).toFixed(1));
                                if(data.selectedNode.root){
                                    $.each(data.selectedNode._children, function (index, value) {
                                        $.observable(value).setProperty("locked", false);
                                    });
                                    $.each(data.selectedNode.children, function (index, value) {
                                        $.observable(value).setProperty("locked", false);
                                    });
                                }
                                $.observable(data.playedDrugs).insert(data.currentDrug.id);
                                $.observable(data.discoveredDrugs).insert(data.currentDrug);

                                data.tree.update(data.tree.root);
                                successContinueGame("Yeah!", "You are the one! Do you want to keep trying with the next drug?")
                            }
                            else{
                                $.observable(data).setProperty("credit", (data.credit - data.selectedNode.cost/2).toFixed(1));
                                if(data.credit<0)
                                    gameOver("Game OVER", "You 're finished");
                                else{
                                    keepInTheSameDrug("Nope!", "But you can try again!")

                                }
                            }

                        }
                    }
                }
            });



        });
    });

    $("body").arrive("#guess-list-container", function () {
        var template = $.templates("#theListTmpl");

        template.link("#guess-list-container", data);

    });


    function keepInTheSameDrug(title, message) {
        bootbox.dialog({
            closeButton: false,
            message: message,
            title: title,
            buttons: {
                success: {
                    label: "Go on!",
                    className: "btn-success",
                    callback: function() {
                        //Example.show("great success");
                    }
                },
                danger: {
                    label: "Go Away!",
                    className: "btn-danger",
                    callback: function() {
                        leaveGame();
                    }
                },

            }
        });
    }


    function gameOver(title, message) {
        bootbox.dialog({
            closeButton: false,
            message: message,
            title: title,
            buttons: {
                success: {
                    label: "Restart!",
                    className: "btn-success",
                    callback: function() {
                        leaveGame();

                    }
                },


            }
        });
    }

    function successContinueGame(title, message) {
        bootbox.dialog({
            closeButton: false,
            message: message,
            title: title,
            buttons: {
                success: {
                    label: "Go on!",
                    className: "btn-success",
                    callback: function() {
                        loadDrug();
                    }
                },
                danger: {
                    label: "Tired!",
                    className: "btn-danger",
                    callback: function() {
                        leaveGame();
                    }
                }

            }
        });
    }

    loadDrug();

    function leaveGame() {
        window.location = "end";
    }

    function loadDrug(){
        $.observable(data).setProperty("currentDrug", null);

        $.observable(data).setProperty("selectedNode", null);
        $("#tree-container").empty();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post("data.json", {playedDrugs:data.playedDrugs}, function(json, textStatus) {
            var tree = new Tree(json.drug);
            $.observable(data).setProperty("tree",tree );
            $.observable(data).setProperty("allDrugs",json.drugs );
        }, "json");


    }


    var Tree = function (currentDrug) {


        this.theTree = this;
        $.observable(data).setProperty("currentDrug", currentDrug);
        var treeData= this.treeData =  currentDrug;
        console.log(treeData);

        // Calculate total nodes, max label length
        this.totalNodes = 0;
        this.maxLabelLength = 0;
        // variables for drag/drop
        this.selectedNode = null;
        this.draggingNode = null;
        // panning variables
        this.panSpeed = 200;
        this.panBoundary = 20; // Within 20px from edges will pan when dragging.
        // Misc. variables
        this.i = 0;
        this.duration = 750;
        this.root = null;

        // size of the diagram
        this.viewerWidth = $("#tree-container").width();
        this.viewerHeight = $(document).height();

        var tree = this.tree= d3.layout.tree()
            .size([this.viewerHeight, this.viewerWidth]);

        // define a d3 diagonal projection for use by the node paths later on.
        this.diagonal = d3.svg.diagonal()
            .projection(function(d) {
                return [d.y, d.x];
            });


        var that = this;
        // Call visit function to establish maxLabelLength
        this.visit(treeData, function(d) {
            that.totalNodes++;
            that.maxLabelLength = Math.max(d.name.length, that.maxLabelLength);

        }, function(d) {
            return d.children && d.children.length > 0 ? d.children : null;
        });
        console.log(this.totalNodes);




        // define the zoomListener which calls the zoom function on the "zoom" event constrained within the scaleExtents
        this.zoomListener = d3.behavior.zoom().scaleExtent([0.1, 3]).on("zoom", this.zoom);



        // define the baseSvg, attaching a class for styling and the zoomListener
        this.baseSvg = d3.select("#tree-container").append("svg")
            .attr("width", this.viewerWidth)
            .attr("height", this.viewerHeight)
            .call(this.zoomListener);


        // Helper functions for collapsing and expanding nodes.



        // Append a group which holds all nodes and which the zoom Listener can act upon.
        this.svgGroup = this.baseSvg.append("g");

        // Define the root
        this.root = treeData;
        this.root.x0 = this.viewerHeight / 2;
        this.root.y0 = 0;
        var theTree = this;
        // Collapse all children of roots children before rendering.
        this.root.children.forEach(function(child){
            theTree.collapse(child);
        });
        this.sortTree();
        // Layout the tree initially and center on the root node.
        this.update(this.root);
        this.centerNode(this.root);
    };
    // A recursive helper function for performing some setup by walking through all nodes

    Tree.prototype.visit = function (parent, visitFn, childrenFn) {
        if (!parent) return;

        visitFn(parent);

        var children = childrenFn(parent);
        if (children) {
            var count = children.length;
            for (var i = 0; i < count; i++) {
                this.visit(children[i], visitFn, childrenFn);
            }
        }
    };

    Tree.prototype.collapse = function(d) {
        if (d.children) {
            d._children = d.children;
            d._children.forEach(this.collapse);
            d.children = null;
        }
    };

    Tree.prototype.expand = function(d) {
        if (d._children) {
            d.children = d._children;
            d.children.forEach(expand);
            d._children = null;
        }
    };



    // sort the tree according to the node names

    Tree.prototype.sortTree= function() {
        this.tree.sort(function(a, b) {
            return b.name.toLowerCase() < a.name.toLowerCase() ? 1 : -1;
        });
    };
    // Sort the tree initially incase the JSON isn't in a sorted order.


    // TODO: Pan function, can be better implemented.

    Tree.prototype.pan= function(domNode, direction) {
        var speed = this.panSpeed;
        if (panTimer) {
            clearTimeout(panTimer);
            var translateCoords = d3.transform(this.svgGroup.attr("transform"));
            if (direction == 'left' || direction == 'right') {
                var translateX = direction == 'left' ? translateCoords.translate[0] + speed : translateCoords.translate[0] - speed;
                var translateY = this.translateCoords.translate[1];
            } else if (direction == 'up' || direction == 'down') {
                this.translateX = translateCoords.translate[0];
                this.translateY = direction == 'up' ? translateCoords.translate[1] + speed : translateCoords.translate[1] - speed;
            }
            var scaleX = translateCoords.scale[0];
            var scaleY = translateCoords.scale[1];
             var scale = this.zoomListener.scale();
            this.svgGroup.transition().attr("transform", "translate(" + translateX + "," + translateY + ")scale(" + scale + ")");
            d3.select(domNode).select('g.node').attr("transform", "translate(" + translateX + "," + translateY + ")");
            this.zoomListener.scale(this.zoomListener.scale());
            zoomListener.translate([translateX, translateY]);
            panTimer = setTimeout(function() {
                pan(domNode, speed, direction);
            }, 50);
        }
    };

    // Define the zoom function for the zoomable tree

    Tree.prototype.zoom = function() {
        data.tree.svgGroup.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    };

    // Function to center node when clicked/dropped so node doesn't get lost when collapsing/moving with large amount of children.

     Tree.prototype.centerNode = function(source) {
         var scale = this.zoomListener.scale();
         var x = -source.y0;
         var y = -source.x0;
         x =x * scale + this.viewerWidth / 2;
          y = y * scale + this.viewerHeight / 2;
        d3.select('g').transition()
            .duration(this.duration)
            .attr("transform", "translate(" + x + "," + y + ")scale(" + scale + ")");
         this.zoomListener.scale(scale);
         this.zoomListener.translate([x,y]);
    };

    // Toggle children function

    Tree.prototype.toggleChildren = function(d) {
        if(d.root)return d;

        if (d.children) {
            d._children = d.children;
            d.children = null;
        } else if (d._children) {
            d.children = d._children;
            d._children = null;
        }
        return d;
    };

    // Toggle children on click.

    Tree.prototype.click = function (d) {
        if (d3.event.defaultPrevented) return; // click suppressed
        $.observable(data).setProperty("selectedNode", null);
        $.observable(data).setProperty("selectedNode", d);

        d = data.tree.toggleChildren(d);
        data.tree.update(d);
        data.tree.centerNode(d);
    };

    Tree.prototype.clickLink  = function (d) {
        d = d.target;
        console.log(d);
        //if (d3.event.defaultPrevented) return; // click suppressed
        d = data.tree.toggleChildren(d);
        data.tree.update(d);
        data.tree.centerNode(d);
    };

    Tree.prototype.update = function (source) {
        console.log(this.treeData);
        // Compute the new height, function counts total children of root node and sets tree height accordingly.
        // This prevents the layout looking squashed when new nodes are made visible or looking sparse when nodes are removed
        // This makes the layout more consistent.
        var levelWidth = [1];
        var childCount = function(level, n) {

            if (n.children && n.children.length > 0) {
                if (levelWidth.length <= level + 1) levelWidth.push(0);

                levelWidth[level + 1] += n.children.length;
                n.children.forEach(function(d) {
                    childCount(level + 1, d);
                });
            }
        };
        childCount(0, this.root);
        var newHeight = d3.max(levelWidth) * 25; // 25 pixels per line
        this.tree = this.tree.size([newHeight, this.viewerWidth]);

        // Compute the new tree layout.
        var nodes = this.tree.nodes(this.root).reverse(),
            links = this.tree.links(nodes);
        var that = this;

        // Set widths between levels based on maxLabelLength.
        nodes.forEach(function(d) {
            d.y = (d.depth * (that.maxLabelLength * 10)); //maxLabelLength * 10px
            // alternatively to keep a fixed scale one can set a fixed depth per level
            // Normalize for fixed-depth by commenting out below line
            // d.y = (d.depth * 500); //500px per level.
        });

        // Update the nodes…
        this.node = this.svgGroup.selectAll("g.node")
            .data(nodes, function(d) {
                return d.id || (d.id = ++that.i);
            });

        // Enter any new nodes at the parent's previous position.
        var nodeEnter = this.node.enter().append("g")
            .attr("class", "node")
            .attr("transform", function(d) {
                return "translate(" + source.y0 + "," + source.x0 + ")";
            })
            .on('click', this.click);

        nodeEnter.append("circle")
            .attr('class', 'nodeCircle')
            .attr("r", 0)
            .style("fill", function(d) {
                return d.locked ? "red" : "#fff";
            });

        nodeEnter.append("text")
            .attr("x", function(d) {
                return d.children || d._children ? -10 : 10;
            })
            .attr("dy", ".35em")
            .attr('class', 'nodeText')
            .attr("text-anchor", function(d) {
                return d.children || d._children ? "end" : "start";
            })
            .text(function(d) {
                return d.name;
            }).
        attr("data-real-value", function (d) {
            return d.value;
        })
            .style("fill-opacity", 0);

        // phantom node to give us mouseover in a radius around it
        nodeEnter.append("circle")
            .attr('class', 'ghostCircle')
            .attr("r", 30)
            .attr("opacity", 0.2) // change this to zero to hide the target area
            .style("fill", "red")
            .attr('pointer-events', 'mouseover')
            .on("mouseover", function(node) {
                overCircle(node);
            })
            .on("mouseout", function(node) {
                outCircle(node);
            });

        // Update the text to reflect whether node has children or not.
        this.node.select('text')
            .attr("x", function(d) {
                return d.children || d._children ? -10 : 10;
            })
            .attr("text-anchor", function(d) {
                return d.children || d._children ? "end" : "start";
            })
            .text(function(d) {
                return d.name;
            });

        // Change the circle fill depending on whether it has children and is collapsed
        this.node.select("circle.nodeCircle")
            .attr("r", 4.5)
            .style("fill", function(d) {
                if(d.children || d._children) return "blue";
                return d.locked ? "red" : "#fff";
            });

        // Transition nodes to their new position.
        var nodeUpdate = this.node.transition()
            .duration(this.duration)
            .attr("transform", function(d) {
                return "translate(" + d.y + "," + d.x + ")";
            });

        // Fade the text in
        nodeUpdate.select("text")
            .style("fill-opacity", 1);

        // Transition exiting nodes to the parent's new position.
        var nodeExit = this.node.exit().transition()
            .duration(this.duration)
            .attr("transform", function(d) {
                return "translate(" + source.y + "," + source.x + ")";
            })
            .remove();

        nodeExit.select("circle")
            .attr("r", 0);

        nodeExit.select("text")
            .style("fill-opacity", 0);

        // Update the links…
        var link = this.svgGroup.selectAll("path.link")
            .data(links, function(d) {
                return d.target.id;
            });
        // Enter any new links at the parent's previous position.
        link.enter().insert("path", "g")
            .attr("class", "link")
            .attr("d", function(d) {
                var o = {
                    x: source.x0,
                    y: source.y0
                };
                return that.diagonal({
                    source: o,
                    target: o
                });
            })
            .on('click', this.clickLink);

        // Transition links to their new position.
        link.transition()
            .duration(that.duration)
            .attr("d", that.diagonal);

        // Transition exiting nodes to the parent's new position.
        link.exit().transition()
            .duration(that.duration)
            .attr("d", function(d) {
                var o = {
                    x: source.x,
                    y: source.y
                };
                return data.tree.diagonal({
                    source: o,
                    target: o
                });
            })
            .remove();

        // Stash the old positions for transition.
        nodes.forEach(function(d) {
            d.x0 = d.x;
            d.y0 = d.y;
        });
    };

});


