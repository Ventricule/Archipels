project.view.zoom = .5;
var pmouse, pcenter, ppoint, ctr;
var instance = [];

var hitOptions = {
	segments: true,
	stroke: true,
	fill: true,
	tolerance: 5
};

var elements_list;

jQuery.get('elements_list_json.txt', function (data) {

	var icone = ['immateriel', 'volume', 'plan'];
	var icones_loaded_num = 0;
	icone.forEach(function (entry) {
		//console.log(entry);
		project.importSVG('icones/' + entry + '2.svg', function (item) {
			icone[entry] = new Symbol(item);
			item.remove();
			icone_loaded();
		});
	});

	function icone_loaded() {
		icones_loaded_num++;
		if (icones_loaded_num >= icone.length) {
			//console.log(icone);
			elements_list = JSON.parse(data);
			//console.log(elements_list);
			show_lines();
			for (var i = 0, l = elements_list.length; i < l; i++) {
				var x = parseInt(elements_list[i]['x']) || 0;
				var y = parseInt(elements_list[i]['y']) || 0;
				var class_a = elements_list[i]['class_a'];

				instance[i] = icone[class_a].place([x, y]);
				instance[i].scale(6);
				//instance.position = Point( x, y );
			}
			//select( 'volume' );
			init_icones();
		}
	}
});

function init_icones() {
	for (var i = 0; i < instance.length; i++) {
		instance[i].onMouseEnter = function () {
			this.scale(1.5);
			console.log('enter');
		}

		instance[i].onMouseLeave = function () {
			this.scale(0.75);
			console.log('leave');
		}
	}
}

function select(class_a) {
	var selected = [];
	for (var i = 0; i < elements_list.length; i++) {
		if (class_a == elements_list[i]["class_a"]) {
			instance[i].scale(2);
		}
	}
}

function show_lines() {
	var lines = [];
	for (var i = 0; i < elements_list.length; i++) {
		var tables = elements_list[i]["tables"];
		for (var j = 0; j < tables.length; j++) {
			var table = elements_list[i]["tables"][j];
			var point = [elements_list[i]['x'], elements_list[i]['y']];
			if (lines[table] === undefined) {
				lines[table] = [];
			}
			lines[table].push(point);
		}
	}

	var lines_array = [];
	for (var i = 0; i < lines.length; i++) {
		if (lines[i]) {
			var values;
			values = {
				coord: lines[i],
				table: i
			}
			lines_array.push(values);
		}
	}



	for (var i = 0; i < lines_array.length; i++) {
		var coordonnees = lines_array[i]['coord'];
		var table = lines_array[i]['table'];
		//console.log(coordonnees);

		/*console.log( coordonnees );
		coordonnees.sort(function(a, b){
		  return a[0] - b[0];
		});
		console.log( coordonnees );*/

		var path = new Path();
		path.strokeColor = {
			hue: Math.random() * 360,
			saturation: 1,
			brightness: 1
		};
		path.strokeWidth = 5;
		//console.log( table )
		for (var j = 0; j < coordonnees.length; j++) {
			path.add(new Point(parseFloat(coordonnees[j][0]), parseFloat(coordonnees[j][1])));
		}
		//path.smooth();

	}


}

function onMouseMove(event) {
	console.log(instance[1]);
}


//----------------------- ZOOM / PAN -------------------------

function traslladar(a, b) {
	var center = paper.project.view.center;
	var desX = (a.x - b.x);
	var desY = (a.y - b.y);

	var newCenter = [center.x + desX, center.y + desY];
	return newCenter;
}

function onMouseDown(event) {
	path_pan = null;
	var hitResult = project.hitTest(event.point, hitOptions);
	if (!hitResult) {
		path_pan = new Point(event.point);
		//path_pan.add(event.point);
	}
}

function onMouseDrag(event) {
	if (path_pan) {
		path_pan.add(event.point);
		var des = traslladar(event.downPoint, event.point);
		paper.project.view.center = des;
	}
}
$('body, html').mousewheel(function (event) {
	project.view.zoom = Math.max((project.view.zoom + event.deltaY * .01), .01);
	for (var i = 0; i < instance.length; i++) {
		instance[i].scale(1 - (event.deltaY * .025));
	}
});