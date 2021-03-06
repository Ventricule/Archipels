	project.view.zoom = .5 ;
	project.view.center = pcenter ;
	var pmouse, pcenter, ppoint, ctr;
	
	$('body, html').mousewheel(function(event) {
		project.view.zoom = Math.max( ( project.view.zoom + event.deltaY * .01 ) , .01 );
	});
	
	var hitOptions = {
		segments: true,
		stroke: true,
		fill: true,
		tolerance: 5
	};

	// Ported from original Metaball script by SATO Hiroyuki
	// http://park12.wakwak.com/~shp/lc/et/en_aics_script.html
	project.currentStyle = {
		fillColor: 'black'
	};
	
	
	var elements = $(".elmts").html();
	var ballPositions = $.parseJSON(elements);

	var handle_len_rate = 2.5;
	var circlePaths = [];
	var reliefPaths = [];
	var radius = 50;
	for (var i = 0, l = ballPositions.length; i < l; i++) {
		var circlePath = new Path.Circle({
			center: ballPositions[i],
			radius: 50
		});
		circlePaths.push(circlePath);
	}

	var layerA = new Layer({
		children: circlePaths,
		position: view.center
	});

	
	//Drag elements
	function onMouseDown(event) {
		path_element = path_pan = null;
		var hitResult = layerA.hitTest(event.point, hitOptions);
		if ( !hitResult ) {
			path_pan = new Point();
			path_pan.add(event.point);
		} else {
			path_element = hitResult.item;
		}
	}

	function onMouseDrag(event) {
		if (path_element) {
			path_element.position += event.delta;
			generateConnections(circlePaths);
		} else if ( path_pan ) {
			path_pan.add(event.point);
			var des = traslladar (event.downPoint , event.point);
			paper.project.view.center = des;
		}
	}

	function onMouseUp(event) {
		generateRelief(circlePaths)
	}
	
	
	//----------------------- ZOOM / PAN -------------------------
	
	
	function traslladar(a,b){
		var center = paper.project.view.center;
		var desX = (a.x - b.x);
		var desY= (a.y - b.y);

		var newCenter = [center.x + desX , center.y + desY];
		return newCenter;
	}

            
	//------------------------------------------------------------


	var connections = new Group();
	function generateConnections(paths) {
		// Remove the last connection paths:
		connections.children = [];

		for (var i = 0, l = paths.length; i < l; i++) {
			for (var j = i - 1; j >= 0; j--) {
				var path = metaball(paths[i], paths[j], 0.5, handle_len_rate, 300);
				if (path) {
					connections.appendTop(path);
					//path.removeOnMove();
				}
			}
		}
	}


	var layerB = new Layer();
	function generateRelief(paths) {
		reliefPaths = [];
		layerB.remove();
		for (var i = 0, l = paths.length; i < l; i++) {
			var circlePath = new Path.Circle({
				center: paths[i].position,
				radius: 60,
				fillColor: 'grey'
			});
			reliefPaths.push(circlePath);
		}
		layerB = new Layer({
			children: reliefPaths,
			position: view.center
		});
		
		var merged = merge(paths);

		merged[0].set({
		  fillColor: "yellow",
		  opacity: 0.5
		});
		//layerA.insertAbove(layerB)
	}

	generateConnections(circlePaths);

	// ---------------------------------------------
	function metaball(ball1, ball2, v, handle_len_rate, maxDistance) {
		var center1 = ball1.position;
		var center2 = ball2.position;
		var radius1 = ball1.bounds.width / 2;
		var radius2 = ball2.bounds.width / 2;
		var pi2 = Math.PI / 2;
		var d = center1.getDistance(center2);
		var u1, u2;

		if (radius1 == 0 || radius2 == 0)
			return;

		if (d > maxDistance || d <= Math.abs(radius1 - radius2)) {
			return;
		} else if (d < radius1 + radius2) { // case circles are overlapping
			u1 = Math.acos((radius1 * radius1 + d * d - radius2 * radius2) /
					(2 * radius1 * d));
			u2 = Math.acos((radius2 * radius2 + d * d - radius1 * radius1) /
					(2 * radius2 * d));
		} else {
			u1 = 0;
			u2 = 0;
		}

		var angle1 = (center2 - center1).getAngleInRadians();
		var angle2 = Math.acos((radius1 - radius2) / d);
		var angle1a = angle1 + u1 + (angle2 - u1) * v;
		var angle1b = angle1 - u1 - (angle2 - u1) * v;
		var angle2a = angle1 + Math.PI - u2 - (Math.PI - u2 - angle2) * v;
		var angle2b = angle1 - Math.PI + u2 + (Math.PI - u2 - angle2) * v;
		var p1a = center1 + getVector(angle1a, radius1);
		var p1b = center1 + getVector(angle1b, radius1);
		var p2a = center2 + getVector(angle2a, radius2);
		var p2b = center2 + getVector(angle2b, radius2);

		// define handle length by the distance between
		// both ends of the curve to draw
		var totalRadius = (radius1 + radius2);
		var d2 = Math.min(v * handle_len_rate, (p1a - p2a).length / totalRadius);

		// case circles are overlapping:
		d2 *= Math.min(1, d * 2 / (radius1 + radius2));

		radius1 *= d2;
		radius2 *= d2;

		var path = new Path({
			segments: [p1a, p2a, p2b, p1b],
			style: ball1.style,
			closed: true
		});
		var segments = path.segments;
		segments[0].handleOut = getVector(angle1a - pi2, radius1);
		segments[1].handleIn = getVector(angle2a + pi2, radius2);
		segments[2].handleOut = getVector(angle2b - pi2, radius2);
		segments[3].handleIn = getVector(angle1b + pi2, radius1);
		return path;
	}

	// ------------------------------------------------
	function getVector(radians, length) {
		return new Point({
			// Convert radians to degrees:
			angle: radians * 180 / Math.PI,
			length: length
		});
	}
	
	// ------------------------------------------------ 
	// MERGING SYSTEM
	// ------------------------------------------------

	var c, circles, merge, mergeOne, overlaps, x, _i, _len, _ref;

	overlaps = function(path, other) {
	  return !(path.getIntersections(other).length === 0);
	};

	mergeOne = function(path, others) {
	  var i, merged, other, union, _i, _len, _ref;
	  for (i = _i = 0, _len = others.length; _i < _len; i = ++_i) {
		other = others[i];
		if (overlaps(path, other)) {
		  union = path.unite(other);
		  merged = mergeOne(union, others.slice(i + 1));
		  return (_ref = others.slice(0, i)).concat.apply(_ref, merged);
		}
	  }
	  return others.concat(path);
	};

	merge = function(paths) {
	  var path, result, _i, _len;
	  result = [];
	  for (_i = 0, _len = paths.length; _i < _len; _i++) {
		path = paths[_i];
		result = mergeOne(path, result);
	  }
	  return result;
	};
