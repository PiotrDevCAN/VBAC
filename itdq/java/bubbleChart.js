/**
 *
 */



var BubbleChart = {


init : function() {
	console.log('init called');

	this.margin = {top: 20, right: 30, bottom: 30, left: 40};
	this.width = parseInt(d3.select('#bubbleChart').style('width'), 10);
	this.width = this.width - this.margin.left - this.margin.right;
	this.height = 800 - this.margin.top - this.margin.bottom;

	this.padding = 1.5; // separation between same-color nodes
	this.clusterPadding = 6; // separation between different-color nodes
	this.maxRadius = 40;
	this.minRadius = 20;

},

displayBubbleChart : function (fileName){
	console.log(this);
	BubbleChart.init();
	console.log(this.node);
	var legendMap = {};
	d3.csv(fileName, function(data) {
	  //calculate teh maximum groups present
	  m = d3.max(data, function(d){
		  legendMap[d.group] = d.group;
		  return d.group});

	  //create the color categories
	  color = d3.scale.category10()
	  .domain(d3.range(m));
	  //make the clusters array for each cluster for each group
	  clusters = new Array(m);
	  dataset = data.map(function(d) {
	    //find the radius entered in the csv
	  var r = parseInt(d.radius);

	    var dta = {
	      cluster: d.group,//group
	      name: d.name,//label
	      radius: r,//radius
	      x: Math.cos(d.group / m * 2 * Math.PI) * 100 + this.width / 2 + Math.random(),
	      y: Math.sin(d.group / m * 2 * Math.PI) * 100 + this.height / 2 + Math.random()
	    };
	    //add the one off the node inside the cluster
	    if (!clusters[d.group] || (d.radius > clusters[d.group].radius)) clusters[d.group] = dta;
	    return dta;
	  });
	  //after mapping use that to make the graph
	  BubbleChart.makeGraph(dataset);
	});

},


	//this will make the graph from nodes
makeGraph : function(nodes) {
	  var force = d3.layout.force()
	    .nodes(nodes)
	    .size([this.width, this.height])
	    .gravity(.02)
	    .charge(0)
	    .on("tick", tick)
	    .start();

	  var svg = d3.select("body").append("svg")
	    .attr("width", this.width)
	    .attr("height", this.height);

	  var node = svg.selectAll("circle")
	    .data(nodes)


	    .enter().append("g").call(force.drag);
	  //addcircle to the group
	  node.append("circle")

	    .style("fill", function(d) {
	    	console.log(d.cluster);
	    	console.log(color(d.cluster));
	    	console.log(d.name);

	    	return color(d.cluster); })
	    .attr("r", function(d) { return d.radius  })

	    //add text to the group
	  node.append("text")
	    .text(function(d) {
	      return d.name;
	    })
	    .attr("dx", -10)
	    .attr("dy", ".35em")
	    .text(function(d) {
	      return d.name
	    })
	    .style("stroke", "none");


	  function tick(e) {
	    node.each(cluster(10 * e.alpha * e.alpha))
	      .each(collide(.5))
	      //.attr("transform", functon(d) {});
	      .attr("transform", function(d) {
	        var k = "translate(" + d.x + "," + d.y + ")";
	        return k;
	      })

	  }

	  // Move d to be adjacent to the cluster node.
	  function cluster(alpha) {
	    return function(d) {
	      var cluster = clusters[d.cluster];
	      if (cluster === d) return;
	      var x = d.x - cluster.x,
	        y = d.y - cluster.y,
	        l = Math.sqrt(x * x + y * y),
	        r = d.radius + cluster.radius;
	      if (l != r) {
	        l = (l - r) / l * alpha;
	        d.x -= x *= l;
	        d.y -= y *= l;
	        cluster.x += x;
	        cluster.y += y;
	      }
	    };
	  }


	  // Resolves collisions between d and all other circles.
	  function collide(alpha) {
	    var quadtree = d3.geom.quadtree(nodes);
	    return function(d) {
	      var r = d.radius + BubbleChart.maxRadius + Math.max(BubbleChart.padding, BubbleChart.clusterPadding),
	        nx1 = d.x - r,
	        nx2 = d.x + r,
	        ny1 = d.y - r,
	        ny2 = d.y + r;
	      quadtree.visit(function(quad, x1, y1, x2, y2) {
	        if (quad.point && (quad.point !== d)) {
	          var x = d.x - quad.point.x,
	            y = d.y - quad.point.y,
	            l = Math.sqrt(x * x + y * y),
	            r = d.radius + quad.point.radius + (d.cluster === quad.point.cluster ? BubbleChart.padding : BubbleChart.clusterPadding);
	          if (l < r) {
	            l = (l - r) / l * alpha;
	            d.x -= x *= l;
	            d.y -= y *= l;
	            quad.point.x += x;
	            quad.point.y += y;
	          }
	        }
	        return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
	      });
	    };
	  }
	},

legend : function(legendMapJson){

		console.log(legendMap);
		var legendMap = JSON.parse(legendMapJson);

		var numberOfGroups = legendMap.length;

		console.log(numberOfGroups);

		color = d3.scale.category10()
		  .domain(d3.range(legendMap.length));

		  // Dimensions of legend item: width, height, spacing, radius of rounded rect.
		  var li = {
		    w: 85, h: 40, s: 3, r: 3
		  };

		  var legend = d3.select("#legend").append("svg:svg")
		      .attr("width", li.w)
		      .attr("height", d3.keys(legendMap).length * (li.h + li.s));

		  var g = legend.selectAll("g")
		      .data(d3.entries(legendMap))
		      .enter().append("svg:g")
		      .attr("transform", function(d, i) {
		              return "translate(0," + i * (li.h + li.s) + ")";
		           });

		  g.append("svg:rect")
		      .attr("rx", li.r)
		      .attr("ry", li.r)
		      .attr("width", li.w)
		      .attr("height", li.h)
		      .style("fill", function(d) {
		    	  console.log(d.key);
		    	  console.log(d.value);
		    	  console.log(color(d.key));
		    	  return color(d.key); });

		  g.append("svg:text")
		      .attr("x", li.w / 2)
		      .attr("y", li.h / 2)
		      .attr("dy", "0.35em")
		      .attr("text-anchor", "middle")
		      .style("fill", function(d) { return d3plus.color.text(color(d.key)); })
		      .text(function(d) {
		    	  	console.log(d);
		    	  	return d.value; });
		}
	}